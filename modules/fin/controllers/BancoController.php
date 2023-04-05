<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use app\modules\fin\models\Banco;
use app\modules\fin\models\BancoSearch;
use app\modules\fin\models\BancoDocumentoPagamento;
/**
 * EdeficiosController implements the CRUD actions for Banco model.
 */
class BancoController extends Controller
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
                        'actions' => ['banco-list'],
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
     * Lists all Banco models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BancoSearch
    ();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Banco model.
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
     * Creates a new Banco model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Banco();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            BancoDocumentoPagamento::deleteAll('fin_banco_id='.$id);
              foreach ($model->fin_documento_pagamento_id  as $key => $value) {
                   $data = new BancoDocumentoPagamento();                   
                   $data->fin_banco_id = $value;
                   $data->fin_documento_pagamento_id = $model->id;
                   $data->save();
                }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Banco model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->fin_documento_pagamento_id = ArrayHelper::map(BancoDocumentoPagamento::find()->joinWith('documentoPagamento')->where(['fin_banco_id'=>$id])->all(), 'documentoPagamento.id','documentoPagamento.id');

        // print_r($model->fin_documento_pagamento_id);die();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            BancoDocumentoPagamento::deleteAll('fin_banco_id='.$id);
              foreach ($model->fin_documento_pagamento_id  as $key => $value) {
                   $data = new BancoDocumentoPagamento();                   
                   $data->fin_banco_id = $id;
                   $data->fin_documento_pagamento_id = $value;
                   $data->save();
                }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Banco model.
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
     * Finds the Banco model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Banco the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Banco::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    // THE CONTROLLER
    public function actionBancoList()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = $parents[0];

                $query = new Query;
                $query->select('B.id as id, B.descricao as name')
                    ->from('fin_banco_documento_pagamento A')
                    ->leftJoin('fin_banco B', 'A.fin_banco_id=B.id')
                    ->where(['A.fin_documento_pagamento_id'=> $id])
                    ->orderBy('B.descricao')
                    ->all();
                $command = $query->createCommand();
                $out = $command->queryAll();
                // echo 
                return Json::encode(['output'=>$out, 'selected'=>'']);;
            }
        }
        return Json::encode(['output'=>'', 'selected'=>'']);
    }
}
