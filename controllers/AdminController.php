<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use yii\db\Query;

class AdminController extends Controller
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
                        'actions' => ['index','update-valor-recebido'],
                        'allow' => true,
                        'roles' => ['@'],                        
                    ],
                    
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update-valor-recebido' => ['post'],
                ],
            ],
        ];
    }

    

   

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionUpdateValorRecebido()
    {
         $ok = false;
       $data = \app\modules\fin\models\Receita::find()
                ->where('valor>valor_recebido')
                ->asArray()
                ->all();
        foreach($data as $model){
          $ok = Yii::$app->FinAutoFixs->bugApdateValorRecebido($model['dsp_fataura_provisoria_id']);
        }  
        if($ok){
            Yii::$app->getSession()->setFlash('success', 'OPERAÇÃO EFETUADO COM SUCESSO');
        }else{
            Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    

    
}
