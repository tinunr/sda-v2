<?php

namespace app\modules\cnt\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\db\ActiveQuery;
use yii\db\Query;
use app\models\Model;
use yii\data\ArrayDataProvider;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

use app\modules\cnt\models\DiarioNumero;
use app\modules\cnt\models\Documento;
use app\modules\cnt\models\Natureza;
use app\modules\cnt\models\Razao;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\RazaoSearch;
use app\modules\cnt\models\RazaoAutoCreate;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\Recebimento;
use app\modules\fin\models\Pagamento;
use app\modules\dsp\models\Item;
use app\modules\cnt\models\Lancamento;
use app\modules\cnt\models\LancamentoItem;
use app\modules\cnt\models\LancamentoCreate;
use app\models\Ano;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\RazaoLancamento;


/**
 * Default controller for the `employee` module
 */
class RazaoController extends Controller
{  

  public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions'=>['file-browser'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index','create','update','view','create-auto','undo','lancamento-error','create-a'],
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
                    'delete' => ['post'],
                    'create-auto' => ['post'],
                    'undo' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {    
        $razaoAutoCreate = new RazaoAutoCreate();
        $lancamentoCreate = new LancamentoCreate();
        $lancamentoCreate->bas_ano_id = substr(date('Y'),-2);
        $lancamentoCreate->cnt_diario_id = 6;

        $data_incio = mktime(0, 0, 0, date('m') , 1 , date('Y'));
        $data_fim = mktime(23, 59, 59, date('m'), date("t"), date('Y'));
        $razaoAutoCreate->dataInicio = date('Y-m-d', $data_incio);
        $razaoAutoCreate->dataFim = date('Y-m-d', $data_fim);

         $searchModel = new RazaoSearch();
         // $searchModel->cnt_diario_id = 1;
         $searchModel->bas_ano_id = substr(date('Y'),-2);
         $searchModel->bas_mes_id = date('m');
         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'razaoAutoCreate'=>$razaoAutoCreate,
            'lancamentoCreate'=>$lancamentoCreate,
        ]);
    }

     /**
     * Renders the index view for the module
     * @return string
     */
    public function actionLancamentoError()
    {    
        

         
         $data = Yii::$app->CntQuery->getLancamentoError();

        return $this->render('lancamento-error', [
            'data' => $data,
        ]);
    }

    
  /**
     * Displays a single Razao model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $providerRazaoItem = new ArrayDataProvider([
            'allModels' => $model->razaoItem,
            'pagination'=>false,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerRazaoItem'=>$providerRazaoItem,
        ]);
    }

    /**
     * Creates a new Razao model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Razao();
        $model->data = date('Y-m-d');
        $model->documento_origem_data = date('Y-m-d');
        $model->bas_mes_id = date('m');
        $model->bas_ano_id = substr(date('Y'),-2);
        $modelsRazaoItem = [new RazaoItem];

            if ($model->load(Yii::$app->request->post())) {
            $modelsRazaoItem = Model::createMultiple(RazaoItem::classname());
            Model::loadMultiple($modelsRazaoItem, Yii::$app->request->post());
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsRazaoItem),
                    ActiveForm::validate($model)
                );
            }
            $valid = $model->validate();           
            if ($valid) {                
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {         
                        foreach ($modelsRazaoItem as $modelRazaoItem) {
                            $modelRazaoItem->cnt_razao_id = $model->id;
                            if (! ($flag = $modelRazaoItem->save(false))) {
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
            'modelsRazaoItem' => (empty($modelsRazaoItem)) ? [new RazaoItem] : $modelsRazaoItem,
            
        ]);
    }



    /**
     * Updates an existing Razao model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelsRazaoItem = $model->razaoItem;

            if ($model->load(Yii::$app->request->post())) {
            // $model->bas_ano_id = substr(Yii::$app->formatter->asDate($model->data, 'Y'),-2);
            // $model->bas_ano_id = substr(date('Y', strtotime($model->data)),-2);
            $oldIDs = ArrayHelper::map($modelsRazaoItem, 'id', 'id');
            $modelsRazaoItem = Model::createMultiple(RazaoItem::classname(), $modelsRazaoItem);
            Model::loadMultiple($modelsRazaoItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsRazaoItem, 'id', 'id')));
            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsRazaoItem),
                    ActiveForm::validate($model)
                );
            }
            $valid = $model->validate();           
            if ($valid) {                
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {   
                        if (! empty($deletedIDs)) {
                            RazaoItem::deleteAll(['id' => $deletedIDs]);
                        }      
                        foreach ($modelsRazaoItem as $modelRazaoItem) {
                            $modelRazaoItem->cnt_razao_id = $model->id;
                            if (! ($flag = $modelRazaoItem->save(false))) {
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
            'modelsRazaoItem' => (empty($modelsRazaoItem)) ? [new RazaoItem] : $modelsRazaoItem,
            
        ]);
        
    }



    
    /**
     * Creates a new Razao model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateAuto()
    {
        $razaoAutoCreate = new RazaoAutoCreate();
        $ok = 0;
        if ($razaoAutoCreate->load(Yii::$app->request->post())) {
                // lista dos documentos
                $documentos = Documento::find()
                            ->where(['id'=>$razaoAutoCreate->cnt_documento_id])
                            ->all();
                            // print_r($documentos);die();
                foreach ($documentos as $key => $documento) {
                    # DOCUMENTO FATURA DEFINITIVA
                    if ($documento->id == Documento::FATURA_DEFINITIVA) {
                        $ok = $ok + Yii::$app->RazaoAutoCreate->createByFaturaDefinitiva($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim) ; 
                    }// end lisat dos documentos



                    # DOCUMENTO createByMovimentoInterno
                    if ($documento->id == Documento::MOVIMENTO_INTERNO) {
                        $ok = $ok + Yii::$app->RazaoAutoCreate->createByMovimentoInterno($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim) ; 
                    }// end createByMovimentoInterno


                    // # DOCUMENTODESPESA_FATURA_FORNECEDOR
                    if ($documento->id == Documento::DESPESA_FATURA_FORNECEDOR) {
                        $ok = $ok + Yii::$app->RazaoAutoCreate->createByFaturaFornecidor($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim) ; 
                    }// end DESPESA_FATURA_FORNECEDOR

                    // # FATURA_FORNECEDOR_INVESTIMENTO
                    if ($documento->id == Documento::FATURA_FORNECEDOR_INVESTIMENTO) {
                        $ok = $ok + Yii::$app->RazaoAutoCreate->createByFaturaFornecidorInvestimento($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim) ; 
                    }// end FATURA_FORNECEDOR_INVESTIMENTO

                     // # RECEBIMENTO_TESOURARIO
                    if ($documento->id == Documento::RECEBIMENTO_TESOURARIO) {
                        $ok = $ok + Yii::$app->RazaoAutoCreate->createByRecebimentoTesouraria($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim) ; 
                    }// end RECEBIMENTO_TESOURARIO

                     // # RECEBIMENTO_FATURA_PROVISORIA
                    if ($documento->id == Documento::RECEBIMENTO_FATURA_PROVISORIA) {
                        $ok = $ok + Yii::$app->RazaoAutoCreate->createByRecebimentoFaturaProviria($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim) ; 
                    }// end RECEBIMENTO_FATURA_PROVISORIA

                     // # RECEBIMENTO_FATURA_PROVISORIA
                    if ($documento->id == Documento::RECEBIMENTO_ADIANTAMENTO) {
                        $ok = $ok + Yii::$app->RazaoAutoCreate->createByRecebimentoAdiantamento($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim) ; 
                    }// end RECEBIMENTO_FATURA_PROVISORIA 

                    // # RECEBIMENTO_FATURA_PROVISORIA
                    if ($documento->id == Documento::RECEBIMENTO_REEMBOLSO) {
                        $ok = $ok + Yii::$app->RazaoAutoCreate->createByRecebimentoReembolso($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim) ; 
                    }// end RECEBIMENTO_FATURA_PROVISORIA 

                    
                    # DOCUMENTO FATURA PAGAMENTO
                    if ($documento->id == Documento::PAGAMENTO) {
                         $ok = $ok + Yii::$app->RazaoAutoCreate->createByPagamento($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim);
                    }// end lisat dos documentos  

                    # DOCUMENTO FATURA ELETRONICA
                    if ($documento->id == Documento::FACTURA) {
                         $ok = $ok + Yii::$app->RazaoAutoCreate->createByFatura($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim);
                    }// end lisat dos documentos

                    # NOTA DE DÉBITO - CLIENTE
                    if($documento->id == Documento::NOTA_DE_DEBITO_CLIENTE){
                         $ok = $ok + Yii::$app->RazaoAutoCreate->createByFaturaDebitoCliente($razaoAutoCreate->dataInicio, $razaoAutoCreate->dataFim);
                    }
                
            }
        }
        Yii::$app->getSession()->setFlash('success',($ok>0?$ok:'').' Lamçamento atualizado com sucesso.');
        return $this->redirect(Yii::$app->request->referrer);
        // return $this->redirect(['index']);
            
    }









    
    
    /**
     * Deletes an existing FaturaProvisoria model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionCreateA()
    {
        set_time_limit(0);
        $transaction = Yii::$app->db->beginTransaction();
        $model = new LancamentoCreate();
        $ok = false;
        $valor = 0;
        if ($model->load(Yii::$app->request->post())) {

            $ano = Ano::findOne($model->bas_ano_id)->ano;
            $lancamentoS = Lancamento::find()->orderBy('id')->all();
            $razaoLancamento = new RazaoLancamento();
            $razaoLancamento->ano = $ano;
            $razaoLancamento->descricao = 'LANÇAMENTO AUTOMATICO DE FECHO DO ANO';
            $razaoLancamento->save();

            foreach ($lancamentoS as $key => $lancamento) {
            if($lancamento->cnt_lancamento_tipo_id==1){
                        $dataBalanecete = Yii::$app->CntQuery->getBalanceteAberturaConta($model->bas_ano_id);
                        $razao = new Razao();
                        $razao->operacao_fecho = $razaoLancamento->id; 
                        $razao->status = 1; 
                        $razao->bas_ano_id = substr(($ano+1),-2);
                        $razao->bas_mes_id = $lancamento->bas_mes_id; 
                        $razao->valor_debito = 0;
                        $razao->valor_credito = 0;
                        $razao->cnt_diario_id = $lancamento->cnt_diario_id;
                        $razao->data = ($ano+1).'-01-01';
                        $razao->descricao = $lancamento->descricao;
                if(!($razao->save())){
                        $errors = [
                                'errors'=>$razao->errors,
                                'razao'=>$razao,
                        ];
                            print_r($errors);die();
                        $transaction->rollBack();
                    }

                    foreach($dataBalanecete as $value){
                        $cnt_plano_conta_id = null;
                        if(($lancamentoItem = LancamentoItem::find()->where(['origem_id'=>$value['cnt_plano_conta_id']])->asArray()->one())!=null){
                            $cnt_plano_conta_id = $lancamentoItem['destino_id'];
                        }else{
                            $cnt_plano_conta_id =  $value['cnt_plano_conta_id'];
                        }
                        $modelItem = new RazaoItem();
                        $modelItem->descricao =  $lancamento->descricao.' - '.$ano.' - '.$cnt_plano_conta_id;
                        $modelItem->cnt_razao_id = $razao->id;
                        $modelItem->cnt_plano_conta_id =$cnt_plano_conta_id;
                        $modelItem->cnt_plano_terceiro_id = $value['cnt_plano_terceiro_id'];
                        if($value['saldo_debito']>0){
                            $valor = $value['saldo_debito'];
                            $natureza =  'D';
                        }else{
                            $valor = $value['saldo_credito'];
                            $natureza =  'C';
                        }
                        $modelItem->cnt_natureza_id = $natureza;
                        $modelItem->valor = $valor;
                        
                        if(!($modelItem->save(false))){
                            $errors = [
                                'errors'=>$modelItem->errors,
                                'modelItem'=>$modelItem,
                            ];
                            print_r($errors);die();
                            $transaction->rollBack();
                        }
                    }


            // fecho de conta
            }else{

                $razao = new Razao();
                $razao->operacao_fecho = $razaoLancamento->id; 
                $razao->status = 1; 
                $razao->bas_ano_id = $model->bas_ano_id; 
                $razao->bas_mes_id = $lancamento->bas_mes_id; 
                $razao->valor_debito = 0;
                $razao->valor_credito =0;
                $razao->cnt_diario_id = $lancamento->cnt_diario_id;
                $razao->data = date('Y-m-d');
                $razao->descricao =$lancamento->descricao;
           if(!($razao->save())){
               $errors = [
                    'errors'=>$razao->errors,
                    'razao'=>$razao,
               ];
                print_r($errors);die();
                $transaction->rollBack();
            }

           
            $lancamentoItem = LancamentoItem::find()
                            ->where(['cnt_lancamento_id'=>$lancamento->id])
                            ->asArray()
                            ->all();

            foreach($lancamentoItem as $value){
                
                $dataOne = Yii::$app->CntQuery->getBalanceteFechoConta($model->bas_ano_id, $lancamento->bas_mes_id,$value['origem_id']);
                foreach ($dataOne as $key => $data) {
                    $modelItem = new RazaoItem();
                    $modelItem->descricao = $lancamento->descricao.' - '.$data['descricao'];
                    $modelItem->cnt_razao_id = $razao->id;
                    $modelItem->cnt_plano_conta_id =$value['destino_id'];
                    $modelItem->cnt_plano_terceiro_id = $data['cnt_plano_terceiro_id'];
                    if($data['saldo_debito']>0){
                        $valor = $data['saldo_debito'];
                        $natureza =  'D';
                    }elseif($data['saldo_credito']>0){
                        $valor = $data['saldo_credito'];
                        $natureza =  'C';
                    }
                    $modelItem->cnt_natureza_id = $natureza;
                    $modelItem->valor = $valor;
                    if(!($modelItem->save(false))){
                        $errors = [
                            'errors'=>$modelItem->errors,
                            'razao'=>$modelItem,
                    ];
                        print_r($errors);die();
                        $transaction->rollBack();
                    }
                }

                $dataTwo = Yii::$app->CntQuery->getBalanceteFechoContaAll($model->bas_ano_id, $lancamento->bas_mes_id,$value['origem_id']);
                // print_r( $dataTwo );die();
                foreach ($dataTwo as $key => $data) {
                    $modelItem = new RazaoItem();
                    $modelItem->descricao = $data['descricao'];
                    $modelItem->cnt_razao_id = $razao->id;
                    $modelItem->cnt_plano_conta_id =$data['id'];
                    $modelItem->cnt_plano_terceiro_id = $data['cnt_plano_terceiro_id'];
                    if($data['saldo_debito']>0){
                        $valor = $data['saldo_debito'];
                        $natureza =  'C';
                    }elseif($data['saldo_credito']>0){
                        $valor = $data['saldo_credito'];
                        $natureza =  'D';
                    }
                    $modelItem->cnt_natureza_id = $natureza;
                    $modelItem->valor = $valor;
                    if(!($modelItem->save(false))){
                        $errors = [
                            'errors'=>$modelItem->errors,
                            'razao'=>$modelItem,
                    ];
                        print_r($errors);die();
                        $transaction->rollBack();
                    }
                }

            }
            }
        }
    }
    $transaction->commit();
    Yii::$app->getSession()->setFlash('success', 'OPERÇÃO EFETUADO COM SUCESSO.');
    return $this->redirect(Yii::$app->request->referrer);
        
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
        $model->status = 0;
        if ($model->save(false)) {
            Yii::$app->getSession()->setFlash('success', 'LANÇAMENTO ANULADO COM SUCESSO.');
        }else{
            Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }       
        return $this->redirect(Yii::$app->request->referrer);
        
    }

    /**
     * Finds the Razao model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Razao the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Razao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionFileBrowser($cnt_razao_id)
    {
        $model = new \app\models\FileBrowser();
        $model->path = \app\components\helpers\UploadFileHelper::setUrlFileContabilidade($cnt_razao_id); 
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