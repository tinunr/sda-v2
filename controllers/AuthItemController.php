<?php

namespace app\controllers;

use Yii;
use app\models\AuthItem;
use app\models\AuthItemChild;
use app\models\AuthItemSearch;
use app\models\AuthItemChildSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;


/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthItemController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','grupo','view','create','create-groupe','update','delete','delete-item-child'],
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
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch(['type'=>2]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionGrupo()
    {
        $searchModel = new AuthItemSearch(['type'=>1]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('grupo', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

   
    /**
     * Displays a single AuthItem model.
     * @param integer $id
     * @param string $AuthItem
     * @return mixed
     */
    public function actionView($id)
    {   
        $model = $this->findModel($id);
        $msg = 0;

        $modelAuthItemChild = new AuthItemChild();

        

        $modelAuthItemChilddata = AuthItemChild::find()->where(['parent'=>$model->name])->orderBy('child')->all();

        return $this->render('view', [
            'model' => $model,
            'modelAuthItemChild'=>$modelAuthItemChild,
            'modelAuthItemChilddata' => $modelAuthItemChilddata,
            'msg' => $msg,
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
         $model = new AuthItem();
        $model->type = 2;
        if ($model->load(Yii::$app->request->post())&&$model->save()) {   
               $modelAuthItemChild = new AuthItemChild();
               $modelAuthItemChild->parent = 'ADIMINISTRATOR';
               $modelAuthItemChild->child = $model->name;
                try {
                           $modelAuthItemChild->save();
                }catch(\Exception $e){ 
                        $msg = 1;
                        Yii::$app->getSession()->setFlash('error', 'Este registo ja encontra');
                }

        return $this->redirect(['index']);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }



     /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateGroupe()
    {
        $model = new AuthItem();
        $model->type = 1;
        if ($model->load(Yii::$app->request->post())&&$model->save()) {
        return $this->redirect(['grupo']);
            return $this->redirect(['index']);
        } else {
            return $this->render('create_groupe', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string $AuthItem
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->list = ArrayHelper::map(AuthItemChild::find()->where(['parent'=>$model->name])->all(), 'child','child');
        if ($model->load(Yii::$app->request->post())&&$model->save()) {
            $atthItemChild= AuthItemChild::find()->where(['parent'=>$model->name])->all(); 
            foreach ($atthItemChild as $key => $value) {
                 $value->delete();
             } 
                 foreach ($model->list as $key => $list) {
             // print_r($list);die();
                     $listModel  = new AuthItemChild();
                     $listModel->parent  = $model->name;
                     $listModel->child  = $list;
                     if(!$listModel->save()){
                        print_r($listModel->errors);die();
                     }
                 }

            return $this->redirect(['view','id'=>$model->name]);
            
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $AuthItem
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
        #return $this->redirect(['index']);
    }

     /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $AuthItem
     * @return mixed
     */
    public function actionDeleteItemChild($parent, $child)
    {
        $modelAuthItemChild = AuthItemChild::findOne(['parent'=>$parent,'child'=>$child]);
        $modelAuthItemChild->delete();
        $msg = 1;
        Yii::$app->getSession()->setFlash('success', 'REGISTO REMOVIDO COM SUCESSO');

        return $this->redirect(['view', 'id' => $parent]);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $AuthItem
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne(['name' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
