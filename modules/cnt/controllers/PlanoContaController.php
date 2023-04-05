<?php

namespace app\modules\cnt\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\httpclient\XmlParser;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\db\ActiveQuery;
use yii\db\Query;

use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoContaSearch;

/**
 * PlanoContaController implements the CRUD actions for PlanoConta model.
 */
class PlanoContaController extends Controller
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
                        'actions'=>['get-data','plano-conta-list','plano-conta-list-operacional','json-list'],
                        'allow' => true,                        
                    ],
                    [
                        'actions' => ['index','view','create','update','delete','conf-abertura','conf-abertura-delete','conf-fecho','conf-fecho-delete'],
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
                    'conf-abertura-delete' => ['POST'],
                    'conf-fecho-delete' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Lists all Person models.
     * @return mixed
     */
     public function actionPlanoContaList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, codigo AS text')
                ->from('cnt_plano_conta')
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
     public function actionPlanoContaListOperacional($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, codigo AS text')
                ->from('cnt_plano_conta')
                ->where(['cnt_plano_conta_tipo_id'=>2])
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
                ->from('cnt_plano_conta')
                ->orderBy('codigo')
                ->orfilterWhere(['like', 'codigo', $q.'%', false])
                ->orfilterWhere(['like', 'descricao', $q.'%', false])
                ->all();
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        
        return $out;
    }
    


    /**
     * Lists all PlanoConta models.
     * @return mixed
     */
    public function actionIndex()
    {

        Yii::$app->session->remove('urlIndex');
        $searchModel = new PlanoContaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlanoConta model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (empty(Yii::$app->session->has('urlIndex'))) {
               Yii::$app->session->open();
               Yii::$app->session->set('urlIndex', Yii::$app->request->referrer);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'urlIndex' => Yii::$app->session->get('urlIndex'),
        ]);
    }

    


    /**
     * Creates a new PlanoConta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlanoConta();
         if ($model->load(Yii::$app->request->post())) {
             $model->codigo = $model->id;
             if( $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);

             }
        } 

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing PlanoConta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {     
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
             $model->codigo = $model->id;  
             if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]); 
             } 
                 print_r($model->errors);die();
              
        }  
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlanoConta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($numero,$ano,$dsp_desembaraco_id)
    {
        $this->findModel($numero,$ano,$dsp_desembaraco_id)->delete();

        return $this->redirect(['index']);
    }
    

    /**
     * Finds the PlanoConta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PlanoConta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlanoConta::findOne(['id'=>$id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Displays a single PlanoConta model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionConfAbertura()
    {   
        $model = new PlanoConta();
        if ($model->load(Yii::$app->request->post())) {
            $newModel = PlanoConta::findOne($model->id);
            $newModel->cnt_plano_conta_abertura = $model->cnt_plano_conta_abertura;
            $newModel->save();
        }

        $model = new PlanoConta();
        $data = PlanoConta::find()
                ->where(['cnt_plano_conta_tipo_id'=>2])
                ->andWhere('id!=cnt_plano_conta_abertura')
                ->all();
        return $this->render('conf-abertura', [
            'model' => $model,
            'data' => $data,
        ]);
    }

    /**
     * Displays a single PlanoConta model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionConfAberturaDelete($id)
    {   
       $newModel = PlanoConta::findOne($id);
       $newModel->cnt_plano_conta_abertura = $id;
       $newModel->save();

        return $this->redirect(['conf-abertura']);
    }



    /**
     * Displays a single PlanoConta model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionConfFecho()
    {
        $model = new PlanoConta();
        if ($model->load(Yii::$app->request->post())) {
            $newModel = PlanoConta::findOne($model->id);
            $newModel->cnt_plano_conta_fecho = $model->cnt_plano_conta_fecho;
            $newModel->save();
        }

        $model = new PlanoConta();
        $data = PlanoConta::find()
                ->where(['cnt_plano_conta_tipo_id'=>2])
                ->andWhere('id!=cnt_plano_conta_fecho')
                ->all();
        return $this->render('conf-fecho', [
            'model' => $model,
            'data' => $data,
        ]);
    }



    /**
     * Displays a single PlanoConta model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionConfFechoDelete($id)
    {   
       $newModel = PlanoConta::findOne($id);
       $newModel->cnt_plano_conta_fecho = $id;
       $newModel->save();

        return $this->redirect(['conf-fecho']);
    }

    
}
