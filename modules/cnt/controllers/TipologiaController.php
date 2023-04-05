<?php

namespace app\modules\cnt\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\ActiveQuery;

use app\modules\cnt\models\Tipologia;
use app\modules\cnt\models\TipologiaSearch;

/**
 * TipologiaController implements the CRUD actions for Tipologia model.
 */
class TipologiaController extends Controller
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
                        'actions' => ['ajax'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','index-Tipologia','view','create','create-Tipologia','update','update-Tipologia','delete'],
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
                ],
            ],
        ];
    }    

    /**
     * Lists all Tipologia models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TipologiaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Tipologia models.
     * @return mixed
     */
    public function actionIndexTipologia()
    {
        $searchModel = new TipologiaSearch(['dsp_Tipologia_type_id'=>2]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-Tipologia', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tipologia model.
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
     * Creates a new Tipologia model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tipologia();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Tipologia model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateTipologia()
    {
        $model = new Tipologia();
        $model->dsp_Tipologia_type_id = 2;
        $model->dsp_person_id = 21;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index-Tipologia');
        }

        return $this->render('_form_Tipologia', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tipologia model.
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
     * Updates an existing Tipologia model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateTipologia($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('_form_Tipologia', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Tipologia model.
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
     * Finds the Tipologia model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tipologia the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tipologia::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
