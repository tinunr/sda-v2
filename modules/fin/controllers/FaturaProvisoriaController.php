<?php

namespace app\modules\fin\controllers;

use yii\helpers\Url;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\models\Model;
use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\FaturaProvisoriaSearch;
use app\modules\fin\models\FaturaProvisoriaItem;
use app\modules\fin\models\FaturaProvisoriaRegimeItem;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\modules\fin\models\Recebimento;
use app\modules\fin\models\DocumentoPagamento;
use app\modules\fin\models\Pagamento;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaItem;
use app\modules\fin\models\Receita;
use app\modules\fin\models\BaseDespacho;
use app\modules\fin\models\BaseDespachoItem;
use app\modules\dsp\models\Item;
use app\models\Mes;
use app\models\Module;
use app\models\Parameter;
use kartik\mpdf\Pdf;
use yii\base\Exception;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * FaturaProvisoriaController implements the CRUD actions for FaturaProvisoria model.
 */
class FaturaProvisoriaController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['honorario-fp', 'list-processo', 'get-despesa-items', 'update-receita', 'faturas-json'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'undo', 'report', 'relatorio', 'view-pdf', 'send-unsend', 'honorario','valor-adever'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->AuthService->permissiomHandler() === true;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'undo' => ['post'],
                    'send' => ['post'],
                    'unsend' => ['post'],
                    'update-receita' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all FaturaProvisoria models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FaturaProvisoriaSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all FaturaProvisoria models.
     * @return mixed
     */
    public function actionReport()
    {
        $searchModel = new FaturaProvisoriaSearch();
        $dataProvider = $searchModel->report(Yii::$app->request->queryParams);

        return $this->render('relatorio', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all FaturaProvisoria models.
     * @return mixed
     */
    public function actionValorAdever()
    {
        $searchModel = new FaturaProvisoriaSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->searchValorAdever(Yii::$app->request->queryParams);

        return $this->render('fp-valor-adever', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FaturaProvisoria model.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        \app\modules\fin\services\FaturaProvisoriaService::atualizarValorRecebido($id);
        $modelsFaturaProvisoriaItem = $model->faturaProvisoriaItem;
        return $this->render('view', [
            'model' => $model,
            'modelsFaturaProvisoriaItem' => $modelsFaturaProvisoriaItem,
        ]);
    }


    /**
     * Creates a new FaturaProvisoria model.p
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FaturaProvisoria;
        $model->data = date('Y-m-d');
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::FATURA_PROVISORIA_ID);
        $model->numero = $documentoNumero->getNexNumber();
        $model->taxa_comunicaco =  Parameter::getValue(Module::FINANCEIRO, 'TAXA_COMONICACAO');;
        $model->impresso = Parameter::getValue(Module::FINANCEIRO, 'IMPRESSO');
        $model->nord = null;
        $modelsFaturaProvisoriaItem = [new FaturaProvisoriaItem];
        if ($model->load(Yii::$app->request->post())) {

            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);
            $modelsFaturaProvisoriaItem = Model::createMultiple(FaturaProvisoriaItem::classname());
            Model::loadMultiple($modelsFaturaProvisoriaItem, Yii::$app->request->post());
            // validate all models
            $valid = $model->validate();
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {
                        $totalRecebimento = 0;
                        $i = 0;
                        $j = 0;
                        foreach ($modelsFaturaProvisoriaItem as $modelFaturaProvisoriaItem) {
                            $modelFaturaProvisoriaItem->dsp_fatura_provisoria_id = $model->id;
                            $totalRecebimento = $totalRecebimento + $modelFaturaProvisoriaItem->valor;
                            if (!($flag = $modelFaturaProvisoriaItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
            'modelsFaturaProvisoriaItem' => (empty($modelsFaturaProvisoriaItem)) ? [new FaturaProvisoriaItem] : $modelsFaturaProvisoriaItem,
        ]);
    }

    /**
     * Updates an existing FaturaProvisoria model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->status == FaturaProvisoria::STATUS_ANULADO || ($model->send == FaturaProvisoria::VALIDADO ||$model->send == FaturaProvisoria::ENVIADO)) {
            Yii::$app->getSession()->setFlash('error', 'ESTA FATURA JÁ ESTA  ANULADO VALIDADO OU ENVIADO AO CLIENTE POR ISSO NÃO PODE SER ATUALIZADO.');
            return $this->redirect(Yii::$app->request->referrer); 
        }

        if ($model->receita->valor_recebido > 0) {
            Yii::$app->getSession()->setFlash('warning', 'ESTA FATURA JÁ FOI RCEBIDO ANULE O RECEBIMENTO CASO PRETENTEDA CONTINUAR.');
            return $this->redirect(Yii::$app->request->referrer);
        }

        $modelsFaturaProvisoriaItem = $model->faturaProvisoriaItem;


        if ($model->load(Yii::$app->request->post())) {

            $model->bas_ano_id = substr(Yii::$app->formatter->asDate($model->data, 'Y'), -2);
            $oldIDs = ArrayHelper::map($modelsFaturaProvisoriaItem, 'id', 'id');
            $modelsFaturaProvisoriaItem = Model::createMultiple(FaturaProvisoriaItem::classname(), $modelsFaturaProvisoriaItem);
            Model::loadMultiple($modelsFaturaProvisoriaItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsFaturaProvisoriaItem, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsFaturaProvisoriaItem),
                    ActiveForm::validate($model)
                );
            }
            // validate all models
            $valid = $model->validate();
            // $valid = Model::validateMultiple($modelsFaturaProvisoriaItem) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            FaturaProvisoriaItem::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsFaturaProvisoriaItem as $modelFaturaProvisoriaItem) {
                            $modelFaturaProvisoriaItem->dsp_fatura_provisoria_id = $model->id;
                            if (!($flag = $modelFaturaProvisoriaItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
            'modelsFaturaProvisoriaItem' => (empty($modelsFaturaProvisoriaItem)) ? [new FaturaProvisoriaItem] : $modelsFaturaProvisoriaItem
        ]);
    }


    /**
     * Deletes an existing FaturaProvisoria model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionUndo($id)
    {
        $model = $this->findModel($id);
        $modelReceita = Receita::FindOne(['dsp_fataura_provisoria_id' => $model->id]);

        if ($modelReceita->valor_recebido > 0) {
            Yii::$app->getSession()->setFlash('warning', 'ESTA FATURA JÁ FOI RCEBIDO ANULE O RECEBIMENTO CASO PRETENTEDA CONTINUAR.');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            $model->status = 0;
            $modelReceita->status = 0;
            if ($model->save(false) && $modelReceita->save(false)) {

                Yii::$app->getSession()->setFlash('success', 'FATURA ANULADO COM SUCESSO.');
            } else {
                // print_r($model->errors);die();
                Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Deletes an existing FaturaProvisoria model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionSendUnsend($id)
    {
        $model = $this->findModel($id);

        if ($model->status != 0 && $model->receita->valor_recebido > 0) {
            Yii::$app->getSession()->setFlash('error', 'ESTA FATURA JÁ ESTA RECEBIDO NÃO PODE SER ANULADO.');
            return $this->redirect(Yii::$app->request->referrer);
        }
        if ($model->send == FaturaProvisoria::POR_VALIDAR) {
            $model->send = FaturaProvisoria::VALIDADO;
        } elseif($model->send == FaturaProvisoria::VALIDADO){
            $model->send = FaturaProvisoria::ENVIADO;
        }else{
			$model->send = FaturaProvisoria::POR_VALIDAR;
		}
        if ($model->save(false)) {
            Yii::$app->getSession()->setFlash('success', 'FATURA ' . ($model->send ? ' CONFERIDO' : 'NÃO CONFERIDO') . ' COM SUCESSO.');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Deletes an existing FaturaProvisoria model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionUnsend($id)
    {
        $model = $this->findModel($id);
        if ($model->status == FaturaProvisoria::STATUS_ANULADO) {
            Yii::$app->getSession()->setFlash('error', 'ESTA FATURA JÁ ESTA  ANULADO OU ENVIADO AO CLIENTE.');
            return $this->redirect(Yii::$app->request->referrer);
        }
        $model->send = FaturaProvisoria::POR_VALIDAR;

        if ($model->save(false)) {
            Yii::$app->getSession()->setFlash('success', 'FATURA DESBLOKEADO COM SUCESSO.');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }



    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateReceita($id)
    {
        if (Yii::$app->FinAutoFixs->bugApdateValorRecebido($id)) {
            Yii::$app->getSession()->setFlash('success', 'OPERAÇÃO EFETUADO COM SUCESSO');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }


    // THE CONTROLLER
    public function actionListProcesso()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = $parents[0];

                $query = new Query;
                $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                    ->from('fin_fatura_provisoria A')
                    ->where(['A.dsp_processo_id' => $id])
                    ->andWhere(['A.status' => 1])
                    ->orderBy('A.numero')
                    ->all();
                $command = $query->createCommand();
                $out = $command->queryAll();
                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }



    /**
     * Finds the FaturaProvisoria model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $perfil_id
     * @return FaturaProvisoria the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FaturaProvisoria::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionHonorarioFp($dsp_processo_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $honorario = 0;


        return $honorario;
    }

    public function actionReportactionReport($id)
    {

        $model = $this->findModel($id);
        $modelsFaturaProvisoriaItem = $model->faturaProvisoriaItem;

        $company = Yii::$app->params['company'];

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('view-pdf', [
            'model' => $model,
            'company' => $company,
            'modelsFaturaProvisoriaItem' => $modelsFaturaProvisoriaItem,
        ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'marginTop' => 0,
            'cssFile' => '@app/web/css/pdf.css',
            'marginLeft' => 25,
            'marginRight' => 25,
            'options' => ['title' => 'Relatório Processo'],
            'methods' => [

                'SetFooter' => ['<p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong><i class="fa fa-graduation-cap"></i>' . $company['adress2'] . '</strong>
                     <br><strong><i class="fa fa-graduation-cap"></i> C.P. Nº ' . $company['cp'] . ' - Tel.: ' . $company['teletone'] . ' - FAX: ' . $company['fax'] . ' - Praia, Santiago</strong>
                     </p> ||Página {PAGENO}/{nbpg}'],
            ]
        ]);
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];



        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewPdf($id)
    {
        $model = $this->findModel($id);
        $model->send = $model->send == FaturaProvisoria::POR_VALIDAR ? FaturaProvisoria::POR_VALIDAR : FaturaProvisoria::ENVIADO;
        $model->save(FALSE);

        $company = Yii::$app->params['company'];
        $content = $this->renderPartial('view-pdf', [
            'company' => $company,
            'model' => $model,
        ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@app/web/css/pdf.css',
            'marginTop' => 5,
            'marginLeft' => 5,
            'marginRight' => 5,
            'marginBottom' => 5,
            'options' => ['title' => 'Relatório Processo'],
            //   'methods' => [ 
            //       'SetHeader'=>['
            //        <div id="logo"><img src="logo.png"></div>
            //         <div id="company">
            //           <div class="name1">'.$company['name'].'</div>
            //           <div>'.$company['adress2'].'</div>
            //           <div>NIF: '.$company['nif'].'</div>
            //         </div>
            //         </div>
            //     '], 
            //       'SetFooter'=>['<p style="font-size: 60%;">'.$company['shortName'].' - C.P. Nº '.$company['cp'].' - Praia , Santigo<br>Tel: '.$company['teletone'].' - FAX: '.$company['fax'].'<br> E-mail: '.$company['email'].'
            //            </p> ||Página {PAGENO}/{nbpg}'],
            //   ]
        ]);
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];


        // return the pdf output as per the destination setting
        return $pdf->render();
    }



    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetDespesaItems($id, $dsp_person_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = [];

        if (($itemsData = Yii::$app->FinQuery->listDespesaFaturaItem($id, $dsp_person_id)) != null) {
            foreach ($itemsData as $key => $item) {
                if ($item['id'] > 0) {
                    $data[$item['id']] = [
                        'dsp_item_id' => $item['id'],
                        'descricao' => $item['descricao'],
                        'valor' => $item['valor'],
                    ];
                }
            }
        }
        if (empty($data)) {
            return ['error' => 'NEHUM ITEM PARA APRESENTAR NESTE PROCESSO'];
        } else {
            return [
                'item' => $data,
            ];
        }
    }

    /**
     * Lists all FaturaProvisoria models.
     * @return mixed
     */
    public function actionHonorario()
    {
        $searchModel = new FaturaProvisoriaSearch();
        $searchModel->beginDate = date('Y-m') . '-01';
        $dataProvider = $searchModel->honorario(Yii::$app->request->queryParams);

        return $this->render('honorario', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all Person models.
     * @return mixed
     */
    public function actionFaturasJson()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $currentMonth = date('m');
        $currenYear = date('Y');
        $labelsMonth = [];
        $dataNFaturaP = [];
        $valorHonorario = [];
        for ($i = 1; $i <= $currentMonth; $i++) {
            $labelsMonth[] = Mes::findOne($i)->descricao;
        }

        $n_fatura_provisoria = (new \yii\db\Query())
            ->select([
                'count' => 'COUNT(A.id)', 'ano' => 'YEAR(A.data)', 'mes' => 'MONTH(A.data)'
            ])
            ->from(['A' => 'fin_fatura_provisoria'])
            ->where(['A.status' => 1])
            ->andWhere(['YEAR(A.data)' => $currenYear])
            ->groupBy(['YEAR(A.data)', 'MONTH(A.data)'])
            ->orderby('MONTH(A.data)')
            ->all();
        foreach ($n_fatura_provisoria as $key => $value) {
            $dataNFaturaP[] = $value['count'];
        }

        $valor_honorario = (new \yii\db\Query())
            ->select([
                'sum' => 'SUM(A.valor)', 'ano' => 'YEAR(B.data)', 'mes' => 'MONTH(B.data)'
            ])
            ->from(['A' => 'fin_fatura_provisoria_item'])
            ->leftJoin(['B' => 'fin_fatura_provisoria'], 'B.id = A.dsp_fatura_provisoria_id')
            ->where(['B.status' => 1])
            ->andWhere(['A.dsp_item_id' => 1002])
            ->andWhere(['YEAR(B.data)' => $currenYear])
            ->groupBy(['YEAR(B.data)', 'MONTH(B.data)'])
            ->orderby('MONTH(B.data)')
            ->all();


        $valor_honorario_fd_s2 = (new \yii\db\Query())
            ->select([
                'sum' => 'SUM(A.valor)', 'ano' => 'YEAR(B.data)', 'mes' => 'MONTH(B.data)'
            ])
            ->from(['A' => 'fin_fatura_definitiva_item'])
            ->leftJoin(['B' => 'fin_fatura_definitiva'], 'B.id = A.fin_fatura_definitiva_id')
            ->where(['B.status' => 1])
            ->where(['B.fin_fatura_definitiva_serie' => 2])
            ->andWhere(['A.dsp_item_id' => 1002])
            ->andWhere(['YEAR(B.data)' => $currenYear])
            ->groupBy(['YEAR(B.data)', 'MONTH(B.data)'])
            ->orderby('MONTH(B.data)')
            ->all();


        $data = array_merge($valor_honorario,  $valor_honorario_fd_s2);
        foreach ($data as $key => $value) {
            $valorHonorario[] = $value['sum'];
        }
        return [
            'chartData' => [
                'labels' => $labelsMonth,
                'dataNFaturaP' => $dataNFaturaP,
                'valorHonorario' => $valorHonorario
            ]
        ];
    }
}
