<?php
namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use app\models\Model;
use app\modules\dsp\models\Tarefa;
use app\modules\dsp\models\TarefaSearch;
use app\modules\dsp\models\ProcessoTarefa;

/**
 * EdeficiosController implements the CRUD actions for Tarefa model.
 */
class TarefaController extends Controller
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
                        'actions' => ['create-processo-tarefa'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','view','create','update','delete','delete-processo-tarefa'],
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
                    'delete' => ['POST'],
                    // 'delete-processo-tarefa' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Tarefa models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TarefaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tarefa model.
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
     * Creates a new Tarefa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tarefa();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tarefa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tarefa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

   /**
     * Creates a new Tarefa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateProcessoTarefa($dsp_processo_id)
    {
        $model = \app\modules\dsp\models\Processo::findOne($dsp_processo_id);
        $modelsTarefa = $model->processoTarefa;
        $flag = TRUE;
        if (Yii::$app->request->post()) {
            $oldIDs = ArrayHelper::map($modelsTarefa, 'id', 'id');
            $modelsTarefa = Model::createMultiple(ProcessoTarefa::classname());
            Model::loadMultiple($modelsTarefa, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsTarefa, 'id', 'id')));
            $transaction = \Yii::$app->db->beginTransaction();

            if (!empty($deletedIDs)) {
                ProcessoTarefa::deleteAll(['id' => $deletedIDs]);
            }
            foreach ($modelsTarefa as $item) {
                $item->dsp_processo_id = $dsp_processo_id;
                $item->descricao = Tarefa::findone($item->dsp_tarefa_id)->descricao;
                if (!($flag = $item->save())) {
                    $transaction->rollBack();
                    break;
                }
            }
            if ($flag) {
                $transaction->commit();
                return $this->redirect(['/dsp/processo/view', 'id' => $dsp_processo_id]);
            }
        }
        return $this->render('create-processo-tarefa', [
            'modelsTarefa' => (empty($modelsTarefa)) ? [new ProcessoTarefa] : $modelsTarefa,
            'model' => $model,

        ]);
    }



    /**
     * Creates a new Tarefa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionDeleteProcessoTarefa($id)
    {
        $model = ProcessoTarefa::findOne($id);
        if ($model->delete()) {       
            Yii::$app->getSession()->setFlash('success', 'Tarefa eleminado com sucesso');
        } else{
            Yii::$app->getSession()->setFlash('error', 'Ocoreu um erro ao efetuar a operação');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Tarefa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tarefa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tarefa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
