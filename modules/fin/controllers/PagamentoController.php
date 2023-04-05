<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\fin\models\Pagamento;
use app\modules\fin\models\PagamentoSearch;
use app\modules\fin\models\PagamentoOrdem;
use app\modules\fin\models\PagamentoOrdemItem;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaSearch;
use app\modules\fin\models\PagamentoItem;
use app\modules\fin\models\BancoTransacao;
use app\modules\fin\models\BancoTransacaoTipo;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\CaixaOperacao;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\models\Model;
use kartik\mpdf\Pdf;
use app\modules\fin\models\Caixa;

/**
 * EdeficiosController implements the CRUD actions for Pagamento model.
 */
class PagamentoController extends Controller
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
                        'actions' => ['validar-person','file-browser'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','view','update','undo','view-pdf','create-by-ordem'],
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
                    'file-browser' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Pagamento models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PagamentoSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);       
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pagamento model.
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
     * Creates a new Pagamento model.
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
     * Creates a new Pagamento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    { $model = new Pagamento();
        $model->bas_ano_id = substr(date('Y'),-2);
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->fin_banco_id = 1;
        $model->fin_banco_conta_id = 1;
        $model->valor = 0;
        $model->descricao = 'Pagamento da(s) despesa(s) nº';

        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::PAGAMENTO_ID);
        $model->numero = $documentoNumero->getNexNumber();
 
        $selection=(array)Yii::$app->request->post('selection');
        $modelsDespesa = Despesa::findAll(['id'=>$selection]);
        foreach ($modelsDespesa as $key => $value) {
           $model->dsp_person_id = $value->dsp_person_id;
           $model->descricao = $model->descricao.' '.$value->numero.'/'.$value->bas_ano_id.';';
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->bas_ano_id = substr(date("Y", strtotime($model->data)),-2);
            $modelsDespesa = Model::createMultiple(Despesa::classname(), $modelsDespesa);
            Model::loadMultiple($modelsDespesa, Yii::$app->request->post());

            $valid = $model->validate();
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $model->fin_banco_id= empty($model->fin_banco_id)?1:$model->fin_banco_id;
                $model->fin_banco_conta_id= empty($model->fin_banco_conta_id)?1:$model->fin_banco_conta_id;

                try {
                    if ($flag = $model->save()) {

                        foreach ($modelsDespesa as $modelDespesa) {
                            $item = new PagamentoItem();
                            $item->fin_pagamento_id = $model->id;
                            $item->fin_despesa_id = $modelDespesa->id;
                            $item->valor = $modelDespesa->valor_pago;
                            $item->valor_pago = $modelDespesa->valor_pago;
                            $item->saldo = $modelDespesa->saldo;
                            $model->valor = $model->valor +  $modelDespesa->valor_pago;
                            if (! ($flag = $item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }


                            $modelDespesaUp = Despesa::findOne(['id'=>$modelDespesa->id]);
                            $modelDespesa->isNewRecord =false;
                            $modelDespesaUp->valor_pago = $modelDespesaUp->valor_pago + $modelDespesa->valor_pago;
                            $modelDespesaUp->saldo = $modelDespesa->saldo;
                            if (! ($flag =$modelDespesaUp->save(false))) {
                                $transaction->rollBack();
                                break;
                            }

                            if (! ($flag =$model->save(false))) {
                                $transaction->rollBack();
                                break;
                            }

                        }


                        $caixaTransacao = new CaixaTransacao();
                        $caixaTransacao->fin_pagamento_id =  $model->id;
                        $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacao->descricao = 'Pagamento nº '.$model->numero.'/'.$model->bas_ano_id;
                        $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id);
                        $caixaTransacao->valor_saida = $model->valor;
                        $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id);
                        $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::PAGAMENTO;
                        $caixaTransacao->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacao->numero_documento = $model->numero_documento;
                        $caixaTransacao->data_documento = $model->data_documento;
                        $caixaTransacao->data = new \yii\db\Expression('NOW()');
                        if (! ($flag =$caixaTransacao->save(false))) {
                                $transaction->rollBack();
                            }



                            // print_r($model);die();

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
     * Creates a new Pagamento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateByOrdem($fin_pagamento_ordem_id)
    {
        $model = new Pagamento();
        $modelPagamentoOrdem = PagamentoOrdem::findOne($fin_pagamento_ordem_id);
        $model->fin_pagamento_ordem_id = $modelPagamentoOrdem->id;
        $model->bas_ano_id = substr(date('Y'), -2);
        $model->data = date('Y-m-d');
        $model->valor = $modelPagamentoOrdem->valor;
        $model->descricao = $modelPagamentoOrdem->descricao;
        $model->dsp_person_id = $modelPagamentoOrdem->dsp_person_id;
        $model->fin_banco_id = $modelPagamentoOrdem->fin_banco_id;
        $model->fin_banco_conta_id = $modelPagamentoOrdem->fin_banco_conta_id;
        $model->fin_documento_pagamento_id = $modelPagamentoOrdem->fin_documento_pagamento_id;
        $model->numero_documento = $modelPagamentoOrdem->numero_documento;
        $model->data_documento = $modelPagamentoOrdem->data_documento;
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::PAGAMENTO_ID);
        $model->numero = $documentoNumero->getNexNumber();

        $modelPagamentoOrdemItem = $modelPagamentoOrdem->item;

        $valid = $model->validate();
        if ($valid) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if ($flag = $model->save()) {

                    $modelPagamentoOrdem->fin_pagamento_id = $model->id;
                    $modelPagamentoOrdem->send = 2;
                    if (!($flag = $modelPagamentoOrdem->save(false))) {
                        $transaction->rollBack();
                    }

                    foreach ($modelPagamentoOrdemItem as $modelDespesa) {
                        $item = new PagamentoItem();
                        $item->fin_pagamento_id = $model->id;
                        $item->fin_despesa_id = $modelDespesa->fin_despesa_id;
                        $item->valor = $modelDespesa->valor_pago;
                        $item->valor_pago = $modelDespesa->valor_pago;
                        $item->saldo = $modelDespesa->saldo;
                        if (!($flag = $item->save(false))) {
                            $transaction->rollBack();
                            break;
                        }


                        $modelDespesaUp = Despesa::findOne(['id' => $modelDespesa->fin_despesa_id]);
                        $modelDespesaUp->isNewRecord = false;
                        $modelDespesaUp->saldo = $modelDespesaUp->saldo - $modelDespesa->valor_pago;
                        $modelDespesaUp->valor_pago = $modelDespesaUp->valor_pago + $modelDespesa->valor_pago;
                        if (!($flag = $modelDespesaUp->save(false))) {
                            $transaction->rollBack();
                            break;
                        }

                        // if (!($flag = $model->save(false))) {
                        //     $transaction->rollBack();
                        //     break;
                        // }
                    }


                    $caixaTransacao = new CaixaTransacao();
                    $caixaTransacao->fin_pagamento_id =  $model->id;
                    $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
                    $caixaTransacao->descricao = 'Pagamento nº ' . $model->numero . '/' . $model->bas_ano_id;
                    $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id);
                    $caixaTransacao->valor_saida = $model->valor;
                    $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id);
                    $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::PAGAMENTO;
                    $caixaTransacao->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                    $caixaTransacao->numero_documento = $model->numero_documento;
                    $caixaTransacao->data_documento = $model->data_documento;
                    $caixaTransacao->data = new \yii\db\Expression('NOW()');
                    if (!($flag = $caixaTransacao->save(false))) {
        $transaction->rollBack();
                    }

                    if ($flag) {
        $documentoNumero->saveNexNumber();
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }

            } catch (yii\base\Exception $e) {
                    print_r($e);die();
                $transaction->rollBack();
            }
        }
        Yii::$app->getSession()->setFlash('error', 'Ocoreu um erro!');

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Updates an existing Pagamento model.
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
     * Deletes an existing Pagamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUndo($id)
    {
        if (Yii::$app->CntQuery->inContabilidadeAtive(\app\modules\cnt\models\Documento::PAGAMENTO, $id)) {
            Yii::$app->getSession()->setFlash('warning', 'Documento já se encontra na Contabilidade anule na Contabilidade para poder proceguir.');
            return $this->redirect(['view','id'=>$id]);
        }

        $model = $this->findModel($id);

        $transaction = \Yii::$app->db->beginTransaction();
        // $flag = false;
        if(($caixaTransacao =  CaixaTransacao::findOne(['fin_pagamento_id'=>$model->id]))!=NULL){
            $caixa = Caixa::findOne($caixaTransacao->fin_caixa_id);
// print_r($caixa);die();
            if ($caixa->status==Caixa::CLOSE_CAIXA) {
                Yii::$app->getSession()->setFlash('warning','A caixa '.$caixa->descricao.' se encontra fechado.');
                return $this->redirect(Yii::$app->request->referrer);
              } else{
            if (! ($flag =$caixaTransacao->delete())){
                  $transaction->rollBack();
            }
            }
          }

        foreach ($model->item as $key => $value) {
          if(($despesa = Despesa::findOne($value->fin_despesa_id))!=null){
              $despesa->valor_pago = $despesa->valor_pago - $value->valor;
              $despesa->saldo = $despesa->saldo + $value->valor;
              if (! ($flag =$despesa->save(false))) {
                  $transaction->rollBack();
                  break;
              }else{
                //   print_r($despesa->errors);die();
              }
          }
        }

       $model->status = 0;


        if(!($flag = $model->save(false))){
                  $transaction->rollBack();

        }else{
            // print_r($model->errors);die();
        }





        if ($flag) {
                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', 'PAGAMENTO ANULADO COM SUCESSO.');
          }else{
              Yii::$app->getSession()->setFlash('error', 'ERRO AO EFETUAR A OPERAÇÃO.');
          }

        return $this->redirect(['view','id'=>$id]);
    }

    /**
     * Finds the Pagamento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pagamento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pagamento::findOne($id)) !== null) {
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
                     ->where(['id'=>$value])  
                     ->one();
           if (!empty($despesa) ){
             $valor = $valor + $despesa->saldo;
             if ($personId==0) {
               $personId = $despesa->dsp_person_id;
             }elseif($personId != $despesa->dsp_person_id){
               $isValid = 0;
             }
           }
         }
         return [
           'isValid'=>$isValid,
           'valor'=>$valor,
         ];
 
         
     }


    public function actionViewPdf($id) {
        
        $model = $this->findModel($id);
        $company = Yii::$app->params['company'];
        $content = $this->renderPartial('view-pdf',[
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

    public function actionFileBrowser($fin_pagamento_id)
    {
        $model = new \app\models\FileBrowser();
        $model->path = \app\components\helpers\UploadFileHelper::setUrlFilePagamento($fin_pagamento_id);
        // print_r($model);
        // die();
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
