<?php


namespace app\controllers;

use Yii;
use app\models\DocumentoNumero;
use app\models\DocumentoNumeroSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class DocumentoNumeroController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['get-number'],
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
     * Lists all DocumentoNumero models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentoNumeroSearch();
        $searchModel->ano = substr(date('Y'),-2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Updates an existing Person model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionUpdate($bas_documento_id,$ano)
     {
         $model = $this->findModel($bas_documento_id,$ano);
 
         if ($model->load(Yii::$app->request->post()) && $model->save()) {
             return $this->redirect(['index']);
         }
 
         return $this->render('update', [
             'model' => $model,
         ]);
     }

     /**
     * Finds the Despacho model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Despacho the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($bas_documento_id,$ano)
    {
        if (($model = DocumentoNumero::findOne(['bas_documento_id'=>$bas_documento_id,'ano'=>$ano])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all DiarioNumero models.
     * @return mixed
     */
    public function actionGetNumber($bas_documento_id, $data = null)
    {
        
        $ano =!empty($data)?substr(date('Y', strtotime($data)),-2):substr(date('Y'),-2);
        if(($model = DocumentoNumero::findOne(['bas_documento_id' => $bas_documento_id, 'ano'=>$ano])) !== null){
           
        }else{
            $model = new DocumentoNumero();
            $model->ano = $ano;
            $model->bas_documento_id = $bas_documento_id;
            $model->numero = 0;
            $model->save();
           
            
        }

        return $model->numero +1;
    }

    


}
