<?php

namespace app\modules\dsp\controllers;

use Yii;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\PersonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\ActiveQuery;
use yii\db\Query;
/**
 * PersonController implements the CRUD actions for Person model.
 */
class PersonController extends Controller
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
                        'actions' => ['person-list','person-despesa','person-receita','get-person'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','view','create','update','delete','ajax'],
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
     public function actionPersonList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(['id', new \yii\db\Expression("CONCAT(`nome`, '  - ', `nif`) as text")])
                ->from('dsp_person')
                // ->where(['!=','nif',null])
                ->orderBy('nome')
                ->filterWhere(['like', 'nome', $q.'%', false])
                ->all();
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Person::findOne($id)->nome];
        }
        
        return $out;
    }

    /**
     * Lists all Person models.
     * @return mixed
     */
     public function actionPersonDespesa($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('dsp_person.id as id, dsp_person.nome AS text')
                ->from('dsp_person')
                ->join('LEFT JOIN', 'fin_despesa', 'dsp_person.id = fin_despesa.dsp_person_id')
                ->where(['>','fin_despesa.saldo' ,0])
                ->groupBy('dsp_person.id')
                ->orderBy('dsp_person.nome')
                ->filterWhere(['like', 'dsp_person.nome', $q.'%', false])
                ->limit(10);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Person::findOne($id)->nome];
        }
        
        return $out;
    }


    /**
     * Lists all Person models.
     * @return mixed
     */
     public function actionPersonReceita($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('dsp_person.id as id, dsp_person.nome AS text')
                ->from('dsp_person')
                ->join('LEFT JOIN', 'fin_receita', 'dsp_person.id = fin_receita.dsp_person_id')
                ->where(['>','fin_receita.saldo' ,0])
                ->groupBy('dsp_person.id')
                ->orderBy('dsp_person.nome')
                ->filterWhere(['like', 'dsp_person.nome', $q.'%', false])
                ->limit(10);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Person::findOne($id)->nome];
        }
        
        return $out;
    }

    /**
     * Lists all Person models.
     * @return mixed
     */
    public function actionIndex()
    { 
        $searchModel = new PersonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Person model.
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
     * Creates a new Person model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Person();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Person model.
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
     * Deletes an existing Person model.
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
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Person the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Person::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

     /**
     * Lists all Person models.
     * @return mixed
     */
     public function actionGetPerson($q = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, nome AS text')
                ->from('dsp_person')
                ->orderBy('nome')
                ->filterWhere(['id'=> $q])
                ->limit(1);
            $command = $query->createCommand();
           return $command->queryOne();
        }
        
    }
}
