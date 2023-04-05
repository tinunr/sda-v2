<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\models\Model;
use app\modules\fin\models\FaturaProforma;
use app\modules\fin\models\FaturaProformaSearch;
use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\FaturaProformaItem;
use app\modules\fin\models\FaturaProvisoriaItem;
use app\modules\fin\models\Receita;
use kartik\mpdf\Pdf;
use yii\base\Exception;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

/**
 * FaturaProformaController implements the CRUD actions for FaturaProforma model.
 */
class FaturaProformaController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'undo', 'view-pdf', 'send-unsend', 'create-fatura-provisoria'],
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
     * Lists all FaturaProforma models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FaturaProformaSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all FaturaProforma models.
     * @return mixed
     */
    public function actionReport()
    {
        $searchModel = new FaturaProformaSearch();
        $dataProvider = $searchModel->report(Yii::$app->request->queryParams);

        return $this->render('relatorio', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FaturaProforma model.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $modelsFaturaProformaItem = $model->faturaProformaItem;
        return $this->render('view', [
            'model' => $model,
            'modelsFaturaProformaItem' => $modelsFaturaProformaItem,
        ]);
    }


    /**
     * Creates a new FaturaProforma model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // $data_id = substr(date('Y'),-2);
        $model = new FaturaProforma;
        $model->data = date('Y-m-d');
        $model->taxa_comunicaco = 200;

        $model->nord = null;
        $modelsFaturaProformaItem = [new FaturaProformaItem];
        //$faturaProformaRegimeItem = new FaturaProformaRegimeItem();
        if ($model->load(Yii::$app->request->post())) {

            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);
            $modelsFaturaProformaItem = Model::createMultiple(FaturaProformaItem::classname());
            Model::loadMultiple($modelsFaturaProformaItem, Yii::$app->request->post());
            #print_r($modelsFaturaProformaItem);die();
            // validate all models
            $valid = $model->validate();
            if ($valid) {

                $transaction = \Yii::$app->db->beginTransaction();

                try {

                    if ($flag = $model->save()) {
                        foreach ($modelsFaturaProformaItem as $modelFaturaProformaItem) {
                            $modelFaturaProformaItem->dsp_fatura_proforma_id = $model->id;
                            if (!($flag = $modelFaturaProformaItem->save(false))) {
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
            'modelsFaturaProformaItem' => (empty($modelsFaturaProformaItem)) ? [new FaturaProformaItem] : $modelsFaturaProformaItem,

        ]);
    }

    /**
     * Updates an existing FaturaProforma model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->status == FaturaProforma::STATUS_ANULADO || $model->send == FaturaProforma::VALIDADO) {
            Yii::$app->getSession()->setFlash('error', 'ESTA FATURA JÁ ESTA  ANULADO OU ENVIADO AO CLIENTE POR ISSO NÃO PODE SER ATUALIZADO.');
            return $this->redirect(Yii::$app->request->referrer);
            // return $this->redirect(['view', 'id' => $model->id]);
            # code...
        }



        $modelsFaturaProformaItem = $model->faturaProformaItem;


        if ($model->load(Yii::$app->request->post())) {

            $model->bas_ano_id = substr(Yii::$app->formatter->asDate($model->data, 'Y'), -2);
            $oldIDs = ArrayHelper::map($modelsFaturaProformaItem, 'id', 'id');
            $modelsFaturaProformaItem = Model::createMultiple(FaturaProformaItem::classname(), $modelsFaturaProformaItem);
            Model::loadMultiple($modelsFaturaProformaItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsFaturaProformaItem, 'id', 'id')));



            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsFaturaProformaItem),
                    ActiveForm::validate($model)
                );
            }

            // validate all models
            $valid = $model->validate();
            // $valid = Model::validateMultiple($modelsFaturaProformaItem) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
				
                    if ($flag = $model->save(false)) {
							
                        if (!empty($deletedIDs)) {
                            FaturaProformaItem::deleteAll(['id' => $deletedIDs]);
                        }
 
                        foreach ($modelsFaturaProformaItem as $modelFaturaProformaItem) {
                            $modelFaturaProformaItem->dsp_fatura_proforma_id = $model->id;
                            if (!($flag = $modelFaturaProformaItem->save())) {
								
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
            'modelsFaturaProformaItem' => (empty($modelsFaturaProformaItem)) ? [new FaturaProformaItem] : $modelsFaturaProformaItem
        ]);
    }


    /**
     * Deletes an existing FaturaProforma model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionUndo($id)
    {
        $model = $this->findModel($id);
        if (!empty($model->faturaProvisoria )) {
            Yii::$app->getSession()->setFlash('warning', 'ESTA FATURA PROFORMA JÁ FOI GERADO UMA FATURA PROVISÓRIA DELE NÃO PODE SER ANULADO');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            $model->status = 0;
            if ($model->save(false) ) {

                Yii::$app->getSession()->setFlash('success', 'FATURA PROFORMA ANULADO COM SUCESSO.');
            } else {
                // print_r($model->errors);die();
                Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Deletes an existing FaturaProforma model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionSendUnsend($id)
    {
        $model = $this->findModel($id);

        if ($model->send == FaturaProforma::ENVIADO) {
            $model->send = 2;
        } else {
            $model->send = !$model->send;
        }

        if ($model->save(false)) {
            Yii::$app->getSession()->setFlash('success', 'FATURA PROFORMA' . ($model->send ? ' CONFERIDO' : 'NÃO CONFERIDO') . ' COM SUCESSO.');
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
     * Finds the FaturaProforma model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $perfil_id
     * @return FaturaProforma the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FaturaProforma::findOne(['id' => $id])) !== null) {
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
        $modelsFaturaProformaItem = $model->faturaProformaItem;

        $company = Yii::$app->params['company'];

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('view-pdf', [
            'model' => $model,
            'company' => $company,
            'modelsFaturaProformaItem' => $modelsFaturaProformaItem,
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
        $model->send = $model->send == FaturaProforma::POR_VALIDAR ? FaturaProforma::POR_VALIDAR : FaturaProforma::ENVIADO;
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
     * Lists all FaturaProforma models.
     * @return mixed
     */
    public function actionHonorario()
    {
        $searchModel = new FaturaProformaSearch();
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
    public function actionCreateFaturaProvisoria($id)
    {
        $model = $this->findModel($id);
        $fp = new FaturaProvisoria();
        $fp->status = 1;
        $fp->send = 1;
        $fp->dsp_processo_id = $model->dsp_processo_id;
        $fp->nord = $model->nord;
        $fp->bas_ano_id = $model->bas_ano_id;
        $fp->data = $model->data;
        $fp->dsp_person_id = $model->dsp_person_id;
        $fp->mercadoria = $model->mercadoria;
        $fp->dsp_regime_id = $model->dsp_regime_id;
        $fp->dsp_regime_descricao = $model->dsp_regime_descricao;
        $fp->dsp_regime_item_id = $model->dsp_regime_item_id;
        $fp->dsp_regime_item_tabela_anexa = $model->dsp_regime_item_tabela_anexa;
        $fp->dsp_regime_item_valor = $model->dsp_regime_item_valor;
        $fp->dsp_regime_item_tabela_anexa_valor = $model->dsp_regime_item_tabela_anexa_valor;
        $fp->dsp_regime_item_desconto = $model->dsp_regime_item_desconto;
        $fp->impresso_principal = $model->impresso_principal;
        $fp->dv = $model->dv;
        $fp->tce = $model->tce;
        $fp->pl = $model->pl;
        $fp->tn = $model->tn;
        $fp->gti = $model->gti;
        $fp->fotocopias = $model->fotocopias;
        $fp->qt_estampilhas = $model->qt_estampilhas;
        $fp->form = $model->form;
        $fp->regime_normal = $model->regime_normal;
        $fp->exprevio_comercial = $model->exprevio_comercial;
        $fp->expedente_matricula = $model->expedente_matricula;
        $fp->taxa_comunicaco = $model->taxa_comunicaco;
        $fp->honorario = $model->honorario;
        $fp->fin_fatura_proforma_id = $model->id;
        if ($fp->save()) {
            $fp->status = 1;
            foreach ($model->faturaProformaItem as $key => $value) {
                $fpItem = new FaturaProvisoriaItem();
                $fpItem->dsp_fatura_provisoria_id = $fp->id;
                $fpItem->dsp_item_id = $value->dsp_item_id;
                $fpItem->valor = $value->valor;
                $fpItem->item_origem_id = $value->item_origem_id;
                $fpItem->dsp_fatura_provisoria_id = $value->id;
                $fpItem->save();
            }
        }
    }
}
