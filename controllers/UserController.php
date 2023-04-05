<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\PasswordForm;
use app\models\SignupForm;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\AuthAssignment;
use app\models\AuthAssignmentSearch;
use yii\db\Query;


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [ 'user-list', 'chang-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [ 'index', 'create','view', 'delete', 'update','signup','reset-password','funcionario','delete-auth-assignment'],
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
     * Lists all User models.
     * @return mixed
     */
     public function actionUserList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, name AS text')
                ->from('user')
                ->where(['status'=>10])
                ->orderBy('name')
                ->filterWhere(['like', 'name', $q.'%', false])
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionFuncionario()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('funcionario', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $modelAuthAssignment = new AuthAssignment();
        if ($modelAuthAssignment->load(Yii::$app->request->post())) {
            $modelAuthAssignment->user_id = $id;
            $modelAuthAssignment->save();
        }

        $searchAuthAssignment = new AuthAssignmentSearch(['user_id'=>$id]);
        $dataProviderAuthAssignment = $searchAuthAssignment->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchAuthAssignment' => $searchAuthAssignment,
            'dataProviderAuthAssignment' => $dataProviderAuthAssignment,
            'modelAuthAssignment' => $modelAuthAssignment,
        ]);
    }



    






    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
       # print_r($mode)
        if ($model->load(Yii::$app->request->post())&& $model->save()) {
           ;
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    
    


    

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing AuthAssignment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $item_name
     * @param string $user_id
     * @return mixed
     */
    public function actionDeleteAuthAssignment($item_name, $user_id)
    {
        AuthAssignment::findOne(['item_name'=>$item_name, 'user_id'=>$user_id])->delete();

        return $this->redirect(['view',  'id' => $user_id]);
        #return $this->redirect(['index']);
    }    

    


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSignup()
    {   
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())&&($user=$model->signup())) {
             return $this->redirect(['view',  'id' =>$user->id]);
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /*Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionChangPassword()
     {
         $model = new PasswordForm;
         $modeluser = $this->findModel(Yii::$app->user->identity->id);
 
         if($model->load(Yii::$app->request->post())){
             if($model->validate()){
                 try{
                     $modeluser->password = $_POST['PasswordForm']['newpass'];
                     if($modeluser->save()){
                         Yii::$app->getSession()->setFlash(
                             'success','Palavra-chave alterado com sucesso.'
                         );
                        return $this->goHome();
                        
                     }else{
                         Yii::$app->getSession()->setFlash(
                             'error','Erro na alteração da palavra-chave.'
                         );
                        return $this->goHome();
                        
                     }
                 }catch(Exception $e){
                     Yii::$app->getSession()->setFlash(
                         'error',"{$e->getMessage()}"
                     );
                     return $this->render('changepassword',[
                         'model'=>$model
                     ]);
                 }
             }else{
                 return $this->render('changepassword',[
                     'model'=>$model
                 ]);
             }
         }else{
             return $this->render('changepassword',[
                 'model'=>$model
             ]);
         }
     }

     /*Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionResetPassword($id)
     {
         $model = new PasswordForm;
         $modeluser = $this->findModel($id);
 
         if($model->load(Yii::$app->request->post())){
             if($model->validate()){
                 try{
                     $modeluser->password = $_POST['PasswordForm']['newpass'];
                     if($modeluser->save()){
                         Yii::$app->getSession()->setFlash(
                             'success','Palavra-chave alterado com sucesso.'
                         );
                         return $this->redirect(['/user/view','id'=>$id]);
                     }else{
                         Yii::$app->getSession()->setFlash(
                             'error','Erro na alteração da palavra-chave.'
                         );
                         return $this->redirect(['site/index']);
                     }
                 }catch(Exception $e){
                     Yii::$app->getSession()->setFlash(
                         'error',"{$e->getMessage()}"
                     );
                     return $this->render('changepassword',[
                         'model'=>$model
                     ]);
                 }
             }else{
                 return $this->render('changepassword',[
                     'model'=>$model
                 ]);
             }
         }else{
             return $this->render('changepassword',[
                 'model'=>$model
             ]);
         }
     }


    


}
