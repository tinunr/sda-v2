<?php


namespace app\modules\cnt\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\cnt\models\DiarioNumero;
use app\modules\cnt\models\DiarioNumeroSearch;


class DiarioNumeroController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['get-numero'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','update'],
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all DiarioNumero models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DiarioNumeroSearch();
        $searchModel->ano = date('Y');
        $searchModel->mes = date('m');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Documento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $item_name
     * @param string $user_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['index']);
           
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    
    /**
     * Finds the Documento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $item_name
     * @param string $user_id
     * @return Documento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DiarioNumero::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    /**
     * Lists all DiarioNumero models.
     * @return mixed
     */
    public function actionGetNumero($cnt_diario_id, $bas_ano_id, $bas_mes_id)
    {
         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // $bas_ano_id = Yii::$app->formatter->asDate($data, 'Y');
        $ano = \app\models\Ano::findOne($bas_ano_id)->ano;
        // $bas_mes_id = Yii::$app->formatter->asDate($data, 'MM');
        if(($model = DiarioNumero::find()
                   ->where(['cnt_diario_id' => $cnt_diario_id, 'ano'=>$ano,'mes'=>$bas_mes_id])
                   ->asArray()
                   ->one()
                    ) !== null){
            return [
                'numero'=>$model['numero']+1,
            ];
        }
        else{
            return [
                'numero'=>1
            ];
        }
    }

    

    


}
