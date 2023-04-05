<?php

namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use app\modules\dsp\models\ProcessoWorkflow;
use app\modules\dsp\models\ProcessoWorkflowSearch;
use app\modules\dsp\models\Item;
use app\modules\dsp\models\ProcessoObs;
use app\modules\dsp\models\Setor;
use app\modules\dsp\models\ProcessoWorkflowStatus;

/**
 * EdeficiosController implements the CRUD actions for ProcessoWorkflow model.
 */
class ProcessoWorkflowController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'report', 'init', 'processo-setor', 'send-setor', 'get-setor', 'send-arquivo', 'unsend-arquivo', 'arquivo', 'send-arquivo-provisorio', 'receber-box', 'enviar-box', 'archive-box', 'undo-archive-box', 'realocar-box', 'set-prioridade', 'classificacao', 'classificacao-user', 'classificacao-setor'],
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
                    'get-setor' => ['POST'],
                    'set-prioridade' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProcessoWorkflow models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProcessoWorkflowSearch();
        $searchModel->status = [ProcessoWorkflowStatus::PEND_RECEBIMENTO, ProcessoWorkflowStatus::RECEBIDO, ProcessoWorkflowStatus::AGUR_RECEBIMENTO];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all ProcessoWorkflow models.
     * @return mixed
     */
    public function actionProcessoSetor()
    {
        $setorList = [];
        $searchModel = new ProcessoWorkflowSearch();
        if (!Yii::$app->user->can('dsp/processo-workflow/set-prioridade')) {
            $searchModel->dsp_setor_id = \app\modules\dsp\models\SetorUser::find()->select(['dsp_setor_id'])->where(['user_id' => Yii::$app->user->identity->id])->scalar();
            $setorList = ArrayHelper::map(\app\modules\dsp\models\SetorUser::find()->joinWith(['setor'])->where(['user_id' => Yii::$app->user->identity->id])->all(), 'setor.id', 'setor.descricao');
        } else {
            $searchModel->dsp_setor_id = 4; // id setor de insercao
            $setorList = ArrayHelper::map(\app\modules\dsp\models\Setor::find()->all(), 'id', 'descricao');
        }
        $dataProvider = $searchModel->searchProcessoSetor(Yii::$app->request->queryParams);
        return $this->render('processo-setor', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'setorList' => $setorList,
        ]);
    }


    /**
     * Lists all ProcessoWorkflow models.
     * @return mixed
     */
    public function actionReport()
    {
        $searchModel = new ProcessoWorkflowSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $searchModel->status = [1, 2, 4];
        $dataProvider = $searchModel->searchReport(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all ProcessoWorkflow models.
     * @return mixed
     */
    public function actionArquivo()
    {
        $searchModel = new ProcessoWorkflowSearch();
        $dataProvider = $searchModel->searchArquivo(Yii::$app->request->queryParams);

        return $this->render('arquivo', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUndoArchiveBox()
    {
        $count = 0;
        if (Yii::$app->request->post('selection')) {
            $selection = (array)Yii::$app->request->post('selection');
            $models = ProcessoWorkflow::findAll(['id' => $selection]);
            foreach ($models as $key => $data) {
                $count++;
                $model = ProcessoWorkflow::findOne($data->id);
                $model->dsp_setor_id = 12;
                $model->user_id = Yii::$app->user->identity->id;
                $model->status = 2;
                $model->out_workflow_id = null;
                $model->out_data_hora = null;
                $model->data_fim = null;
                $model->save();
            }
            Yii::$app->getSession()->setFlash('success', $count . ' Pocesso removido do arquivo com sucesso');
            return $this->redirect(['arquivo']);
        }
        $searchModel = new ProcessoWorkflowSearch();
        $dataProvider = $searchModel->searchArquivo(Yii::$app->request->queryParams);

        return $this->render('undo-archive-box', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProcessoWorkflow model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        // $ProcessoWorkflow = ProcessoWorkflow::find()
        // ->where(['dsp_setor_id'=>10])
        // ->andWhere(['out_workflow_id'=>NULL])
        // // ->andWhere(['<','out_workflow_id',0])
        // ->all(); 
        // foreach ($ProcessoWorkflow as $pw){
        // $pw->save();
        // }


        $model = $this->findModel($id);
        if ($model->status == 3) {
            Yii::$app->getSession()->setFlash('warning', 'Este processo já foi enviado');

            return $this->redirect(Yii::$app->request->referrer);
        }
        $newModel = new ProcessoWorkflow();
        $modelObs = new ProcessoObs();

        return $this->render('view', [
            'model' => $model,
            'newModel' => $newModel,
            'modelObs' => $modelObs,
        ]);
    }

    /**
     * Creates a new ProcessoWorkflow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($dsp_processo_id, $in_workflow_id)
    {
        $model = new ProcessoWorkflow();
        $model->dsp_processo_id = $dsp_processo_id;
        $model->in_workflow_id = $in_workflow_id;
        $model->status = ProcessoWorkflowStatus::DISPONIVEL_SETOR;
        $model->data_inicio = new \yii\db\Expression('NOW()');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $old = ProcessoWorkflow::findOne($in_workflow_id);
            $old->out_workflow_id = $model->id;
            $old->data_fim = new \yii\db\Expression('NOW()');
            $old->status = ProcessoWorkflowStatus::ENVIADO;
            $old->save();
            Yii::$app->getSession()->setFlash('success', 'Processo enviado com sucesso');
        } else {
            print_r($model->errors);
            die();
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
        }

        return $this->redirect(['index']);
    }



    /**
     * Creates a new ProcessoWorkflow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionInit($dsp_processo_id)
    {
        $model = new ProcessoWorkflow();
        $model->dsp_processo_id = $dsp_processo_id;
        $model->status = 2;
        $model->in_data_hora = new \yii\db\Expression('NOW()');


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Processo enviado com sucesso');
        } else {
            print_r($model->errors);
            die();
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
        }

        return $this->redirect(['index']);
        // return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Updates an existing ProcessoWorkflow model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        // $model = $this->findModel($id);
        // $model->status = 2;
        // $model->recebeu_data_hora = new \yii\db\Expression('NOW()');
        // $model->data_inicio = new \yii\db\Expression('NOW()');

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     if(($old = ProcessoWorkflow::findOne($model->in_workflow_id))!=null){
        //         $old->data_fim = new \yii\db\Expression('NOW()');
        //         $old->status = 3;
        //         $old->save();
        //     }
        //     Yii::$app->getSession()->setFlash('success', 'Processo aceite com sucesso');
        // }else{
        //     print_r($model->errors);die();
        //     Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
        // }

        // return $this->redirect(['index']);
    }


    /**
     * Updates an existing ProcessoWorkflow model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetSetor($id)
    {
        $old = $this->findModel($id);
        $model = new ProcessoWorkflow();
        $model->status = ProcessoWorkflowStatus::RECEBIDO;
        $model->user_id = Yii::$app->user->identity->id;
        $model->dsp_setor_id = $old->dsp_setor_id;
        $model->dsp_processo_id = $old->dsp_processo_id;
        $model->data_inicio = new \yii\db\Expression('NOW()');
        $model->in_workflow_id = $id;
        if ($model->save()) {
            $old->status = ProcessoWorkflowStatus::ENVIADO;
            $old->out_workflow_id = $model->id;
            $old->data_fim = new \yii\db\Expression('NOW()');
            $old->save();
            Yii::$app->getSession()->setFlash('success', 'Processo aceite com sucesso');
        } else {
            // print_r($model->errors);die();
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
        }

        return $this->redirect(['processo-setor']);
    }


    /**
     * Creates a new ProcessoWorkflow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionSendArquivo($dsp_processo_id, $in_workflow_id)
    {
        $model = new ProcessoWorkflow();
        $model->dsp_processo_id = $dsp_processo_id;
        $model->in_workflow_id = $in_workflow_id;
        $model->dsp_setor_id = 10;
        $model->status = 5;
        $model->data_inicio = new \yii\db\Expression('NOW()');
        if ($model->save()) {
            $old = ProcessoWorkflow::findOne($in_workflow_id);
            $old->out_workflow_id = $model->id;
            $old->data_fim = new \yii\db\Expression('NOW()');
            $old->status = 3;
            $old->save();
            Yii::$app->getSession()->setFlash('success', 'Processo enviado com sucesso');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
        }
        return $this->redirect(['index']);
    }

    /**
     * Creates a new ProcessoWorkflow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUnsendArquivo($id)
    {
        //     $model = ProcessoWorkflow::findOne($id);
        //     $old = ProcessoWorkflow::findOne($model->in_workflow_id);
        //     if($model->delete()){
        //         $old->status = 2;
        //         $old->out_workflow_id = null;
        //         $old->out_data_hora = null;
        //         $old->data_fim = null;
        //         $old->save();
        //         Yii::$app->getSession()->setFlash('success', 'Processo enviado com sucesso');
        //     }else{
        //         Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
        //     }

        //     return $this->redirect(['index']);
    }


    /**
     * Creates a new ProcessoWorkflow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionSendArquivoProvisorio($dsp_processo_id, $in_workflow_id)
    {
        $model = new ProcessoWorkflow();
        $model->dsp_processo_id = $dsp_processo_id;
        $model->in_workflow_id = $in_workflow_id;
        $model->user_id = Yii::$app->user->identity->id;
        $model->dsp_setor_id = Setor::ARQUIVO_PROVISORIO_ID;
        $model->status = ProcessoWorkflowStatus::ARQUIVADO;
        $model->data_inicio = new \yii\db\Expression('NOW()');
        if ($model->save()) {
            $old = ProcessoWorkflow::findOne($in_workflow_id);
            $old->out_workflow_id = $model->id;
            $old->data_fim = new \yii\db\Expression('NOW()');
            $old->status = ProcessoWorkflowStatus::ENVIADO;
            $old->save();
            Yii::$app->getSession()->setFlash('success', 'Processo enviado com sucesso');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
        }

        return $this->redirect(['index']);
    }

    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionReceberBox()
    {
        $model = new ProcessoWorkflow();
        $count = 0;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->post('selection')) {
            $selection = (array)Yii::$app->request->post('selection');
            $models = ProcessoWorkflow::findAll(['id' => $selection]);
            foreach ($models as $key => $data) {
                $data->dsp_setor_id = $model->dsp_setor_id;
                $data->status = ProcessoWorkflowStatus::RECEBIDO;
                $data->data_inicio = new \yii\db\Expression('NOW()');

                if ($data->save()) {
                    $count++;
                    if (($old = ProcessoWorkflow::findOne($data->in_workflow_id)) != null) {
                        $old->data_fim = new \yii\db\Expression('NOW()');
                        $old->status = ProcessoWorkflowStatus::ENVIADO;
                        $old->save();
                    }
                }
            }
            Yii::$app->getSession()->setFlash('success', $count . ' Processo eecebido com sucesso');
            return $this->redirect(['index']);
        }

        $searchModel = new ProcessoWorkflowSearch();
        $dataProvider = $searchModel->searchReceberBox(Yii::$app->request->queryParams);

        return $this->render('receber-box', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionEnviarBox()
    {
        $model = new ProcessoWorkflow();
        $count = 0;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->post('selection')) {
            $selection = (array)Yii::$app->request->post('selection');
            $models = ProcessoWorkflow::findAll(['id' => $selection]);
            foreach ($models as $key => $value) {
                $workflow = new ProcessoWorkflow();
                $workflow->dsp_processo_id = $value->dsp_processo_id;
                $workflow->in_workflow_id = $value->id;
                $workflow->user_id = $model->user_id;
                $workflow->dsp_setor_id = $model->dsp_setor_id;
                $workflow->descricao = $model->descricao;
                $workflow->status = ProcessoWorkflowStatus::RECEBIDO;
                $workflow->data_inicio = new \yii\db\Expression('NOW()');
                if ($workflow->save()) {
                    $count++;
                    $old = ProcessoWorkflow::findOne($value->id);
                    $old->out_workflow_id = $workflow->id;
                    $old->data_fim = new \yii\db\Expression('NOW()');
                    $old->status = ProcessoWorkflowStatus::ENVIADO;
                    $old->save();
                    Yii::$app->getSession()->setFlash('success', $count . ' Processo enviado com sucesso');
                } else {
                    Yii::$app->getSession()->setFlash('warning',  ' Ocoreu um erro ao enviar o processo');
                }
            }

            return $this->redirect(['index']);
        }
        $searchModel = new ProcessoWorkflowSearch();
        $dataProvider = $searchModel->searchEnviarBox(Yii::$app->request->queryParams);

        return $this->render('enviar-box', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRealocarBox()
    {
        $model = new ProcessoWorkflow();
        $count = 0;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->post('selection')) {
            $selection = (array)Yii::$app->request->post('selection');
            $models = ProcessoWorkflow::findAll(['id' => $selection]);
            foreach ($models as $key => $value) {
                $workflow = new ProcessoWorkflow();
                $workflow->dsp_processo_id = $value->dsp_processo_id;
                $workflow->in_workflow_id = $value->id;
                $workflow->user_id = $model->user_id;
                $workflow->dsp_setor_id = $model->dsp_setor_id;
                $workflow->status = ProcessoWorkflowStatus::RECEBIDO;
                $workflow->data_inicio = new \yii\db\Expression('NOW()');
                if ($workflow->save()) {
                    $count++;
                    $old = ProcessoWorkflow::findOne($value->id);
                    $old->out_workflow_id = $workflow->id;
                    $old->data_fim = new \yii\db\Expression('NOW()');
                    $old->status = ProcessoWorkflowStatus::ENVIADO;
                    $old->save();
                }
            }
            Yii::$app->getSession()->setFlash('success', $count . ' Processo realocado com sucesso');

            return $this->redirect(['index']);
        }
        $searchModel = new ProcessoWorkflowSearch();
        $dataProvider = $searchModel->searchRealocarBox(Yii::$app->request->queryParams);

        return $this->render('realocar-box', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionArchiveBox()
    {
        $model = new ProcessoWorkflow();
        $count = 0;
        if (Yii::$app->request->post('selection')) {
            $selection = (array)Yii::$app->request->post('selection');
            $models = ProcessoWorkflow::findAll(['id' => $selection]);
            foreach ($models as $key => $value) {
                $model = new ProcessoWorkflow();
                $model->dsp_processo_id = $value->dsp_processo_id;
                $model->in_workflow_id = $value->id;
                $model->dsp_setor_id = Setor::ARQUIVO_ID;
                $model->status = ProcessoWorkflowStatus::ARQUIVADO;
                $model->data_inicio = new \yii\db\Expression('NOW()');
                if ($model->save()) {
                    $value->out_workflow_id = $model->id;
                    $value->data_fim = new \yii\db\Expression('NOW()');
                    $value->status = ProcessoWorkflowStatus::ENVIADO;
                    $value->save();
                    $count++;
                }
            }
            Yii::$app->getSession()->setFlash('success', $count . ' Processo qruivado com sucesso');
            return $this->redirect(['arquivo']);
        }

        $searchModel = new ProcessoWorkflowSearch();
        $dataProvider = $searchModel->searchArchiveBox(Yii::$app->request->queryParams);

        return $this->render('archive-box', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionSetPrioridade($id)
    {
        $model = $this->findModel($id);
        $model->prioridade = 1;
        if ($model->save(false)) {
            Yii::$app->getSession()->setFlash('success', 'Prioridade atribuido com sucesso');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'ocoreu um erro tenta de novo.');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the ProcessoWorkflow model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProcessoWorkflow the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProcessoWorkflow::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionClassificacao()
    {
        $data_incio = mktime(0, 0, 0, date('m') - 1, 20, date('Y'));
        $data_fim = mktime(23, 59, 59, date('m'), 21, date('Y'));

        $searchModel = new ProcessoWorkflowSearch();
        $searchModel->dataInicio = date('Y-m-d', $data_incio);
        $searchModel->dataFim = date('Y-m-d', $data_fim);
        $dataProvider = $searchModel->searchClassificacao(Yii::$app->request->queryParams);

        return $this->render('classificacao', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionClassificacaoSetor()
    {
        $data_incio = mktime(0, 0, 0, (date('m') - 1), 20, date('Y'));
        $data_fim = mktime(23, 59, 59, date('m'), 21, date('Y'));

        $searchModel = new ProcessoWorkflowSearch();
        $searchModel->dsp_setor_id = 4;
        $searchModel->dataInicio = date('Y-m-d', $data_incio);
        $searchModel->dataFim = date('Y-m-d', $data_fim);
        $dataProvider = $searchModel->searchClassificacaoSetor(Yii::$app->request->queryParams);
        $dspSetor = Setor::findOne($searchModel->dsp_setor_id);
        $tottalClassificacao = 1;
        foreach ($dataProvider->getModels() as $key => $value) {
            //print_r($value['user_id']);die();
            $tottalClassificacao = $tottalClassificacao + \app\modules\dsp\services\ProcessoWorkflowService::totalClassificacaoSetor($searchModel->dsp_setor_id, $value['user_id'], $searchModel->dataInicio, $searchModel->dataFim);
        }

        return $this->render('classificacao-setor', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dspSetor' => $dspSetor,
            'tottalClassificacao' => $tottalClassificacao,
        ]);
    }




    /**
     * Creates a new Recebimento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionClassificacaoUser($user_id = null, $data_incio = null, $data_fim = null)
    {
        if ($data_incio == null);
        $data_incio = mktime(0, 0, 0, (date('m') - 1), 20, date('Y'));
        if ($data_fim == null);
        $data_fim = mktime(23, 59, 59, date('m'), 21, date('Y'));
        $user = \app\models\User::findOne(($user_id == null ? Yii::$app->user->identity->id : $user_id));
        $searchModel = new ProcessoWorkflowSearch();
        $searchModel->user_id = $user->id;
        $searchModel->dataInicio = date('Y-m-d', $data_incio);
        $searchModel->dataFim = date('Y-m-d', $data_fim);
        $dataProvider = $searchModel->searchClassificacaoUser(Yii::$app->request->queryParams);

        return $this->render('classificacao-user', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user,
        ]);
    }
}
