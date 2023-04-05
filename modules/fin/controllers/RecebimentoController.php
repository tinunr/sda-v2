<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Model;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\modules\fin\models\Recebimento;
use app\modules\fin\models\RecebimentoSearch;
use app\modules\fin\models\RecebimentoItem;
use kartik\mpdf\Pdf;
use app\modules\fin\models\Receita;
use app\modules\fin\models\ReceitaSearch;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\CaixaOperacao;
use app\modules\fin\models\DespesaItem;
use app\modules\fin\models\RecebimentoTipo;
use app\modules\dsp\models\Item;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\fin\services\DespesaService;
use yii\base\Exception;
use yii\helpers\Html;

/**
 * EdeficiosController implements the CRUD actions for Recebimento model.
 */
class RecebimentoController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'undo', 'view-pdf', 'criar', 'receita', 'relatorio', 'reportpdf', 'adiantamento', 'reembolso', 'view-historico-pdf'],
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
     * Lists all Recebimento models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new RecebimentoSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Recebimento models.
     * @return mixed
     */
    public function actionRelatorio()
    {

        $searchModel = new RecebimentoSearch();
        // $searchModel->bas_ano_id = substr(date('Y'),-2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('relatorio', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Recebimento model.
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
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionReceita()
    {
        $searchModel = new ReceitaSearch();
        // $searchModel->bas_ano_id = substr(date('Y'),-2);       
        $dataProvider = $searchModel->searchCreate(Yii::$app->request->queryParams);

        return $this->render('receita', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new Recebimento();
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->descricao = 'Recebimento da(s) ';
        $model->fin_recebimento_tipo_id = RecebimentoTipo::CONTA_CORENTE;


        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::RECEBIMENTO_ID);
        $model->numero = $documentoNumero->getNexNumber();


        $selection = (array)Yii::$app->request->post('selection');
        $modelsReceita = Receita::findAll(['id' => $selection]);

        foreach ($modelsReceita as $key => $value) {
            if ($value->fin_receita_tipo_id == 1) {
                $model->descricao .= 'FP nº ' . $value->faturaProvisoria->numero . '/' . $value->faturaProvisoria->bas_ano_id . ';';
            $model->dsp_person_id = $value->faturaProvisoria->dsp_person_id;

            } else {
                $model->descricao .= 'FD nº ' . $value->faturaDefinitiva->numero . '/' . $value->faturaDefinitiva->bas_ano_id . ';';
            $model->dsp_person_id = $value->faturaDefinitiva->dsp_person_id;

            }
        }


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);

            $modelsReceita = Model::createMultiple(Receita::classname(), $modelsReceita);
            Model::loadMultiple($modelsReceita, Yii::$app->request->post());

            if ($model->validate()) {
				
                $transaction = \Yii::$app->db->beginTransaction();
                $flag = true;
                 try {

                    if ($flag = $model->save()) {

                        foreach ($modelsReceita as $modelReceita) {
                            $recebimentoItem = new RecebimentoItem();
                            $recebimentoItem->fin_recebimento_id = $model->id;
                            $recebimentoItem->fin_receita_id = $modelReceita->id;
                            $recebimentoItem->valor = $modelReceita->valor;
                            $recebimentoItem->valor_recebido = $modelReceita->valor_recebido;
                            $recebimentoItem->saldo = $modelReceita->saldo;
                            $recebimentoItem->descricao_item = $modelReceita->descricao;
                            $recebimentoItem->status = 1;
                            $model->dsp_fatura_provisoria_id = $modelReceita->dsp_fataura_provisoria_id;
                            if (!($flag = $recebimentoItem->save())) {
								Yii::$app->getSession()->setFlash('error', Html::errorSummary($recebimentoItem, ['encode' => false]));								
                                $transaction->rollBack();
                                break;
                            }

                            $receita = Receita::findOne(['id' => $modelReceita->id]);
                            $receita->saldo =  $modelReceita->saldo;
                            $receita->valor_recebido =  $receita->valor_recebido + $modelReceita->valor_recebido;
                            if (!($flag = $receita->save(false))) {
							    Yii::$app->getSession()->setFlash('error', Html::errorSummary($receita, ['encode' => false]));
                                $transaction->rollBack();
                                break;
                            }


                            /********************** gerar despesas recebimento fatura provisoria ****************/
                            if ($receita->fin_receita_tipo_id == Receita::FATURA_PROVISORIA) {
                                $fatura = $recebimentoItem->receita->faturaProvisoria;
                                $faturaItem = $recebimentoItem->receita->faturaProvisoria->faturaProvisoriaItem;
                            } elseif ($receita->fin_receita_tipo_id == Receita::FATURA_DEFINITIVA) {
                                $fatura = $recebimentoItem->receita->faturaDefinitiva;
                                $faturaItem = $recebimentoItem->receita->faturaDefinitiva->faturaDefinitivaItem;
                            }
                            $pagamentos = [];
                            $i = 0;
                            $j = 0;

                            foreach ($faturaItem as $key => $modelFaturaProvisoriaItem) {
                                $i = $modelFaturaProvisoriaItem->item->dsp_person_id;

                                if (array_key_exists($i, $pagamentos)) {
                                    $pagamentos[$i][$j] =   [
                                        'dsp_processo_id' => $fatura->dsp_processo_id,
                                        'dsp_fatura_provisoria_id' => $recebimentoItem->receita->dsp_fataura_provisoria_id,
                                        'dsp_person_id' => $modelFaturaProvisoriaItem->item->dsp_person_id,
                                        'dsp_item_id' => $modelFaturaProvisoriaItem->item->id,
                                        'valor' => $modelFaturaProvisoriaItem->valor,
                                        'item_origem_id' => $modelFaturaProvisoriaItem->item_origem_id,
                                    ];
                                    $j++;
                                } else {
                                    $pagamentos[$i][$j] = [
                                        'dsp_processo_id' => $fatura->dsp_processo_id,
                                        'dsp_fatura_provisoria_id' => $recebimentoItem->receita->dsp_fataura_provisoria_id,
                                        'dsp_person_id' => $modelFaturaProvisoriaItem->item->dsp_person_id,
                                        'dsp_item_id' => $modelFaturaProvisoriaItem->item->id,
                                        'valor' => $modelFaturaProvisoriaItem->valor,
                                        'item_origem_id' => $modelFaturaProvisoriaItem->item_origem_id,
                                    ];
                                    $j++;
                                }
                            }

                            foreach ($pagamentos as $key => $data) {
                                if ($key != 1) {
                                     $despesaOld = Despesa::find()
                                                ->where([
                                                    'dsp_processo_id' => $fatura->dsp_processo_id,
                                                    'dsp_person_id' => $key,
                                                    'dsp_fatura_provisoria_id' => $fatura->id
                                                ])
                                                ->andWhere(['status' => 1])
                                                ->one();
												
                                                                  

                                    $despesaNumero = 'PR' . $fatura->processo->numero . '/' . $fatura->processo->bas_ano_id .(($receita->fin_receita_tipo_id == Receita::FATURA_PROVISORIA )? '|FP' : '|FD' ). $fatura->numero . '/' . $fatura->bas_ano_id . '|CF' . $key;
									
									$despesaOldTwo = Despesa::find()
                                        ->where(['numero' => $despesaNumero])
                                        ->andWhere(['status' => 1])
                                        ->one(); 
                                    
                                    $print = ['fatura' => $despesaOldTwo, 'dsp' => $despesaOld];
                                    if (empty($despesaOld) && empty($despesaOldTwo)) {
                                        $despesa = new Despesa();
                                        $despesa->dsp_processo_id = $fatura->dsp_processo_id;
                                        $despesa->data = $model->data;
                                        $despesa->fin_recebimento_id = $model->id;
                                        $despesa->dsp_fatura_provisoria_id = $fatura->id;
                                        $despesa->dsp_person_id = $key;
                                        $despesa->numero = $despesaNumero;
                                        $despesa->valor = 0;
                                        $despesa->valor_pago = 0;
                                        $despesa->saldo = 0;
                                        $despesa->bas_ano_id =$fatura->bas_ano_id;
                                        $despesa->recebido = 1;
                                        $despesa->descricao = 'Despesa gerada ' .($receita->fin_receita_tipo_id == Receita::FATURA_PROVISORIA ? '|FP nº' : 'FD nº ') .$fatura->numero . '/' . $fatura->bas_ano_id;

                                        if ($despesa->save()) {
                                            $ok = false;
                                            foreach ($data as $id => $value) {
                                                if ($value['item_origem_id'] != 'D') {
                                                    $ok = true;
                                                    $despesaItem = new DespesaItem();
                                                    $despesaItem->fin_despesa_id = $despesa->id;
                                                    $despesaItem->dsp_fatura_provisoria_id = $fatura->id;
                                                    $despesaItem->item_id = $value['dsp_item_id'];
                                                    $despesaItem->valor = $value['valor'];
                                                    $despesaItem->valor_iva = 0;
                                                    $despesaItem->cnt_plano_terceiro_id = $fatura->dsp_person_id;
                                                    if (!($flag = $despesaItem->save())) {
														Yii::$app->getSession()->setFlash('error', Html::errorSummary($despesaItem, ['encode' => false]));
                                                        $transaction->rollBack();
                                                        break;
                                                    }
                                                }
                                            }
                                            if (!$ok) {
                                                $despesa->delete();
                                            }
                                        }
                                    }
                                }
                            }
                        } // end  foreacch item


                        // caixa transação
                        $caixaTransacao = new CaixaTransacao();
                        $caixaTransacao->fin_recebimento_id =  $model->id;
                        $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacao->descricao = 'Recebimento nº ' . $model->numero;
                        $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id);
                        $caixaTransacao->valor_entrada = $model->valor;
                        $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id);
                        $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::RECEBIMENTO;
                        $caixaTransacao->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacao->numero_documento = $model->numero_documento;
                        $caixaTransacao->data_documento = $model->data_documento;
                        $caixaTransacao->data = $model->data;
                        if (!($flag = $caixaTransacao->save())) {
							Yii::$app->getSession()->setFlash('error', Html::errorSummary($caixaTransacao, ['encode' => false]));
                            $transaction->rollBack();
                        }

                        if (!($flag = $model->save())) {
							Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['encode' => false]));
                            $transaction->rollBack();
                        }
                    }else{
						Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['encode' => false]));
					}

                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                  } catch (Exception $e) {
					  Yii::$app->getSession()->setFlash('error', $e);
                      $transaction->rollBack();
                  }
            }
        }




        return $this->render('create', [
            'modelsReceita' => $modelsReceita,
            'model' => $model,
        ]);
    }



    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdiantamento()
    {

        $model = new Recebimento();
        $model->data = date('Y-m-d');
        $model->fin_recebimento_tipo_id = RecebimentoTipo::ADIANTAMENTO;
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::RECEBIMENTO_ID);
        $model->numero = $documentoNumero->getNexNumber();
        $modelsRecebimentoItem = [new RecebimentoItem];


        if ($model->load(Yii::$app->request->post())) {
            $model->bas_ano_id = substr(Yii::$app->formatter->asDate($model->data, 'Y'), -2);
            $modelsRecebimentoItem = Model::createMultiple(RecebimentoItem::classname(), $modelsRecebimentoItem);
            Model::loadMultiple($modelsRecebimentoItem, Yii::$app->request->post());

            $valid = $model->validate();
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {
                        $despesa = new Despesa();
                        $despesa->of_acount = 1;
                        $despesa->data = $model->data;
                        $despesa->fin_recebimento_id = $model->id;
                        $despesa->dsp_person_id = $model->dsp_person_id;
                        $despesa->numero = 'AD' . $model->numero . '/' . $model->bas_ano_id . '|CF' . $model->dsp_person_id;
                        $despesa->valor = 0;
                        $despesa->valor_pago = 0;
                        $despesa->saldo = 0;
                        $despesa->bas_ano_id = $model->bas_ano_id;
                        $despesa->recebido = 1;
                        $despesa->descricao = 'Despesa gerada apartir do adiantamento.';

                        if (!($flag = $despesa->save())) {
                            $transaction->rollBack();
                        }
                        $despesaItem = new DespesaItem();
                        $despesaItem->fin_despesa_id = $despesa->id;
                        $despesaItem->item_id = 1066;
                        $despesaItem->cnt_plano_terceiro_id = $model->dsp_person_id;
                        $despesaItem->valor = $model->valor;
                        if (!($flag = $despesaItem->save())) {
                            $transaction->rollBack();
                        }

                        $caixaTransacao = new CaixaTransacao();
                        $caixaTransacao->fin_recebimento_id =  $model->id;
                        $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacao->descricao = 'Recebimento nº ' . $model->numero;
                        $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id);
                        $caixaTransacao->valor_entrada = $model->valor;
                        $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id);
                        $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::RECEBIMENTO;
                        $caixaTransacao->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacao->numero_documento = $model->numero_documento;
                        $caixaTransacao->data_documento = $model->data_documento;
                        $caixaTransacao->data = $model->data;
                        if (!($flag = $caixaTransacao->save())) {
                            $transaction->rollBack();
                        }
                    }

                    if ($flag) {
                        if ($model->numero == $documentoNumero->getNexNumber()) {
                        }
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->render('adiantamento', [
            'model' => $model,

        ]);
    }

    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCriar()
    {

        $model = new Recebimento();
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->fin_recebimento_tipo_id = RecebimentoTipo::TESOURARIA;
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::RECEBIMENTO_ID);
        $model->numero = $documentoNumero->getNexNumber();
        $modelsRecebimentoItem = [new RecebimentoItem];

        if ($model->load(Yii::$app->request->post())) {
            $model->bas_ano_id = substr(Yii::$app->formatter->asDate($model->data, 'Y'), -2);

            $modelsRecebimentoItem = Model::createMultiple(RecebimentoItem::classname(), $modelsRecebimentoItem);
            Model::loadMultiple($modelsRecebimentoItem, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsRecebimentoItem),
                    ActiveForm::validate($model)
                );
            }
            $valid = $model->validate();
            //$valid = Model::validateMultiple($modelsRecebimentoItem) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {
                        foreach ($modelsRecebimentoItem as $modelRecebimento) {
                            #$print_r($model->id);die();

                            $recebimentoItem = new RecebimentoItem();
                            $recebimentoItem->fin_recebimento_id = $model->id;
                            $recebimentoItem->valor = $modelRecebimento->valor;
                            $recebimentoItem->valor_recebido = $modelRecebimento->valor;
                            $recebimentoItem->saldo = 0;
                            $recebimentoItem->dsp_item_id = $modelRecebimento->dsp_item_id;
                            $recebimentoItem->descricao_item = Item::findOne($modelRecebimento->dsp_item_id)->descricao;
                            $recebimentoItem->status = 1;
                            $model->valor = $model->valor +  $modelRecebimento->valor;

                            if (!($flag = $recebimentoItem->save(FALSE))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if (!($flag = $model->save())) {
                            $transaction->rollBack();
                        }



                        $caixaTransacao = new CaixaTransacao();
                        $caixaTransacao->fin_recebimento_id =  $model->id;
                        $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacao->descricao = 'Recebimento nº ' . $model->numero;
                        $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id);
                        $caixaTransacao->valor_entrada = $model->valor;
                        $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id);
                        $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::RECEBIMENTO;
                        $caixaTransacao->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacao->numero_documento = $model->numero_documento;
                        $caixaTransacao->data_documento = $model->data_documento;
                        $caixaTransacao->data = $model->data;
                        if (!($flag = $caixaTransacao->save())) {
                            $transaction->rollBack();
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




        return $this->render('criar', [
            'model' => $model,
            'modelsRecebimentoItem' => (empty($modelsRecebimentoItem)) ? [new RecebimentoItem] : $modelsRecebimentoItem,

        ]);
    }




    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionReembolso()
    {

        $model = new Recebimento();
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->fin_recebimento_tipo_id = RecebimentoTipo::REEMBOLSO;

        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::RECEBIMENTO_ID);
        $model->numero = $documentoNumero->getNexNumber();

        $modelsRecebimentoItem = [new RecebimentoItem];



        if ($model->load(Yii::$app->request->post())) {
            $model->valor = 0;
            $model->bas_ano_id = substr(Yii::$app->formatter->asDate($model->data, 'Y'), -2);
            $modelsRecebimentoItem = Model::createMultiple(RecebimentoItem::classname(), $modelsRecebimentoItem);
            Model::loadMultiple($modelsRecebimentoItem, Yii::$app->request->post());

            $valid = $model->validate();
            if ($valid) {
                try {
                    $transaction = \Yii::$app->db->beginTransaction();

                    if ($flag = $model->save()) {
                        foreach ($modelsRecebimentoItem as $modelRecebimento) {
                            $recebimentoItem = new RecebimentoItem();
                            $recebimentoItem->fin_recebimento_id = $model->id;
                            $recebimentoItem->valor = $modelRecebimento->valor;
                            $recebimentoItem->valor_recebido = $modelRecebimento->valor;
                            $recebimentoItem->saldo = 0;
                            $recebimentoItem->dsp_item_id = $modelRecebimento->dsp_item_id;
                            $recebimentoItem->descricao_item = Item::findOne($modelRecebimento->dsp_item_id)->descricao;
                            $recebimentoItem->status = 1;
                            $recebimentoItem->dsp_person_id = $model->dsp_person_id;
                            $model->valor = $model->valor +  $modelRecebimento->valor;

                            if (!($flag = $recebimentoItem->save())) {
                                print_r($recebimentoItem->errors);
                                die();
                                $transaction->rollBack();
                                break;
                            }
                        }


                        $caixaTransacao = new CaixaTransacao();
                        $caixaTransacao->fin_recebimento_id =  $model->id;
                        $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
                        $caixaTransacao->descricao = 'Recebimento nº ' . $model->numero;
                        $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($model->fin_banco_conta_id);
                        $caixaTransacao->valor_entrada = $model->valor;
                        $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($model->fin_banco_conta_id);
                        $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::RECEBIMENTO;
                        $caixaTransacao->fin_documento_pagamento_id = $model->fin_documento_pagamento_id;
                        $caixaTransacao->numero_documento = $model->numero_documento;
                        $caixaTransacao->data_documento = $model->data_documento;
                        $caixaTransacao->data = $model->data;
                        if (!($flag = $caixaTransacao->save())) {
                            $transaction->rollBack();
                        }



                        if (!($flag = $model->save())) {
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

        return $this->render('reembolso', [
            'model' => $model,
            'modelsRecebimentoItem' => (empty($modelsRecebimentoItem)) ? [new RecebimentoItem] : $modelsRecebimentoItem,

        ]);
    }





    /**
     * Updates an existing Recebimento model.
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
     * Deletes an existing Recebimento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUndo($id)
    {
        $model = $this->findModel($id); 


        if ($model->status == 0) {
            Yii::$app->getSession()->setFlash('warning', 'ESTE RECEBIMENTO JÁ FOI ANULADO.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        // $documento_id = 0;
        if ($model->fin_recebimento_tipo_id = RecebimentoTipo::CONTA_CORENTE) {
            $documento_id = \app\modules\cnt\models\Documento::RECEBIMENTO_FATURA_PROVISORIA;
        } elseif ($model->fin_recebimento_tipo_id = RecebimentoTipo::ADIANTAMENTO) {
            $documento_id = \app\modules\cnt\models\Documento::RECEBIMENTO_ADIANTAMENTO;
        } elseif ($model->fin_recebimento_tipo_id = RecebimentoTipo::TESOURARIA) {
            $documento_id = \app\modules\cnt\models\Documento::RECEBIMENTO_TESOURARIO;
        } elseif ($model->fin_recebimento_tipo_id = RecebimentoTipo::REEMBOLSO) {
            $documento_id = \app\modules\cnt\models\Documento::REEMBOLSO;
        }

        if (Yii::$app->CntQuery->inContabilidadeAtive($documento_id, $id)) {
            Yii::$app->getSession()->setFlash('warning', 'Esta recebimento já se encontra na Contabilidade anule na Contabilidade para poder proceguir.');
            return $this->redirect(['view', 'id' => $id]);
        }



        if (($caixaTransacao =  CaixaTransacao::findOne(['fin_recebimento_id' => $model->id])) != NULL) {
            $caixa = Caixa::findOne($caixaTransacao->fin_caixa_id);
            if ($caixa->status == Caixa::CLOSE_CAIXA) {
                Yii::$app->getSession()->setFlash('warning', 'A caixa ' . $caixa->descricao . ' se encontra fechado.');
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                if (!($flag = $caixaTransacao->delete())) {
                    // $transaction->rollBack();
                }
            }
        } 

        // $despesaPago = false;
        foreach ($model->despesa as $key => $despesa) {
            if (DespesaService::temPagamento($despesa->id)) {
                Yii::$app->getSession()->setFlash('warning', 'DESPESAS GERADA POR ESTE RECEBIMENTO JÁ TEM ORDEM DE PAGAMENTO OU FOI PAGO ANULE PRIMEIRO OS PAGAMENTO E A ORDEM DE PAGEMTO.');
                return $this->redirect(['view', 'id' => $id]);
            }
        }
        // anular despesa
        foreach ($model->despesa as $key => $despesa) {
            $despesa->status = 0;
            // $despesa->numero = $despesa->numero.'-Anulado';
            $despesa->save(false);
        }




        foreach ($model->recebimentoItem as $key => $value) {
            if (($receita = Receita::findOne($value->fin_receita_id)) != null) {
                $receita->valor_recebido = $receita->valor_recebido - $value->valor_recebido;
                $receita->saldo = $receita->saldo + $value->valor_recebido;
                $receita->save();
            }
        }
        $model->status = 0;
        if ($model->save(false)) {



            Yii::$app->getSession()->setFlash('success', 'RECEBIMENTO ANULADO COM SUCESSO.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'ERRO AO EFETUAR A OPERAÇÂO.');
        }


        return $this->redirect(['view', 'id' => $model->id]);
    }


    /**
     * Finds the Recebimento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Recebimento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Recebimento::findOne($id)) !== null) {
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
    public function actionValidarPerson(array $keys)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $isValid = 1;
        $personId = 0;
        $valor = 0;
        foreach ($keys as $key => $value) {
            $receita = Receita::find()
                ->where(['id' => $value])
                ->one();
            if (!empty($receita)) {
                $valor = $valor + $receita->saldo;
                if ($personId == 0) {
                    $personId = $receita->dsp_person_id;
                } elseif ($personId != $receita->dsp_person_id) {
                    $isValid = 0;
                }
            }
        }
        return [
            'isValid' => $isValid,
            'personId' => $personId,
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

        ]);
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        // return the pdf output as per the destination setting
        return $pdf->render();
    }





    public function actionViewHistoricoPdf($id)
    {

        $model = $this->findModel($id);
        // $modelsFaturaProvisoriaItem = $model->faturaProvisoriaItem;
        // $faturaProvisoria = $model->faturaProvisoria;
        // $faturaProvisoriaRegimeItem = $faturaProvisoria->faturaProvisoriaRegimeItem;

        $company = Yii::$app->params['company'];

        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('view-historico-pdf', [
            'model' => $model,
            'company' => $company,
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

        ]);
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        // return the pdf output as per the destination setting
        return $pdf->render();
    }





    public function actionReportpdf($bas_ano_id = null, $dsp_person_id = null, $data = null)
    {

        $searchModel = new RecebimentoSearch(['bas_ano_id' => $bas_ano_id, 'dsp_person_id' => $dsp_person_id, 'data' => $data]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $company = Yii::$app->params['company'];


        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('reportpdf', [
            // 'model' => $model,
            'searchModel' => $searchModel,
            'company' => $company,
            'dataProvider' => $dataProvider,
        ]);


        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Fatura provisoria'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => [''],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);



        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionFileBrowser($fin_recebimento_id)
    {
        $model = new \app\models\FileBrowser();
        $model->path = \app\components\helpers\UploadFileHelper::setUrlFileRecebimento($fin_recebimento_id);

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
