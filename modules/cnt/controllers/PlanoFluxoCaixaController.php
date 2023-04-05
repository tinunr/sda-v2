<?php

namespace app\modules\cnt\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Query;

use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\cnt\models\PlanoFluxoCaixaSearch;

/**
 * EdeficiosController implements the CRUD actions for PlanoFluxoCaixa model.
 */
class PlanoFluxoCaixaController extends Controller
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
                        'actions' => ['plano-fluxo-caixa-list','json-list'],
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Person models.
     * @return mixed
     */
     public function actionPlanoFluxoCaixaList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, codigo AS text')
                ->from('cnt_plano_fluxo_caixa')
                ->orderBy('codigo')
                ->filterWhere(['like', 'codigo', $q.'%', false])
                ->all();
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        
        return $out;
    }

    /**
     * Lists all Person models.
     * @return mixed
     */
     public function actionJsonList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(['id', new \yii\db\Expression("CONCAT(id, ' - ',descricao) as text")])
                ->from('cnt_plano_fluxo_caixa')
                ->orderBy('codigo')
                ->orfilterWhere(['like', 'descricao', $q.'%', false])
                ->orfilterWhere(['like', 'codigo', $q.'%', false])
                ->all();
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        
        return $out;
    }

    /**
     * Lists all PlanoFluxoCaixa models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlanoFluxoCaixaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlanoFluxoCaixa model.
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
     * Creates a new PlanoFluxoCaixa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlanoFluxoCaixa();

        if ($model->load(Yii::$app->request->post())) {
            $model->codigo = (string)$model->id;
            if($model->save()){
                return $this->redirect(['index']);
            }
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlanoFluxoCaixa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
    
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())&&$model->save()) {
                return $this->redirect(['index']);
        }


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlanoFluxoCaixa model.
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
     * Finds the PlanoFluxoCaixa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PlanoFluxoCaixa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlanoFluxoCaixa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
