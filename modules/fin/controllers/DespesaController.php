<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

use app\modules\fin\services\DespesaService;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaItem;
use app\modules\fin\models\DespesaTipo;
use app\modules\fin\models\DespesaSearch;
use app\modules\cnt\models\Documento;
use app\models\Model;

/**
 * EdeficiosController implements the CRUD actions for Despesa model.
 */
class DespesaController extends Controller
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
                        'actions' => ['file-browser'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'create-agencia', 'update', 'undo', 'lock-and-unlock', 'relatorio', 'grelha', 'grelha-update'],
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
                    'lock-and-unlock' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Despesa models.
     * @return mixed
     */
    public function actionIndex()
    {


        //         $models = Despesa::find()
        //         ->where(['>','dsp_processo_id',0])
        //         ->andWhere(['>','dsp_processo_id',0])
        //         ->andWhere(['dsp_fatura_provisoria_id'=>NULL])
        //         ->andWhere(['fin_recebimento_id'=>NULL])
        //         ->andWhere(['fin_nota_credito_id'=>NULL])
        //         ->andWhere(['>','fin_aviso_credito_id',0])
        //         ->all();
        // foreach ($models as $key => $value) {
        //     $pr =\app\modules\dsp\models\Processo::findOne($value->dsp_processo_id);
        //     // $fp = \app\modules\fin\models\FaturaProvisoria::findOne($value->dsp_fatura_provisoria_id);
        //     $ac = \app\modules\fin\models\AvisoCredito::findOne($value->fin_aviso_credito_id);
        //     $value->numero = (empty($pr->numero)?'':'PR'.$pr->numero.'/'.$pr->bas_ano_id).'|NC'.$ac->numero.'/'.$ac->bas_ano_id.'|CF'.$value->dsp_person_id;
        //     $value->save();
        // }

        $searchModel = new DespesaSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Despesa models.
     * @return mixed
     */
    public function actionRelatorio()
    {
        $searchModel = new DespesaSearch();
        $dataProvider = $searchModel->relatorio(Yii::$app->request->queryParams);

        return $this->render('relatorio', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Despesa models.
     * @return mixed
     */
    public function actionGrelha()
    {
        $searchModel = new DespesaSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->searchGrelha(Yii::$app->request->queryParams);

        return $this->render('grelha', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Despesa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGrelhaUpdate($id)
    {

        $model = $this->findModel($id);
        if ($model->is_lock == Despesa::IS_LOCK) {
            Yii::$app->getSession()->setFlash('warning', 'ESTA DESPESA JÁ FOI BLOQUEADA NÃO PODE SER ALTERADO');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        if ($model->status == Despesa::STATUS_ANULADO) {
            Yii::$app->getSession()->setFlash('warning', 'ESTA DESPESA JÁ FOI ANULADO.');
            return $this->redirect(['view', 'id' => $model->id]);
        }


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'REGISTRO ATUALIZADO COM SUCESSO.');
            return $this->redirect(['grelha']);
        }

        return $this->render('grelha-update', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Despesa model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $modelItem = $model->despesaItem; // your model can be loaded here
        // Check if there is an Editable ajax request
        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            // if ($item->load($_POST)) {
            // $despesaItem = Yii::$app->request->post('DespesaItem');
            $id = Yii::$app->request->post('item_id');
            $item = DespesaItem::findOne($id);
            $item->item_descricao = Yii::$app->request->post($id);
            if ($item->save()) {
                return ['output' => $item->item_descricao, 'message' => ''];
            } else {
                return ['output' => '', 'message' => 'Erro na validação do item'];
            }
        }
        return $this->render('view', [
            'model' => $model,
            'modelItem' => $modelItem,
        ]);
    }

    /**
     * Creates a new Despesa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Despesa;
        $model->data = date('Y-m-d');
        $model->isNewRecord = true;
        $model->valor = 0;
        $model->saldo =  0;
        $model->valor_pago = 0;
        $model->status = 1;
        $model->recebido = 1;
        $model->fin_despesa_tipo_id = DespesaTipo::CLIENTE;


        $modelsDespesaItem = [new DespesaItem];

        if ($model->load(Yii::$app->request->post())) {
            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);
            $model->numero =  (empty($model->processo->numero) ? '' : 'PR' . $model->processo->numero . '/' . $model->processo->bas_ano_id) . (empty($model->notaCredito->numero) ? '' : '|AC' . $model->notaCredito->numero . '/' . $model->notaCredito->bas_ano_id) . (empty($model->dsp_fatura_provisoria_id) ? '' : '|FP' . $model->faturaProvisoria->numero . '/' . $model->faturaProvisoria->bas_ano_id) . '|CF' . $model->dsp_person_id;
            $modelsDespesaItem = Model::createMultiple(DespesaItem::classname());
            Model::loadMultiple($modelsDespesaItem, Yii::$app->request->post());




            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsDespesaItem),
                    ActiveForm::validate($model)
                );
            }
            // validate all models
            $valid = $model->validate();
            // $valid = Model::validateMultiple($modelsDespesaItem) && $valid;
            if (empty($model->dsp_fatura_provisoria_id) && Yii::$app->FinQuery->existFpProcesso($model->dsp_processo_id)) {
                Yii::$app->getSession()->setFlash('warning', 'A FATURA PROVISÓRIA DEVE SER INFORMADO');
                return $this->render('create', [
                    'model' => $model,
                    'modelsDespesaItem' => (empty($modelsDespesaItem)) ? [new DespesaItem] : $modelsDespesaItem,
                ]);
            }
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $model->valor =  0;
                $model->saldo =  0;
                try {

                    if ($flag = $model->save()) {
                        foreach ($modelsDespesaItem as $modelDespesaItem) {
                            $item = new DespesaItem();
                            $item->fin_despesa_id = $model->id;
                            $item->item_id = $modelDespesaItem->item_id;
                            $item->valor = $modelDespesaItem->valor;
                            $item->valor_iva = 0;
                            $item->cnt_plano_terceiro_id = $model->processo->nome_fatura;
                            if (!($flag = $item->save())) {
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
            'modelsDespesaItem' => (empty($modelsDespesaItem)) ? [new DespesaItem] : $modelsDespesaItem,

        ]);
    }



    /**
     * Creates a new Despesa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateAgencia()
    {
        $model = new Despesa;
        $model->data = date('Y-m-d');
        $model->valor = 0;
        $model->saldo =  0;
        $model->valor_pago = 0;
        $model->bas_ano_id = substr(date('Y'), -2);
        $model->recebido = 1;
        $model->fin_despesa_tipo_id = DespesaTipo::AGENCIA;

        $modelsDespesaItem = [new DespesaItem];

        if ($model->load(Yii::$app->request->post())) {

            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);


            $modelsDespesaItem = Model::createMultiple(DespesaItem::classname());
            Model::loadMultiple($modelsDespesaItem, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsDespesaItem),
                    ActiveForm::validate($model)
                );
            }
            // validate all models
            $valid = $model->validate();
            // $valid = Model::validateMultiple($modelsDespesaItem) && $valid;
            // print_r($valid);die();
            if ($valid) {

                $transaction = \Yii::$app->db->beginTransaction();
                $model->valor =  0;
                $model->saldo =  0;

                try {

                    if ($flag = $model->save()) {
                        foreach ($modelsDespesaItem as $modelDespesaItem) {
                            $item = new DespesaItem();
                            $item->fin_despesa_id = $model->id;
                            $item->item_id = $modelDespesaItem->item_id;
                            $item->valor = $modelDespesaItem->valor;
                            $item->valor_iva = $modelDespesaItem->valor_iva;
                            $item->cnt_plano_terceiro_id = $modelDespesaItem->cnt_plano_terceiro_id;
                            if (!($flag = $item->save())) {
                                Yii::$app->getSession()->setFlash('warning', $item->errors);
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

        return $this->render('create-agencia', [
            'model' => $model,
            'modelsDespesaItem' => (empty($modelsDespesaItem)) ? [new DespesaItem] : $modelsDespesaItem,

        ]);
    }

    /**
     * Updates an existing Despesa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);
        $readonly = !Yii::$app->user->can('fin/despesa/admin-update');
        // VEREFICAR SE FOI PAGO OU BLOQUEADO OU ANULADO
        if ($model->is_lock == Despesa::IS_LOCK || $model->status == Despesa::STATUS_ANULADO) {
            Yii::$app->getSession()->setFlash('warning', 'ESTA DESPESA JÁ FOI BLOQUEADA OU PAGO OU ANULADO NÃO PODE SER ALTERADO');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // vereficar se a DESP foi criando antes da FP e agora já existe FP  
        if (DespesaService::foiCriadoAntesFaturaProvisoria($model->id)) {
            Yii::$app->getSession()->setFlash('warning', 'Despesa foi criado antes da Fatura Provisória e agora já tem Fatura Provisória.');
            if (!Yii::$app->user->can('fin/despesa/update-com-fatura')) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        // vereficar se a DESP ja tem uma PD associado atraves da FP associado 
        if (DespesaService::temFaturaFefinitiva($model->id)) {
            Yii::$app->getSession()->setFlash('warning', 'Despesa já tem Fatura Definitiva.');
            if (!Yii::$app->user->can('fin/despesa/update-com-fatura')) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $modelsDespesaItem = $model->despesaItem;
        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsDespesaItem, 'id', 'id');
            $modelsDespesaItem = Model::createMultiple(DespesaItem::classname(), $modelsDespesaItem);
            Model::loadMultiple($modelsDespesaItem, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDespesaItem, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsDespesaItem),
                    ActiveForm::validate($model)
                );
            }
            $valid = $model->validate();

            if ($valid) {
                $model->saldo =  0;
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {
                        if (!empty($deletedIDs)) {
                            DespesaItem::deleteAll(['id' => $deletedIDs]);
                        }
                        $model->valor =  0;
                        foreach ($modelsDespesaItem as $modelDespesaItem) {
                            $modelDespesaItem->fin_despesa_id = $model->id;
                            $model->valor =  $model->valor + $modelDespesaItem->valor;
                            if (!($flag = $modelDespesaItem->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        $model->saldo =  $model->valor - $model->valor_pago;

                        if (!($flag = $model->save())) {
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

        return $this->render('update', [
            'model' => $model,
            'modelsDespesaItem' => (empty($modelsDespesaItem)) ? [new DespesaItem] : $modelsDespesaItem,
            'readonly' => $readonly,
        ]);
    }

    /**
     * Deletes an existing Despesa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUndo($id)
    {
        $model = $this->findModel($id);

        if (DespesaService::temPagamento($model->id)) {
            Yii::$app->getSession()->setFlash('warning', 'Esta despesa já se encontra pago.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $documento_id = null;
        if ($model->cnt_documento_id = Documento::DESPESA_FATURA_FORNECEDOR) {
            $documento_id = Documento::DESPESA_FATURA_FORNECEDOR;
        } elseif ($model->cnt_documento_id = Documento::FATURA_FORNECEDOR_INVESTIMENTO) {
            $documento_id = Documento::FATURA_FORNECEDOR_INVESTIMENTO;
        } elseif ($model->cnt_documento_id = Documento::FATURA_RECIBO) {
            $documento_id = Documento::FATURA_RECIBO;
        }

        if (!empty($documento_id)) {
            if (Yii::$app->CntQuery->inContabilidadeAtive($documento_id, $id)) {
                Yii::$app->getSession()->setFlash('warning', 'Esta despesa já se encontra na Contabilidade anule na Contabilidade para poder proceguir.');
                return $this->redirect(['view', 'id' => $id]);
            }
        }




        if ($model->is_lock) {
            Yii::$app->getSession()->setFlash('error', 'ESTA DESPESA ESTA BLOQUEADO  NÃO PODE SER ANULADO.');
        } else {
            $model->status = 0;
            if ($model->save(false)) {
                Yii::$app->getSession()->setFlash('success', 'DESPESA ANULADO COM SUCESSO.');
            } else {
                Yii::$app->getSession()->setFlash('error', 'ERRO AO EFETUAR A OPERAÇÃO.');
                // print_r($model->errors);die();
            }
        }


        return $this->redirect(['view', 'id' => $model->id]);
    }


    /**
     * Deletes an existing Despesa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLockAndUnlock($id, $value)
    {
        $model = $this->findModel($id);
        if ($model->status == Despesa::STATUS_ANULADO) {
            Yii::$app->getSession()->setFlash('error', 'ESTA DESPESA JÁ FOI ANULADO.');
        } else {
            $model->is_lock = $value;
            if ($model->save()) {
                if ($value) {
                    Yii::$app->getSession()->setFlash('success', 'DESPESA BLOQUEADO COM SUCESSO.');
                } else {
                    Yii::$app->getSession()->setFlash('success', 'DESPESA DESBLOQUEADO COM SUCESSO.');
                }
            } else {
                Yii::$app->getSession()->setFlash('error', 'ERRO AO EFETUAR A OPERAÇÃO.');
            }
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }



    /**
     * Finds the Despesa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Despesa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Despesa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Finds the Despesa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Despesa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGrelha1()
    {
        $models = Despesa::find()->orderBy('id')->all();
        foreach ($models as $key => $model) {
            $model->is_lock = 0;
            $model->save(false);
            # code...
        }
    }

    public function actionFileBrowser($fin_despesa_id)
    {
        $model = new \app\models\FileBrowser();
        $model->path = \app\components\helpers\UploadFileHelper::setUrlFileDespesa($fin_despesa_id);

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
