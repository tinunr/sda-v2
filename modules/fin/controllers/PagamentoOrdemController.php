<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\base\Exception;
use kartik\mpdf\Pdf;
use app\modules\fin\models\PagamentoOrdem;
use app\modules\fin\models\PagamentoOrdemSearch;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaSearch;
use app\modules\fin\models\PagamentoOrdemItem;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\models\Model;

/**
 * EdeficiosController implements the CRUD actions for PagamentoOrdem model.
 */
class PagamentoOrdemController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['validar-person', 'file-browser'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create-a', 'create', 'update', 'undo', 'view-pdf', 'send-unsend'],
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
                    'undo' => ['POST'],
                    'send-unsend' => ['POST'],
                    'file-browser' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PagamentoOrdem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PagamentoOrdemSearch();
        // $searchModel->send =[0,1];
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PagamentoOrdem model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PagamentoOrdem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateA()
    {
        $searchModel = new DespesaSearch();
        // $searchModel->bas_ano_id = substr(date('Y'),-2);       
        $dataProvider = $searchModel->searchCreate(Yii::$app->request->queryParams);

        return $this->render('create-a', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new PagamentoOrdem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PagamentoOrdem();
        $model->bas_ano_id = substr(date('Y'), -2);
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->fin_banco_id = 1;
        $model->fin_banco_conta_id = 1;
        $model->valor = 0;
        $model->descricao = 'PagamentoOrdem da(s) despesa(s) nº';

        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::PAGAMENTO_ORDEM_ID);
        $model->numero = $documentoNumero->getNexNumber();

        $selection = (array) Yii::$app->request->post('selection');
        $modelsDespesa = Despesa::findAll(['id' => $selection]);
        foreach ($modelsDespesa as $key => $value) {
            $model->dsp_person_id = $value->dsp_person_id;
            $model->descricao = $model->descricao . ' ' . $value->numero . '/' . $value->bas_ano_id . ';';
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);
            $modelsDespesa = Model::createMultiple(Despesa::classname(), $modelsDespesa);
            Model::loadMultiple($modelsDespesa, Yii::$app->request->post());

            $valid = $model->validate();
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $model->fin_banco_id = empty($model->fin_banco_id) ? 1 : $model->fin_banco_id;
                $model->fin_banco_conta_id = empty($model->fin_banco_conta_id) ? 1 : $model->fin_banco_conta_id;

                try {
                    if ($flag = $model->save()) {

                        foreach ($modelsDespesa as $modelDespesa) {
                            $item = new PagamentoOrdemItem();
                            $item->fin_pagamento_id = $model->id;
                            $item->fin_despesa_id = $modelDespesa->id;
                            $item->valor = $modelDespesa->valor_pago;
                            $item->valor_pago = $modelDespesa->valor_pago;
                            $item->saldo = $modelDespesa->saldo;
                            $model->valor = $model->valor +  $modelDespesa->valor_pago;
                            if (!($flag = $item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }

                            if (!($flag = $model->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }


                        if ($flag) {
                            $documentoNumero->saveNexNumber();
                            $transaction->commit();
                            return $this->redirect(['view', 'id' => $model->id]);
                        }
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'modelsDespesa' => $modelsDespesa,
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PagamentoOrdem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PagamentoOrdem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUndo($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        if ($model->save(false)) {
            Yii::$app->getSession()->setFlash('success', 'PagamentoOrdem ANULADO COM SUCESSO.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Deletes an existing PagamentoOrdem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSendUnsend($id)
    {
        $model = $this->findModel($id);
        $model->send = !$model->send;
        if ($model->send) {
            $model->data_validacao = new \yii\db\Expression('NOW()');
            $model->person_validacao = Yii::$app->user->identity->id;
        } else {
            $model->data_validacao = NULL;
            $model->person_validacao = NULL;
        }
        if ($model->save(false)) {
            Yii::$app->getSession()->setFlash('success', 'ORDEM DE PAGAMNTO  ' . ($model->send ? 'VALIDADO' : 'INVALIDADO') . ' COM SUCESSO.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the PagamentoOrdem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PagamentoOrdem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PagamentoOrdem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionValidarPerson(array $keys)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $isValid = 1;
        $personId = 0;
        $valor = 0;
        foreach ($keys as $key => $value) {
            $despesa = Despesa::find()
                ->where(['id' => $value])
                ->one();
            if (!empty($despesa)) {
                $valor = $valor + $despesa->saldo;
                if ($personId == 0) {
                    $personId = $despesa->dsp_person_id;
                } elseif ($personId != $despesa->dsp_person_id) {
                    $isValid = 0;
                }
            }
        }
        return [
            'isValid' => $isValid,
            'valor' => $valor,
        ];
    }


    public function actionViewPdf($id)
    {

        $model = $this->findModel($id);
        // $modelsFaturaProvisoriaItem = $model->faturaProvisoriaItem;
        // $faturaProvisoria = $model->faturaProvisoria;
        // $faturaProvisoriaRegimeItem = $faturaProvisoria->faturaProvisoriaRegimeItem;

        $company = Yii::$app->params['company'];

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('view-pdf', [
            'model' => $model,
            'company' => $company,
            // 'faturaProvisoriaRegimeItem'=>$faturaProvisoriaRegimeItem,
            // 'modelsFaturaProvisoriaItem'=>$modelsFaturaProvisoriaItem,
        ]);

        // setup kartik\mpdf\Pdf component
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'marginTop' => 50,
            'marginLeft' => 25,
            'marginRight' => 25,
            'options' => ['title' => 'Relatório Processo'],
            'methods' => [
                'SetHeader' => ['
              <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                  <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="' . $company['logo'] . '" height="60" width="150" />
                  </p>
                  <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i> ' . $company['name'] . ' </strong></p>  
                    <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>RECIBO Nº ' . $model->numero . '</strong></p>
              </div>
              '],
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

    public function actionFileBrowser($fin_pagamento_ordem_id)
    {
        $model = new \app\models\FileBrowser();
        $model->path = \app\components\helpers\UploadFileHelper::setUrlFilePagamentoOrdem($fin_pagamento_ordem_id);
        if (Yii::$app->request->isPost) {
            $model->files = \yii\web\UploadedFile::getInstances($model, 'files');
            if ($model->upload()) {
                Yii::$app->getSession()->setFlash('success', 'O ficheiro foi caregado com sucesso!');
            } else {
                Yii::$app->getSession()->setFlash('error', 'Ocoreu um erro!');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}
