<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use yii\db\Query;

class DefaultController extends Controller
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
                        'actions' => ['index','documentacao'],
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionDocumentacao()
    {
        return $this->render('documentacao');
    }

    

    

    
}