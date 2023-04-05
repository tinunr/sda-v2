<?php

namespace app\modules\dsp\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\modules\dsp\repositories\ProcessoRepository;
/**
 * Default controller for the `employee` module
 */
class DefaultController extends Controller
{  

  public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['processo-interno','processo-externo'],
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
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {   
        return $this->render('index');
    }
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionProcessoInterno()
    {   
        return $this->render('processo-interno',[
            'personProcessos'=>ProcessoRepository::listPersonProcessoInterno(),
            'listSetorProcessoInterno'=>ProcessoRepository::listSetorProcessoInterno()
        ]);
    }
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionProcessoExterno()
    {   
        return $this->render('processo-externo',[
            'listEstadoProcessoExterno'=>ProcessoRepository::listEstadoProcessoExterno(),
        ]);
    }
  





}
