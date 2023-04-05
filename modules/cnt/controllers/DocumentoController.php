<?php


namespace app\modules\cnt\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\helpers\Json;

use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\Documento;
use app\modules\cnt\models\DocumentoSearch;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\Transferencia;
use app\modules\fin\models\Recebimento;
use app\modules\fin\models\Pagamento;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\BancoConta;
use app\modules\fin\models\RecebimentoTipo;

class DocumentoController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['origem','origem-items'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','view','create','update','delete'],
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
                ],
            ],
        ];
    }

    /**
     * Lists all Documento models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Documento model.
     * @param string $item_name
     * @param string $user_id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Documento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Documento();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['index']);
           
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Documento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $item_name
     * @param string $user_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['index']);
           
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Documento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $item_name
     * @param string $user_id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Documento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $item_name
     * @param string $user_id
     * @return Documento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Documento::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



     // THE CONTROLLER
    public function actionOrigem()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $query = new Query;
                if ($parents[0]==Documento::FATURA_DEFINITIVA) {
                    $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                        ->from('fin_fatura_definitiva A')
                        ->where(['A.status'=>1])
                        ->orderBy('A.numero')
                        ->all();
                }

                if ($parents[0]==Documento::MOVIMENTO_INTERNO) {
                    $query = new Query;
                    $query->select(['A.id as id', 'A.numero as name'])
                        ->from('fin_transferencia A')
                        ->where(['A.status'=>1])
                        ->andWhere(['!=','A.fin_banco_conta_id_origem', 8])
                        ->andWhere(['!=','A.fin_banco_conta_id_destino', 8])
                        ->orderBy('A.numero')
                        ->all();
                }



                if ($parents[0]==Documento::DESPESA_FATURA_FORNECEDOR) {
                    $query = new Query;
                    $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                        ->from('fin_despesa A')
                        ->where(['A.status'=>1])
                        ->andWhere(['A.cnt_documento_id'=>Documento::DESPESA_FATURA_FORNECEDOR])
                        ->orderBy('A.numero')
                        ->all();
                }


                if ($parents[0]==Documento::FATURA_FORNECEDOR_INVESTIMENTO) {
                    $query = new Query;
                    $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                        ->from('fin_despesa A')
                        ->where(['A.status'=>1])
                        ->andWhere(['A.cnt_documento_id'=>Documento::FATURA_FORNECEDOR_INVESTIMENTO])
                        ->orderBy('A.numero')
                        ->all();
                }




                if ($parents[0]==Documento::RECEBIMENTO_FATURA_PROVISORIA) {
                    $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                        ->from('fin_recebimento A')
                        ->where(['A.status'=>1])
                        ->andWhere(['A.fin_recebimento_tipo_id'=>RecebimentoTipo::CONTA_CORENTE])
                        ->andWhere(['!=','A.fin_banco_conta_id', 8])
                        ->orderBy('A.numero')
                        ->all();
                }
                if ($parents[0]==Documento::RECEBIMENTO_REEMBOLSO) {
                    $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                        ->from('fin_recebimento A')
                        ->where(['A.status'=>1])
                        ->andWhere(['A.fin_recebimento_tipo_id'=>RecebimentoTipo::REEMBOLSO])
                        ->orderBy('A.numero')
                        ->all();
                }
                if ($parents[0]==Documento::RECEBIMENTO_ADIANTAMENTO) {
                    $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                        ->from('fin_recebimento A')
                        ->where(['A.status'=>1])
                        ->andWhere(['A.fin_recebimento_tipo_id'=>RecebimentoTipo::ADIANTAMENTO])
                        ->andWhere(['!=','A.fin_banco_conta_id', 8])
                        ->orderBy('A.numero')
                        ->all();
                }
                if ($parents[0]==Documento::RECEBIMENTO_TESOURARIO) {
                   $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                        ->from('fin_recebimento A')
                        ->where(['A.status'=>1])
                        ->andWhere(['A.fin_recebimento_tipo_id'=>RecebimentoTipo::TESOURARIA])
                        ->andWhere(['!=','A.fin_banco_conta_id', 8])
                        ->orderBy('A.numero')
                        ->all();
                }

                if ($parents[0]==Documento::PAGAMENTO) {
                    $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
                        ->from('fin_pagamento A')
                        ->where(['A.status'=>1])
                        ->andWhere(['!=','A.fin_banco_conta_id', 8])
                        ->orderBy('A.numero')
                        ->all();
                }

                

                

                


                
                $command = $query->createCommand();
                $out = $command->queryAll();
                return Json::encode(['output'=>$out, 'selected'=>'']);
                
            }
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }




    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionOrigemItems($cnt_documento_id,$documento_origem_id)
     {
         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         $descricao = '';
         $documento = Documento::findOne($cnt_documento_id);
         $planoConta = PlanoConta::findOne($documento->cnt_plano_conta_id);
         $diario = Diario::findOne($cnt_documento_id);
         $data = []; 
         $data_doc =  date('Y-m-d');
         $cnt_diario_id=!empty($diario->id)?$diario->id:null;
         $cnt_diario_descricao = !empty($diario->descricao)?$diario->descricao:null;





        // FATURA_DEFINITIVA
        if ($cnt_documento_id ==Documento::FATURA_DEFINITIVA) {
            $origem = FaturaDefinitiva::findOne($documento_origem_id);
            $descricao =$origem->descricao;
            $data[0]= [
                 'descricao'=>'PR'.$origem->processo->numero.'/'.$origem->processo->bas_ano_id.' '.$documento->codigo.$origem->numero,
                 'cnt_natureza_id'=>$documento->cnt_natureza_id,
                 'cnt_plano_conta_id'=>$documento->cnt_plano_conta_id,
                 'cnt_plano_terceiro_id'=>$planoConta->tem_plano_externo?$origem->dsp_person_id:null,
                 'cnt_plano_iva_id'=>null,
                 'cnt_plano_fluxo_caixa_id'=>$documento->cnt_plano_fluxo_caixa_id?$documento->cnt_plano_fluxo_caixa_id:null,
                 'valor'=>$origem->valor,
             ];
            $query = Yii::$app->CntQuery->listFaruraDefinitivaItems($origem->id);
            foreach ($query as $key=> $item) {
                 if (($planoContaItem = PlanoConta::findOne($item['cnt_plano_conta_id']))!=null) {
                     $data[$item['id']]= [
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'descricao'=>'PR'.$origem->processo->numero.'/'.$origem->processo->bas_ano_id.' '.$documento->codigo.$origem->numero.'-'.$item['descricao'],
                         'cnt_plano_conta_id'=>$item['cnt_plano_conta_id'],
                         'cnt_plano_terceiro_id'=>$planoContaItem->tem_plano_externo?$origem->dsp_person_id:null,
                         'cnt_plano_iva_id'=>$item['cnt_plano_iva_id']?$item['cnt_plano_iva_id']:null,
                         'cnt_plano_fluxo_caixa_id'=>$planoContaItem->tem_plano_fluxo_caixa?1:null,
                         'valor'=>$item['valor'],
                     ];
                 }
             }
        } // END FATURA_DEFINITIVA






        // MOVIMENTO_INTERNO
        if ($cnt_documento_id ==Documento::MOVIMENTO_INTERNO) {
            $origem = Transferencia::findOne($documento_origem_id);
            $descricao = $origem->descricao;
            $data_doc = $origem->data;
            $bancoContaOrigem = BancoConta::findOne($origem->fin_banco_conta_id_origem);
            $contaOrigem = PlanoConta::findOne($bancoContaOrigem->cnt_plano_conta_id);

            $bancoContaDestino = BancoConta::findOne($origem->fin_banco_conta_id_destino);
            $contaDestino = PlanoConta::findOne($bancoContaDestino->cnt_plano_conta_id);
            
            if ($bancoContaOrigem->cnt_diario_id==2) {
               $cnt_diario_id = 2;
               $cnt_diario_descricao='Banco';
               $tem_plano_fluxo_caixa_id = $bancoContaOrigem->cnt_plano_fluxo_caixa_id;
            }elseif ($bancoContaDestino->cnt_diario_id==2) {
               $cnt_diario_id = 2;
               $cnt_diario_descricao='Banco';
               $tem_plano_fluxo_caixa_id = $bancoContaDestino->cnt_plano_fluxo_caixa_id;
            }else{
                $cnt_diario_id = 1;
               $cnt_diario_descricao='Caixa';
               $tem_plano_fluxo_caixa_id = $bancoContaDestino->cnt_plano_fluxo_caixa_id;
            }
            $data[0]= [
                 'descricao'=>'Transferência Nº '.$origem->numero.'('.$bancoContaOrigem->banco->sigla.'/'.$bancoContaDestino->banco->sigla.')',
                 'cnt_natureza_id'=>'C',
                 'cnt_plano_conta_id'=>$bancoContaOrigem->cnt_plano_conta_id,
                 'cnt_plano_terceiro_id'=>null,
                 'cnt_plano_iva_id'=>null,
                 'cnt_plano_fluxo_caixa_id'=>$tem_plano_fluxo_caixa_id,
                 'valor'=>$origem->valor,
             ];
            $data[1]= [
                 'descricao'=>'Transferência Nº '.$origem->numero.'('.$bancoContaOrigem->banco->sigla.'/'.$bancoContaDestino->banco->sigla.')',
                 'cnt_natureza_id'=>'D',
                 'cnt_plano_conta_id'=>$bancoContaDestino->cnt_plano_conta_id,
                 'cnt_plano_terceiro_id'=>null,
                 'cnt_plano_iva_id'=>null,
                 'cnt_plano_fluxo_caixa_id'=>$tem_plano_fluxo_caixa_id,
                 'valor'=>$origem->valor,
             ];
            
        } // END MOVIMENTO_INTERNO




        // DESPESA_FATURA_FORNECEDOR
        if ($cnt_documento_id ==Documento::DESPESA_FATURA_FORNECEDOR) {
            $origem = Despesa::findOne($documento_origem_id);
            $descricao =$origem->descricao;
            $data[0]= [
                 'descricao'=>$documento->codigo.' - '.$origem->numero,
                 'cnt_natureza_id'=>$documento->cnt_natureza_id,
                 'cnt_plano_conta_id'=>$documento->cnt_plano_conta_id,
                 'cnt_plano_terceiro_id'=>$planoConta->tem_plano_externo?$origem->dsp_person_id:null,
                 'cnt_plano_iva_id'=>null,
                 'cnt_plano_fluxo_caixa_id'=>$documento->cnt_plano_fluxo_caixa_id?$documento->cnt_plano_fluxo_caixa_id:null,
                 'valor'=>$origem->valor,
             ];
            $query = Yii::$app->CntQuery->listDespesaItems($origem->id);
            foreach ($query as $key=> $item) {
                 if (($planoContaItem = PlanoConta::findOne($item['cnt_plano_conta_id']))!=null) {
                     $data[$item['id']]= [
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'descricao'=>$documento->codigo.' - '.$origem->numero.'-'.$item['descricao'],
                         'cnt_plano_conta_id'=>$item['cnt_plano_conta_id'],
                         'cnt_plano_terceiro_id'=>$planoContaItem->tem_plano_externo?$origem->dsp_person_id:null,
                         'cnt_plano_iva_id'=>$item['cnt_plano_iva_id']?$item['cnt_plano_iva_id']:null,
                         'cnt_plano_fluxo_caixa_id'=>$planoContaItem->tem_plano_fluxo_caixa?1:null,
                         'valor'=>$item['valor']-$item['valor_iva'],
                     ];

                     if($item['valor_iva'] >0){
                        $data[$item['id'].$item['valor_iva']]= [
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'descricao'=>$documento->codigo.' - '.$origem->numero.'-'.$item['descricao'],
                         'cnt_plano_conta_id'=>$item['cnt_plano_conta_id'],
                         'cnt_plano_terceiro_id'=>$planoContaItem->tem_plano_externo?$origem->dsp_person_id:null,
                         'cnt_plano_iva_id'=>$item['cnt_plano_iva_id']?$item['cnt_plano_iva_id']:null,
                         'cnt_plano_fluxo_caixa_id'=>$planoContaItem->tem_plano_fluxo_caixa?1:null,
                         'valor'=>$item['valor_iva'],
                     ];
                    }
                 }
             }
        } // END DESPESA_FATURA_FORNECEDOR






        // FATURA_FORNECEDOR_INVESTIMENTO
        if ($cnt_documento_id ==Documento::FATURA_FORNECEDOR_INVESTIMENTO) {
            $origem = Despesa::findOne($documento_origem_id);
            $descricao =$origem->descricao;
            $data[0]= [
                 'descricao'=>$documento->codigo.' - '.$origem->numero,
                 'cnt_natureza_id'=>$documento->cnt_natureza_id,
                 'cnt_plano_conta_id'=>$documento->cnt_plano_conta_id,
                 'cnt_plano_terceiro_id'=>$planoConta->tem_plano_externo?$origem->dsp_person_id:null,
                 'cnt_plano_iva_id'=>null,
                 'cnt_plano_fluxo_caixa_id'=>$documento->cnt_plano_fluxo_caixa_id?$documento->cnt_plano_fluxo_caixa_id:null,
                 'valor'=>$origem->valor,
             ];
            $query = Yii::$app->CntQuery->listDespesaItems($origem->id);
            foreach ($query as $key=> $item) {
                 if (($planoContaItem = PlanoConta::findOne($item['cnt_plano_conta_id']))!=null) {
                     $data[$item['id']]= [
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'descricao'=>$documento->codigo.' - '.$origem->numero.'-'.$item['descricao'],
                         'cnt_plano_conta_id'=>$item['cnt_plano_conta_id'],
                         'cnt_plano_terceiro_id'=>$planoContaItem->tem_plano_externo?$origem->dsp_person_id:null,
                         'cnt_plano_iva_id'=>$item['cnt_plano_iva_id']?$item['cnt_plano_iva_id']:null,
                         'cnt_plano_fluxo_caixa_id'=>$planoContaItem->tem_plano_fluxo_caixa?1:null,
                         'valor'=>$item['valor']-$item['valor_iva'],
                     ];
                     if($item['valor_iva'] >0){
                        $planoIva =  PlanoIva::findOne($item['cnt_plano_iva_id']);
                        $planoContaItem =  PlanoConta::findOne($planoIva->cnt_plano_conta_id);
                        $data[$item['id'].$item['valor_iva']]= [
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'descricao'=>$documento->codigo.' - '.$origem->numero.'-'.$item['descricao'],
                         'cnt_plano_conta_id'=>$planoContaItem->id,
                         'cnt_plano_terceiro_id'=>$planoContaItem->tem_plano_externo?$origem->dsp_person_id:null,
                         'cnt_plano_iva_id'=>$item['cnt_plano_iva_id']?$item['cnt_plano_iva_id']:null,
                         'cnt_plano_fluxo_caixa_id'=>$planoContaItem->tem_plano_fluxo_caixa?1:null,
                         'valor'=>$item['valor_iva'],
                     ];
                    }
                 }
             }
        } // END FATURA_FORNECEDOR_INVESTIMENTO












         // RECEBIMENTO_FATURA_PROVISORIA ---  RECEBIMENTO_REEMBOLSO  --RECEBIMENTO_ADIANTAMENTO
        if ($cnt_documento_id ==Documento::RECEBIMENTO_FATURA_PROVISORIA||$cnt_documento_id ==Documento::RECEBIMENTO_REEMBOLSO ||$cnt_documento_id ==Documento::RECEBIMENTO_ADIANTAMENTO) {
            $documento = Documento::findOne($cnt_documento_id);
            $planoConta = PlanoConta::findOne($documento->cnt_plano_conta_id);
            $origem = Recebimento::findOne($documento_origem_id);
            $bancoConta = BancoConta::findOne($origem->fin_banco_conta_id);
            $diario = Diario::findOne($bancoConta->cnt_diario_id);
            $planoContaBanco = PlanoConta::findOne($bancoConta->cnt_plano_conta_id);
            $descricao = $origem->descricao;
            $data_doc = $origem->data;
            $cnt_diario_id = $diario->id;
            $cnt_diario_descricao=$diario->descricao;
            // print_r($planoConta);die();
            $data[0]= [
                 'descricao'=>'Recebimento Nº '.$origem->numero.'('.$bancoConta->banco->sigla.'/'.$bancoConta->banco->sigla.')',
                 'cnt_natureza_id'=>'C',
                 'cnt_plano_conta_id'=>$planoContaBanco->id,
                 'cnt_plano_terceiro_id'=>!empty($planoContaBanco->tem_plano_externo)?$origem->dsp_person_id:null,
                 'cnt_plano_iva_id'=>!empty($planoContaBanco->cnt_plano_iva_id)?$planoContaBanco->cnt_plano_iva_id:null,
                 'cnt_plano_fluxo_caixa_id'=>$planoContaBanco->tem_plano_fluxo_caixa?$documento->cnt_plano_fluxo_caixa_id:null,
                 'valor'=>$origem->valor,
             ];
            $data[1]= [
                 'descricao'=>'Recebimento Nº '.$origem->numero.'('.$bancoConta->banco->sigla.'/'.$bancoConta->banco->sigla.')',
                 'cnt_natureza_id'=>'D',
                 'cnt_plano_conta_id'=>$planoConta->id,
                 'cnt_plano_terceiro_id'=>!empty($planoConta->tem_plano_externo)?$origem->dsp_person_id:null,
                 'cnt_plano_iva_id'=>null,
                 'cnt_plano_fluxo_caixa_id'=>null,
                 'valor'=>$origem->valor,
             ];
            
         }// RECEBIMENTO_FATURA_PROVISORIA ---  RECEBIMENTO_REEMBOLSO  --RECEBIMENTO_ADIANTAMENTO











        // RECEBIMENTO_TESOURARIO
        if ($cnt_documento_id ==Documento::RECEBIMENTO_TESOURARIO) {
            $origem = Recebimento::findOne($documento_origem_id);
            $bancoConta = BancoConta::findOne($origem->fin_banco_conta_id);
            $diario = Diario::findOne($bancoConta->cnt_diario_id);
            $planoConta = PlanoConta::findOne($bancoConta->cnt_plano_conta_id);
            $descricao = $origem->descricao;
            $data_doc = $origem->data;
            $cnt_diario_id = $diario->id;
            $cnt_diario_descricao=$diario->descricao;
            $data[0]= [
                 'descricao'=>$documento->codigo.'-'.$origem->numero.'/'.$origem->bas_ano_id,
                 'cnt_natureza_id'=>$documento->cnt_natureza_id,
                 'cnt_plano_conta_id'=>$bancoConta->cnt_plano_conta_id,
                 'cnt_plano_terceiro_id'=>!empty($planoConta->tem_plano_externo)?$origem->dsp_person_id:null,
                 'cnt_plano_iva_id'=>null,
                 'cnt_plano_fluxo_caixa_id'=>$bancoConta->cnt_plano_fluxo_caixa_id,
                 'valor'=>$origem->valor,
             ];
            $query = Yii::$app->CntQuery->listRecebimentoItems($origem->id);
            foreach ($query as $key=> $item) {
                 if (($planoContaItem = PlanoConta::findOne($item['cnt_plano_conta_id']))!=null) {
                     $data[$item['id']]= [
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'descricao'=>$documento->codigo.'-'.$item['numero'].'-'.$item['descricao'],
                         'cnt_plano_conta_id'=>$item['cnt_plano_conta_id'],
                         'cnt_plano_terceiro_id'=>!empty($planoContaItem->tem_plano_externo)?$origem->dsp_person_id:null,
                         'cnt_plano_iva_id'=>!empty($item['cnt_plano_iva_id'])?$item['cnt_plano_iva_id']:null,
                         'cnt_plano_fluxo_caixa_id'=>$item['cnt_plano_fluxo_caixa_id'],
                         'valor'=>$item['valor'],
                     ];
                 }
             }
        } // END RECEBIMENTO_TESOURARIO






         // PAGAMENTO
        if ($cnt_documento_id ==Documento::PAGAMENTO) {
            $origem = Pagamento::findOne($documento_origem_id);
            $bancoConta = BancoConta::findOne($origem->fin_banco_conta_id);
            $diario = Diario::findOne($bancoConta->cnt_diario_id);
            $planoContaBanco = PlanoConta::findOne($bancoConta->cnt_plano_conta_id);
            $descricao = $origem->descricao;
            $data_doc = $origem->data;
            $cnt_diario_id = $diario->id;
            $cnt_diario_descricao=$diario->descricao;
            

             $fluxos =[];            
            $query0 = Yii::$app->CntQuery->listPagamentoDespesasClienteItemsProcesso($origem->id);
            foreach ($query0 as $key => $flx) {
                $fluxos[$flx['cnt_plano_fluxo_caixa_id']]=$flx['cnt_plano_fluxo_caixa_id'];
            }
            $query1 = Yii::$app->CntQuery->listPagamentoDespesasClienteItems($origem->id);
            foreach ($query1 as $key => $flx) {
                $fluxos[$flx['cnt_plano_fluxo_caixa_id']]=$flx['cnt_plano_fluxo_caixa_id'];
            }
            $query2 = Yii::$app->CntQuery->listPagamentoDespesasAgenciaItems($origem->id);
            foreach ($query2 as $key => $flx) {
                $fluxos[$flx['cnt_plano_fluxo_caixa_id']]=$flx['cnt_plano_fluxo_caixa_id'];
            }
            $fluxo_id = null;
            foreach ($fluxos as $key => $fluxo) {
                $fluxo_id = $fluxo;
            }

               $data[0]= [
                 'descricao'=>$documento->codigo.'-'.$origem->numero.'/'.$origem->bas_ano_id,
                 'cnt_natureza_id'=>$documento->cnt_natureza_id,
                 'cnt_plano_conta_id'=>$planoContaBanco->id,
                 'cnt_plano_terceiro_id'=>null,
                 'cnt_plano_iva_id'=>null,
                 'cnt_plano_fluxo_caixa_id'=>$fluxo_id,
                 'valor'=>$origem->valor,
             ];
            
             // print_r($query);die();
            foreach ($query0 as $key=> $item) {
                $planoContaItem = PlanoConta::findOne($item['cnt_plano_conta_id']);
                    $data[$item['id']]= [
                        'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                        'descricao'=>$documento->codigo.'-'.$item['numero'].'-'.$item['descricao'],
                        'cnt_plano_conta_id'=>$item['cnt_plano_conta_id'],
                        'cnt_plano_terceiro_id'=>$planoContaItem->tem_plano_externo?$item['cnt_plano_terceiro_id']:null,
                        'cnt_plano_iva_id'=>$item['cnt_plano_iva_id']?$item['cnt_plano_iva_id']:null,
                        'cnt_plano_fluxo_caixa_id'=>$planoContaItem->tem_plano_fluxo_caixa?$item['cnt_plano_fluxo_caixa_id']:null,
                        'valor'=>$item['valor'],
                    ];
                
            }

 
             // print_r($query);die();
            foreach ($query1 as $key=> $item) {
                 $planoContaItem = PlanoConta::findOne($item['cnt_plano_conta_id']);
                     $data[$item['id']]= [
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'descricao'=>$documento->codigo.'-'.$item['numero'].'-'.$item['descricao'],
                         'cnt_plano_conta_id'=>$item['cnt_plano_conta_id'],
                         'cnt_plano_terceiro_id'=>$planoContaItem->tem_plano_externo?$item['cnt_plano_terceiro_id']:null,
                         'cnt_plano_iva_id'=>$item['cnt_plano_iva_id']?$item['cnt_plano_iva_id']:null,
                         'cnt_plano_fluxo_caixa_id'=>$planoContaItem->tem_plano_fluxo_caixa?$item['cnt_plano_fluxo_caixa_id']:null,
                         'valor'=>$item['valor'],
                     ];
                 
             }

             $query2 = Yii::$app->CntQuery->listPagamentoDespesasAgenciaItems($origem->id);
             // print_r($query);die();
            foreach ($query2 as $key=> $item) {
                 $planoContaItem = PlanoConta::findOne($item['cnt_plano_conta_id']);
                     $data[$item['id']]= [
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'descricao'=>$documento->codigo.'-'.$item['numero'].'-'.$item['descricao'],
                         'cnt_plano_conta_id'=>$item['cnt_plano_conta_id'],
                         'cnt_plano_terceiro_id'=>$item['cnt_plano_terceiro_id'],
                         'cnt_plano_iva_id'=>$item['cnt_plano_iva_id']?$item['cnt_plano_iva_id']:null,
                         'cnt_plano_fluxo_caixa_id'=>null,
                         'valor'=>$item['valor'],
                     ];
                 
             }

             $query = Yii::$app->CntQuery->listPagamentoDespesas($origem->id);
            foreach ($query as $key=> $item) {
                $contaDC = PlanoConta::findOne($item['cnt_plano_conta_id']);
                     $data[$item['id']]= [
                         'descricao'=>$documento->codigo.'-CB-'.$item['numero'].'-'.$item['descricao'],
                         'cnt_natureza_id'=>$documento->cnt_natureza_id=='C'?'D':'C',
                         'cnt_plano_conta_id'=>$contaDC->id,
                         'cnt_plano_terceiro_id'=>$contaDC->tem_plano_externo?$origem->dsp_person_id:null,
                         'cnt_plano_iva_id'=>null,
                         'cnt_plano_fluxo_caixa_id'=>$contaDC->cnt_plano_fluxo_caixa_id,
                         'valor'=>$origem->valor,
                     ];
                 
             }
        } // END PAGAMENTO
                
        



        //RETURN
         if(empty($data)){
                 return ['error'=>'NEHUM ITEM PARA APRESENTAR NESTE PROCESSO'];
             }else{ 
             return [
                'items'=>$data,
                'cnt_diario_id'=>$cnt_diario_id?$cnt_diario_id:null,
                'cnt_diario_descricao'=>$cnt_diario_descricao?$cnt_diario_descricao:null,
                'descricao'=>$descricao,
                'documento_origem_data'=>$data_doc,
            ];
         }
     }



}
