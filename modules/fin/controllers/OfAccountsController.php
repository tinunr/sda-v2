<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\dsp\models\Person;
use app\modules\fin\models\OfAccounts;
use app\modules\fin\models\OfAccountsSearch;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaItem;
use app\modules\fin\models\DespesaSearch;
use app\modules\fin\models\Receita;
use app\modules\fin\models\ReceitaSearch;
use app\modules\fin\models\OfAccountsItem;
use app\modules\fin\models\BancoTransacao;
use app\modules\fin\models\BancoTransacaoTipo;
use app\modules\fin\models\CaixaTransacao;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\models\Model;
use kartik\mpdf\Pdf;
use app\modules\fin\models\NotaDebito;

/**
 * EdeficiosController implements the CRUD actions for OfAccounts model.
 */
class OfAccountsController extends Controller
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
                        'actions' => ['validar-person', 'undo', 'report'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'view-pdf', 'create-a', 'create-two', 'create-b', 'create', 'undo', 'report', 'create-c', 'create-tree'],
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
                ],
            ],
        ];
    }

    /**
     * Lists all OfAccounts models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OfAccountsSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OfAccounts model.
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
     * Creates a new OfAccounts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateA()
    {
        $receitaSearchModel = new ReceitaSearch();
        // $receitaSearchModel->bas_ano_id = substr(date('Y'),-2);       
        $receitaDataProvider = $receitaSearchModel->searchCreate(Yii::$app->request->queryParams);

        return $this->render('create-a', [
            'searchModel' => $receitaSearchModel,
            'receitaDataProvider' => $receitaDataProvider,
        ]);
    }

    /**
     * Creates a new OfAccounts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateB()
    {
        $searchModel = new DespesaSearch();
        // $searchModel->bas_ano_id = substr(date('Y'),-2);       
        $dataProvider = $searchModel->searchCreateOf(Yii::$app->request->queryParams);

        return $this->render('create-b', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new OfAccounts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new OfAccounts();
        $model->status = 1;
        // $model->bas_ano_id = substr(date('Y'),-2);
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->descricao = 'Encontro de conta da(s) despesa(s) nº';

        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::OFACCOUNTS_ID);
        $model->numero = $documentoNumero->getNexNumber();
        $selection = (array)Yii::$app->request->post('selection');
        $modelsReceita = Receita::findAll(['id' => $selection]);
        if (!empty($modelsReceita)) {
            foreach ($modelsReceita as $key => $value) {
                $model->fin_receita_id = $value->id;
                $model->dsp_person_id = $value->dsp_person_id;
                $model->descricao = $value->descricao;
                $model->valor = $value->saldo;
            }
        }

        $modelsDespesa = Despesa::find()
            ->where(['dsp_person_id' => $model->dsp_person_id])
            ->andWhere(['>', 'saldo', 0])
            ->andWhere(['of_acount' => 1])
            ->all();

        if (!$model->load(Yii::$app->request->post())) {
            if (empty($modelsDespesa)) {
                Yii::$app->getSession()->setFlash('warning', 'Nenhum Aviso de credito foi encontado para este cliente');
                return $this->redirect(['create-a']);
            }
        }


        if ($model->load(Yii::$app->request->post())) {

            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);
            $modelsDespesa = Model::createMultiple(Despesa::classname(), $modelsDespesa);
            Model::loadMultiple($modelsDespesa, Yii::$app->request->post());
            $valid = $model->validate();

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $model->save()) {


                        $modelsReceitaUp = Receita::findOne(['id' => $model->fin_receita_id]);
                        $modelsReceitaUp->valor_recebido = $modelsReceitaUp->valor_recebido + $model->valor;
                        $modelsReceitaUp->saldo = $modelsReceitaUp->saldo - $model->valor;
                        if (!($flag = $modelsReceitaUp->save(false))) {
                            $transaction->rollBack();
                        }


                        // gerar despesa


                        if (1 == 1) {
                            $pagamentos = [];
                            $i = 0;
                            $j = 0;
                            // $receita = Receita::findOne(['id'=>$fin_receita_id]);
                            $faturaProvisoriaItems = $modelsReceitaUp->faturaProvisoria->faturaProvisoriaItem; // item da fatura provisoria
                            // preparar as despsas por fornecidor
                            foreach ($faturaProvisoriaItems as $key => $modelFaturaProvisoriaItem) {
                                $i = $modelFaturaProvisoriaItem->item->dsp_person_id;
                                if (array_key_exists($i, $pagamentos)) {
                                    $pagamentos[$i][$j] =   [
                                        'dsp_processo_id' => $modelsReceitaUp->faturaProvisoria->dsp_processo_id,
                                        'dsp_fatura_provisoria_id' => $modelsReceitaUp->dsp_fataura_provisoria_id,
                                        'dsp_person_id' => $modelFaturaProvisoriaItem->item->dsp_person_id,
                                        'dsp_item_id' => $modelFaturaProvisoriaItem->item->id,
                                        'valor' => $modelFaturaProvisoriaItem->valor,
                                        'item_origem_id' => $modelFaturaProvisoriaItem->item_origem_id,
                                    ];
                                    $j++;
                                } else {
                                    $pagamentos[$i][$j] = [
                                        'dsp_processo_id' => $modelsReceitaUp->faturaProvisoria->dsp_processo_id,
                                        'dsp_fatura_provisoria_id' => $modelsReceitaUp->dsp_fataura_provisoria_id,
                                        'dsp_person_id' => $modelFaturaProvisoriaItem->item->dsp_person_id,
                                        'dsp_item_id' => $modelFaturaProvisoriaItem->item->id,
                                        'valor' => $modelFaturaProvisoriaItem->valor,
                                        'item_origem_id' => $modelFaturaProvisoriaItem->item_origem_id,
                                    ];
                                    $j++;
                                }
                            } // end

                            // criar as despesas 
                            foreach ($pagamentos as $key => $data) {
                                // vereficar se é despesa do cliente se 1 é honorário
                                if ($key != 1) {
                                    $despesaOld = Despesa::find()
                                        ->where([
                                            'dsp_processo_id' => $modelsReceitaUp->faturaProvisoria->dsp_processo_id,
                                            'dsp_person_id' => $key,
                                            'dsp_fatura_provisoria_id' => $modelsReceitaUp->faturaProvisoria->id
                                        ])
                                        ->andWhere(['status' => 1])
                                        ->one();
                                    if (empty($despesaOld) && !Yii::$app->FinQuery->checkDespesaFaturaProcesso($modelsReceitaUp->faturaProvisoria->dsp_processo_id)) {
                                        $despesaNumero = 'PR' . $modelsReceitaUp->faturaProvisoria->processo->numero . '/' . $modelsReceitaUp->faturaProvisoria->bas_ano_id . '|FP' . $modelsReceitaUp->faturaProvisoria->numero . '/' . $modelsReceitaUp->faturaProvisoria->bas_ano_id . '|CF' . $key; //numero da despesa
                                        $despesa = new Despesa();
                                        $despesa->dsp_processo_id = $modelsReceitaUp->faturaProvisoria->dsp_processo_id;
                                        $despesa->data = $model->data;
                                        $despesa->fin_of_accounts_id = $model->id;
                                        $despesa->dsp_fatura_provisoria_id = $modelsReceitaUp->dsp_fataura_provisoria_id;
                                        $despesa->dsp_person_id = $key;
                                        $despesa->numero = $despesaNumero;
                                        $despesa->valor = 0;
                                        $despesa->valor_pago = 0;
                                        $despesa->saldo = 0;
                                        $despesa->bas_ano_id = $modelsReceitaUp->faturaProvisoria->bas_ano_id;
                                        $despesa->recebido = 1;
                                        $despesa->descricao = 'Despesa gerada FP nº ' . $modelsReceitaUp->faturaProvisoria->numero . '/' . $modelsReceitaUp->faturaProvisoria->bas_ano_id;
                                        if (!($flag = $despesa->save())) {
                                            Yii::$app->getSession()->setFlash('errr', 'HOUVE UM ERRO AO GERAR A DESPESA.');
                                            $transaction->rollBack();
                                            break;
                                        }
                                        // gerar item das despesas
                                        $ok = false;
                                        foreach ($data as $id => $value) {
                                            // ignorar item gerada a partir das despesas
                                            if ($value['item_origem_id'] != 'D') {
                                                $ok = true;
                                                $despesaItem = new DespesaItem();
                                                $despesaItem->fin_despesa_id = $despesa->id;
                                                $despesaItem->dsp_fatura_provisoria_id = $modelsReceitaUp->dsp_fataura_provisoria_id;
                                                $despesaItem->item_id = $value['dsp_item_id'];
                                                $despesaItem->valor = $value['valor'];
                                                $despesaItem->valor_iva = 0;
                                                $despesaItem->cnt_plano_terceiro_id = $modelsReceitaUp->faturaProvisoria->dsp_person_id;
                                                if (!($flag = $despesaItem->save())) {
                                                    Yii::$app->getSession()->setFlash('errr', 'HOUVE UM ERRO AO GERAR A ITEM DA DESPESA.');
                                                    $transaction->rollBack();
                                                    break;
                                                }
                                            }
                                        } // end  
                                        if (!$ok) {
                                            $despesa->delete();
                                        }
                                    } // end                 
                                } // end 
                            } // end foreach

                        } // end if  criar despea

                        foreach ($modelsDespesa as $modelDespesa) {
                            if ($modelDespesa->valor_pago > 0) {
                                $item = new OfAccountsItem();
                                $item->fin_of_account_id = $model->id;
                                $item->fin_despesa_id = $modelDespesa->id;
                                $item->valor = $modelDespesa->valor_pago;
                                if (!($flag = $item->save())) {
                                    $transaction->rollBack();
                                    break;
                                }

                                $modelDespesaUp = Despesa::findOne(['id' => $modelDespesa->id]);
                                $modelDespesaUp->valor_pago = $modelDespesaUp->valor_pago + $modelDespesa->valor_pago;
                                $modelDespesaUp->saldo = $modelDespesa->saldo;
                                if (!($flag = $modelDespesaUp->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }

                        if ($flag) {
                            if ($model->numero == $documentoNumero->getNexNumber()) {
                                $documentoNumero->saveNexNumber();
                            }
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
     * Creates a new OfAccounts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateTwo()
    {

        $model = new OfAccounts();
        $model->status = 1;
        $model->bas_ano_id = substr(date('Y'), -2);
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->descricao = 'Encontro de conta da(s) despesa(s) nº ';

        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::OFACCOUNTS_ID);
        $model->numero = $documentoNumero->getNexNumber();
        $selection = (array)Yii::$app->request->post('selection');
        $modlesDespesa = Despesa::findAll(['id' => $selection]);
        if (!empty($modlesDespesa)) {
            foreach ($modlesDespesa as $key => $value) {
                $model->fin_despesa_id = $value->id;
                $model->dsp_person_id = $value->dsp_person_id;
                $model->descricao = $model->descricao . $value->numero . '/' . $value->bas_ano_id;
                $model->valor = $value->saldo;
            }
        }

        $modelsReceita = Receita::find()
		    ->joinWith(['faturaProvisoria','faturaDefinitiva']) 
            ->where(['fin_receita.dsp_person_id' => $model->dsp_person_id])
            ->andWhere(['>', 'saldo', 0])
			->andWhere(['or',
				['fin_fatura_provisoria.status' => 1],
				['fin_fatura_definitiva.status' => 1],
			])
			->andWhere(['or',
				['!=','fin_fatura_provisoria.send' , 0],
				['!=','fin_fatura_definitiva.send' , 0],
			])
            ->all();
        
       

        if (!$model->load(Yii::$app->request->post())) {
            if (empty($modelsReceita)) {
                Yii::$app->getSession()->setFlash('warning', 'Nenhuma fatura provisoria emcontado a favor desta clenete.');
                return $this->redirect(['create-b']);
            }
        }


        if ($model->load(Yii::$app->request->post())) {

            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);

            $modelsReceita = Model::createMultiple(Receita::classname(), $modelsReceita);
            Model::loadMultiple($modelsReceita, Yii::$app->request->post());
            $valid = $model->validate();

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $model->save()) {


                        $modelsDespesaUp = Despesa::findOne(['id' => $model->fin_despesa_id]);
                        // print_r($model->fin_despesa_id);die();
                        $modelsDespesaUp->valor_pago = $modelsDespesaUp->valor_pago + $model->valor;
                        $modelsDespesaUp->saldo = $modelsDespesaUp->saldo - $model->valor;
                        if (!($flag = $modelsDespesaUp->save(false))) {
                            $transaction->rollBack();
                        }



                        foreach ($modelsReceita as $modelsReceita) {
                            if ($modelsReceita->valor_recebido > 0) {
                                $item = new OfAccountsItem();
                                $item->fin_of_account_id = $model->id;
                                $item->fin_receita_id = $modelsReceita->id;
                                $item->valor = $modelsReceita->valor_recebido;
                                if (!($flag = $item->save())) {
                                    $transaction->rollBack();
                                    break;
                                }

                                $modelsReceitaUp = Receita::findOne(['id' => $modelsReceita->id]);
                                $modelsReceitaUp->valor_recebido = $modelsReceitaUp->valor_recebido + $modelsReceita->valor_recebido;
                                $modelsReceitaUp->saldo = $modelsReceita->saldo;
                                if (!($flag = $modelsReceitaUp->save())) {
                                    $transaction->rollBack();
                                    break;
                                }



                                /********************** gerar despesas recebimento fatura provisoria ****************/
                                if ($modelsReceitaUp->fin_receita_tipo_id == Receita::FATURA_PROVISORIA) {
                                    $fatura = $modelsReceitaUp->faturaProvisoria;
                                    $faturaItem = $modelsReceitaUp->faturaProvisoria->faturaProvisoriaItem;
                                } elseif ($modelsReceitaUp->fin_receita_tipo_id == Receita::FATURA_DEFINITIVA) {
                                    $fatura = $modelsReceitaUp->faturaDefinitiva;
                                    $faturaItem = $modelsReceitaUp->faturaDefinitiva->faturaDefinitivaItem;
                                }
                                if (1 == 1) {
                                    $pagamentos = [];
                                    $i = 0;
                                    $j = 0;
                                     // item da fatura provisoria
                                    // preparar as despsas por fornecidor
                                    foreach ($faturaItem as $key => $modelFaturaProvisoriaItem) {
                                        $i = $modelFaturaProvisoriaItem->item->dsp_person_id;
                                        if (array_key_exists($i, $pagamentos)) {
                                            $pagamentos[$i][$j] =   [
                                                'dsp_processo_id' => $fatura->dsp_processo_id,
                                                'dsp_fatura_provisoria_id' => $modelsReceitaUp->dsp_fataura_provisoria_id,
                                                'dsp_person_id' => $modelFaturaProvisoriaItem->item->dsp_person_id,
                                                'dsp_item_id' => $modelFaturaProvisoriaItem->item->id,
                                                'valor' => $modelFaturaProvisoriaItem->valor,
                                                'item_origem_id' => $modelFaturaProvisoriaItem->item_origem_id,
                                            ];
                                            $j++;
                                        } else {
                                            $pagamentos[$i][$j] = [
                                                'dsp_processo_id' => $fatura->dsp_processo_id,
                                                'dsp_fatura_provisoria_id' => $modelsReceitaUp->dsp_fataura_provisoria_id,
                                                'dsp_person_id' => $modelFaturaProvisoriaItem->item->dsp_person_id,
                                                'dsp_item_id' => $modelFaturaProvisoriaItem->item->id,
                                                'valor' => $modelFaturaProvisoriaItem->valor,
                                                'item_origem_id' => $modelFaturaProvisoriaItem->item_origem_id,
                                            ];
                                            $j++;
                                        }
                                    } // end

                                    // criar as despesas 
                                    foreach ($pagamentos as $key => $data) {
                                        // vereficar se é despesa do cliente se 1 é honorário
                                        if ($key != 1) {
                                           
                                                    $despesaOld = Despesa::find()
                                                            ->where([
                                                                'dsp_processo_id' => $fatura->dsp_processo_id,
                                                                'dsp_person_id' => $key,
                                                                'dsp_fatura_provisoria_id' => $fatura->id
                                                            ])
                                                            ->andWhere(['status' => 1])
                                                            ->one();
                                                
                                            
                                                $despesaNumero = 'PR' . $fatura->processo->numero . '/' . $fatura->bas_ano_id . ($modelsReceitaUp->fin_receita_tipo_id == Receita::FATURA_PROVISORIA?'|FP':'|FD') . $fatura->numero . '/' . $fatura->bas_ano_id . '|CF' . $key; 
                                                
                                                //numero da despesa
                                                $despesaOldTwo = Despesa::find()
                                                    ->where(['numero' => $despesaNumero])
                                                    ->andWhere(['status' => 1])
                                                    ->one();
                                            if (empty($despesaOld) && empty($despesaOldTwo)&& !Yii::$app->FinQuery->checkDespesaFaturaProcesso($fatura->dsp_processo_id)) {
                                                
                                                $despesa = new Despesa();
                                                $despesa->dsp_processo_id = $fatura->dsp_processo_id;
                                                $despesa->data = $model->data;
                                                $despesa->fin_of_account_id = $model->id;
                                                $despesa->dsp_fatura_provisoria_id = $modelsReceitaUp->dsp_fataura_provisoria_id;
                                                $despesa->dsp_person_id = $key;
                                                $despesa->numero = $despesaNumero;
                                                $despesa->valor = 0;
                                                $despesa->valor_pago = 0;
                                                $despesa->saldo = 0;
                                                $despesa->bas_ano_id = $fatura->bas_ano_id;
                                                $despesa->recebido = 1;
                                                $despesa->descricao = 'Despesa gerada  '.($modelsReceitaUp->fin_receita_tipo_id == Receita::FATURA_PROVISORIA?'|FP':'|FD').' nº ' . $fatura->numero . '/' . $fatura->bas_ano_id;
                                                if (!($flag = $despesa->save())) {
                                                    Yii::$app->getSession()->setFlash('errr', 'HOUVE UM ERRO AO GERAR A DESPESA.');
                                                    $transaction->rollBack();
                                                    break;
                                                }
                                                // gerar item das despesas
                                                $ok = false;
                                                foreach ($data as $id => $value) {
                                                    // ignorar item gerada a partir das despesas
                                                    if ($value['item_origem_id'] != 'D') {
                                                        $ok = true;
                                                        $despesaItem = new DespesaItem();
                                                        $despesaItem->fin_despesa_id = $despesa->id;
                                                        $despesaItem->dsp_fatura_provisoria_id = $modelsReceitaUp->dsp_fataura_provisoria_id;
                                                        $despesaItem->item_id = $value['dsp_item_id'];
                                                        $despesaItem->valor = $value['valor'];
                                                        $despesaItem->valor_iva = 0;
                                                        $despesaItem->cnt_plano_terceiro_id = $fatura->dsp_person_id;
                                                        if (!($flag = $despesaItem->save())) {
                                                            Yii::$app->getSession()->setFlash('errr', 'HOUVE UM ERRO AO GERAR A ITEM DA DESPESA.');
                                                            $transaction->rollBack();
                                                            break;
                                                        }
                                                    }
                                                } // end    
                                                if (!$ok) {
                                                    $despesa->delete();
                                                }
                                            } // end 
                                        } // end 
                                    } // end foreach

                                }
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




        return $this->render('_formb', [
            'modelsReceita' => $modelsReceita,
            'model' => $model,
        ]);
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
        foreach ($keys as $key => $value) {
            $receita = Receita::find()
                ->where(['id' => $value])
                ->one();
            if (!empty($receita)) {
                if ($personId == 0) {
                    $personId = $receita->dsp_person_id;
                } elseif ($personId != $receita->dsp_person_id) {
                    $isValid = 0;
                }
            }
        }
        return $isValid;
    }

    /**
     * Deletes an existing OfAccounts model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUndo($id)
    {
        $model = $this->findModel($id);

        $transaction = \Yii::$app->db->beginTransaction();
        $flag = true;
        $model->status = 0;
        if (!($flag = $model->save())) {
            $transaction->rollBack();
        }

        if (!empty($model->fin_receita_id)) {
            $modelsReceitaUp = Receita::findOne(['id' => $model->fin_receita_id]);
            $modelsReceitaUp->valor_recebido = $modelsReceitaUp->valor_recebido - $model->valor;
            $modelsReceitaUp->saldo = $modelsReceitaUp->saldo + $model->valor;
            if (!($flag = $modelsReceitaUp->save())) {
                $transaction->rollBack();
            }

            foreach ($model->item as $item) {

                $modelDespesaUp = Despesa::findOne(['id' => $item->fin_despesa_id]);
                $modelDespesaUp->valor_pago = $modelDespesaUp->valor_pago - $item->valor;
                $modelDespesaUp->saldo = $modelDespesaUp->saldo + $item->valor;
                if (!($flag = $modelDespesaUp->save())) {
                    $transaction->rollBack();
                    break;
                }
            }
        } elseif (!empty($model->fin_despesa_id)) {
            $modelDespesaUp = Despesa::findOne(['id' => $model->fin_despesa_id]);
            $modelDespesaUp->valor_pago = $modelDespesaUp->valor_pago - $model->valor;
            $modelDespesaUp->saldo = $modelDespesaUp->saldo + $model->valor;
            if (!($flag = $modelDespesaUp->save())) {
                $transaction->rollBack();
            }

            foreach ($model->item as $item) {
                $modelsReceitaUp = Receita::findOne(['id' => $item->fin_receita_id]);
                $modelsReceitaUp->valor_recebido = $modelsReceitaUp->valor_recebido - $item->valor;
                $modelsReceitaUp->saldo = $modelsReceitaUp->saldo + $item->valor;
                if (!($flag = $modelsReceitaUp->save())) {
                    $transaction->rollBack();
                    break;
                }
            }
        }

        if ($flag) {
            $transaction->commit();

            Yii::$app->getSession()->setFlash('success', 'ENCONTRO DE CONTA ANULADO COM SUCESSO.');
        } else {
            $transaction->rollBack();
            // print_r($model->errors);die();
            Yii::$app->getSession()->setFlash('error', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the OfAccounts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OfAccounts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OfAccounts::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionViewPdf($id)
    {
        $company = Yii::$app->params['company'];
        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('view-pdf', [
            'model' => $this->findModel($id),
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
            'options' => ['title' => 'Relatório Processo'],

        ]);
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];


        // return the pdf output as per the destination setting
        return $pdf->render();
    }



    /**
     * Creates a new OfAccounts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateC()
    {
        $searchModel = new DespesaSearch();
        $dataProvider = $searchModel->searchCreateC(Yii::$app->request->queryParams);
        return $this->render('create-c', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    /**
     * Creates a new OfAccounts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateTree()
    {

        $model = new OfAccounts();
        // $modelsNotaDebito = [new NotaDebito];

        $model->status = 1;
        $model->bas_ano_id = substr(date('Y'), -2);
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->descricao = 'Encontro de conta da(s) despesa(s) nº ';

        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::OFACCOUNTS_ID);
        $model->numero = $documentoNumero->getNexNumber();
        $selection = (array)Yii::$app->request->post('selection');
        $modlesDespesa = Despesa::findAll(['id' => $selection]);
        if (!empty($modlesDespesa)) {
            foreach ($modlesDespesa as $key => $value) {
                $model->fin_despesa_id = $value->id;
                $model->dsp_person_id = $value->dsp_person_id;
                $model->descricao = $model->descricao . $value->numero . '/' . $value->bas_ano_id;
                $model->valor = $value->saldo;
            }
        }

        $modelsNotaDebito = NotaDebito::find()
            ->where(['dsp_person_id' => $model->dsp_person_id])
            ->andWhere(['>', 'saldo', 0])
            ->andWhere(['status' => 1])
            ->all();
			
			if(empty($modelsNotaDebito)){
				\Yii::$app->getSession()->setFlash('warning', 'Nenhum nota de debito encontrado neste fornecedor.');
				return $this->redirect(Yii::$app->request->referrer);				
			}


        if ($model->load(Yii::$app->request->post())) {

            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);

            $modelsNotaDebito = Model::createMultiple(NotaDebito::classname(), $modelsNotaDebito);
            Model::loadMultiple($modelsNotaDebito, Yii::$app->request->post());
            //  print_r($modelsNotaDebito);die();

            $valid = $model->validate();

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $model->save()) {


                        $modelsDespesaUp = Despesa::findOne(['id' => $model->fin_despesa_id]);
                        $modelsDespesaUp->valor_pago = $modelsDespesaUp->valor_pago + $model->valor;
                        $modelsDespesaUp->saldo = $modelsDespesaUp->saldo - $model->valor;
                        if (!($flag = $modelsDespesaUp->save())) {
                            $transaction->rollBack();
                        }


                        foreach ($modelsNotaDebito as $modelNotaDebito) {

                            if ($modelNotaDebito->valor_pago > 0) {
                                $item = new OfAccountsItem();
                                $item->fin_of_account_id = $model->id;
                                $item->fin_nota_debito_id = $modelNotaDebito->id;
                                $item->valor = $modelNotaDebito->valor_pago;

                                if (!($flag = $item->save())) {
                                    $transaction->rollBack();
                                    break;
                                }

                                $modelNotaDebitoUp = NotaDebito::findOne(['id' => $modelNotaDebito->id]);
                                // print_r($modelNotaDebito);die();

                                $modelNotaDebitoUp->valor_pago = $modelNotaDebitoUp->valor_pago + $modelNotaDebito->valor_pago;
                                $modelNotaDebitoUp->saldo = $modelNotaDebito->saldo;
                                if (!($flag = $modelNotaDebitoUp->save())) {
                                    $transaction->rollBack();
                                    break;
                                }
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




        return $this->render('_formc', [
            'modelsNotaDebito' => $modelsNotaDebito,
            'model' => $model,
        ]);
    }
}
