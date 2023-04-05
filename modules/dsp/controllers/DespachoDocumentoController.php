<?php
namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\dsp\models\DespachoDocumento;
use app\modules\dsp\models\DespachoDocumentoSearch;
use app\modules\dsp\models\Item;

/**
 * EdeficiosController implements the CRUD actions for DespachoDocumento model.
 */
class DespachoDocumentoController extends Controller
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
                        'actions' => ['index','view','create','update','delete','delete-processo-despacho-documento'],
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
                    'delete-processo-despacho-documento'=>['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all DespachoDocumento models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DespachoDocumentoSearch
    ();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DespachoDocumento model.
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
     * Creates a new DespachoDocumento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DespachoDocumento();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['index']);
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DespachoDocumento model.
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
     * Deletes an existing DespachoDocumento model.
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
     * Deletes an existing DespachoDocumento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteProcessoDespachoDocumento($id)
    {
        $model = ProcessoDespachoDocumento::findOne($id);
        //TODO eliminar ficheiro caso exista
        if ($model->delete()) {       
            Yii::$app->getSession()->setFlash('success', 'Registro eleminado com sucesso');
        } else{
            Yii::$app->getSession()->setFlash('error', 'Ocoreu um erro ao efetuar a operação');
        }
        return $this->redirect(Yii::$app->request->referrer);

        return $this->redirect(['index']);
    }

    /**
     * Finds the DespachoDocumento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DespachoDocumento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DespachoDocumento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}