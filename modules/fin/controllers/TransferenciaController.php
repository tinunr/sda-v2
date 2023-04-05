<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;

use app\modules\fin\models\Transferencia;
use app\modules\fin\models\TransferenciaSearch;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\CaixaOperacao;
use app\modules\fin\models\Caixa;
use Exception;

/**
 * EdeficiosController implements the CRUD actions for Transferencia model.
 */
class TransferenciaController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'undo', 'file-browser', 'view-pdf'],
                        'allow' => true,
                        'roles' => ['@'],

                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'undo' => ['POST'],
                    'file-browser' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Transferencia models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransferenciaSearch();
        $searchModel->bas_ano_id =  substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Transferencia model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        // $searchModel =  Transferencia::find()->where(['bas_ano_id'=>20])->all();
        // foreach($searchModel as $m){
        //     ;
        //     if (is_dir(Yii::getAlias('@transferencias').'/'.date('Y', strtotime($m->data)).'/'.$m->referencia)) {
        //     rename(Yii::getAlias('@transferencias').'/'.date('Y', strtotime($m->data)).'/'.$m->referencia, Yii::getAlias('@transferencias').'/'.date('Y', strtotime($m->data)).'/'.$m->numero);
        //     }
        // }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Transferencia model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transferencia();
        $model->data = date('Y-m-d');

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {

                        // SAIDA
                        $caixaTransacaoOut = new CaixaTransacao();
                        $caixaTransacaoOut->fin_transferencia_id =  $model->id;
                        $caixaTransacaoOut->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacaoOut->descricao = 'Transferência nº ' . $model->numero;
                        $caixaTransacaoOut->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id_origem);
                        $caixaTransacaoOut->valor_saida = $model->valor;
                        $caixaTransacaoOut->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id_origem);
                        $caixaTransacaoOut->fin_caixa_operacao_id =  CaixaOperacao::TRANSFERENCIA;
                        $caixaTransacaoOut->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacaoOut->numero_documento = (string)$model->numero;
                        $caixaTransacaoOut->data_documento = $model->data;
                        $caixaTransacaoOut->data = new \yii\db\Expression('NOW()');
                        if (!($flag = $caixaTransacaoOut->save())) {
                            //  print_r($caixaTransacaoOut->errors);die();
                            $transaction->rollBack();
                        }



                        // ENTRADA
                        $caixaTransacaoIn = new CaixaTransacao();
                        $caixaTransacaoIn->fin_transferencia_id =  $model->id;
                        $caixaTransacaoIn->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacaoIn->descricao = 'Transferência nº ' . $model->numero;
                        $caixaTransacaoIn->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id_destino);
                        $caixaTransacaoIn->valor_entrada = $model->valor;
                        $caixaTransacaoIn->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id_destino);
                        $caixaTransacaoIn->fin_caixa_operacao_id =  CaixaOperacao::TRANSFERENCIA;
                        $caixaTransacaoIn->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacaoIn->numero_documento = (string) $model->numero;
                        $caixaTransacaoIn->data_documento = $model->data;
                        $caixaTransacaoIn->data = new \yii\db\Expression('NOW()');
                        if (!($flag = $caixaTransacaoIn->save())) {
                            //  print_r($caixaTransacaoIn->errors);die();
                            $transaction->rollBack();
                        }

                        if ($flag) {
                            $transaction->commit();
                            return $this->redirect(['view', 'id' => $model->id]);
                        }
                    }
                } catch (Exception $e) {
                    print_r($e);
                    die();

                    $transaction->rollBack();
                }
            } else {
                // print_r($model->errors);die();
            }
        }
        // print_r($model->errors);die();

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Transferencia model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {
                        CaixaTransacao::deleteAll(['fin_transferencia_id' => $model->id]);
                        // SAIDA
                        $caixaTransacaoIn = new CaixaTransacao();
                        $caixaTransacaoIn->fin_transferencia_id =  $model->id;
                        $caixaTransacaoIn->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacaoIn->descricao = 'Transferência nº ' . $model->numero;
                        $caixaTransacaoIn->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id_origem);
                        $caixaTransacaoIn->valor_saida = $model->valor;
                        $caixaTransacaoIn->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id_origem);
                        $caixaTransacaoIn->fin_caixa_operacao_id =  CaixaOperacao::TRANSFERENCIA;
                        $caixaTransacaoIn->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacaoIn->numero_documento = $model->numero;
                        $caixaTransacaoIn->data_documento = $model->data;
                        if (!($flag = $caixaTransacaoIn->save())) {
                            $transaction->rollBack();
                        }



                        // ENTRADA
                        $caixaTransacaoIn = new CaixaTransacao();
                        $caixaTransacaoIn->fin_transferencia_id =  $model->id;
                        $caixaTransacaoIn->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacaoIn->descricao = 'Transferência nº ' . $model->numero;
                        $caixaTransacaoIn->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id_destino);
                        $caixaTransacaoIn->valor_entrada = $model->valor;
                        $caixaTransacaoIn->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id_destino);
                        $caixaTransacaoIn->fin_caixa_operacao_id =  CaixaOperacao::TRANSFERENCIA;
                        $caixaTransacaoIn->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacaoIn->numero_documento = $model->numero;
                        $caixaTransacaoIn->data_documento = $model->data;
                        $caixaTransacaoIn->data = new \yii\db\Expression('NOW()');
                        if (!($flag = $caixaTransacaoIn->save())) {
                            //    print_r($caixaTransacaoIn->errors);die();
                            $transaction->rollBack();
                        }

                        if ($flag) {
                            $transaction->commit();
                            return $this->redirect(['view', 'id' => $model->id]);
                        }
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Transferencia model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUndo($id)
    {
        if (Yii::$app->CntQuery->inContabilidade(\app\modules\cnt\models\Documento::MOVIMENTO_INTERNO, $id)) {
            Yii::$app->getSession()->setFlash('warning', 'Esta documento já se encontra na Contabilidade anule na Contabilidade para poder proceguir.');
            return $this->redirect(['view', 'id' => $id]);
        }


        $model = $this->findModel($id);
        $caixaTransacao = CaixaTransacao::find()->where(['fin_transferencia_id' => $model->id])->all();
        foreach ($caixaTransacao as $key => $value) {
            $caixa = Caixa::findOne(['id' => $value->fin_caixa_id]);
            if ($caixa->status == Caixa::CLOSE_CAIXA) {
                Yii::$app->getSession()->setFlash('warning', 'A caixa ' . $caixa->descricao . ' se encontra fechado.');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }


        $caixaTransacaos = CaixaTransacao::find()->where(['fin_transferencia_id' => $model->id])->all();
        foreach ($caixaTransacaos as $key => $transacao) {
            $transacao->delete();
        }
        $model->status = 0;
        $model->save(false);

        Yii::$app->getSession()->setFlash('success', 'Transferencia anulado com sucesso.');
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Transferencia model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transferencia the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transferencia::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionViewPdf($id)
    {

        $model = $this->findModel($id);
        $company = Yii::$app->params['company'];
        $content = $this->renderPartial('view-pdf', [
            'model' => $model,
            'company' => $company,
        ]);
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
            'options' => ['title' => 'Pagamento '],
            'methods' => []
        ]);
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        return $pdf->render();
    }

    public function actionFileBrowser($fin_transferencia_id)
    {
        $model = new \app\models\FileBrowser();
        $model->path = \app\components\helpers\UploadFileHelper::setUrlFileTransferencia($fin_transferencia_id);

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
