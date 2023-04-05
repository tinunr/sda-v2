<?php

namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\dsp\models\Setor;
use app\modules\dsp\models\SetorSearch;
use app\modules\dsp\models\SetorUser;

/**
 * EdeficiosController implements the CRUD actions for Setor model.
 */
class SetorController extends Controller
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
                        'actions' => ['setor-user'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'create-setor-user', 'delete-setor-user'],
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
                    'create-setor-user' => ['POST'],
                    'delete-setor-user' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Setor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SetorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Setor model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'SetorUser' => new SetorUser,
        ]);
    }

    /**
     * Creates a new Setor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Setor();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Setor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateSetorUser($dsp_setor_id)
    {
        $model = new SetorUser();
        $model->dsp_setor_id = $dsp_setor_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Registo Enserido com sucesso');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operção');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Updates an existing Setor model.
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
     * Deletes an existing Setor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteSetorUser($dsp_setor_id, $user_id)
    {
        if (SetorUser::find()->where(['dsp_setor_id'=>$dsp_setor_id, 'user_id'=>$user_id])->one()->delete()) {
            Yii::$app->getSession()->setFlash('success', 'registo eleminado com sucesso');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operção');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Setor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Setor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Setor::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Renders the index view for the module
     * @return string historico
     */
    public function actionSetorUser()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                $id = $parents[0];;
                $query = new \yii\db\Query;
                $query->select('B.id as id, B.descricao as name')
                    ->from('dsp_setor_user A')
                    ->leftJoin('dsp_setor B', 'A.dsp_setor_id=B.id')
                    ->where(['A.user_id' => $id])
                    ->andWhere(['B.arquivo' => 0])
                    ->groupBy('B.id')
                    ->orderBy('B.descricao')
                    ->all();
                $command = $query->createCommand();
                $out = $command->queryAll();
                echo \yii\helpers\Json::encode(['output' => $out, 'selected' => $out]);
                return;
            }
        }
        echo \yii\helpers\Json::encode(['output' => '', 'selected' => '']);
    }
}
