<?php

namespace app\modules\fin\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * Default controller for the `employee` module
 */
class DashboardController extends Controller
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
        $disponilidadeFinanceira = Yii::$app->FinQuery->disponibilidadeFinanceira();
        $valor_a_favor =[
            'processo'=> Yii::$app->FinQuery->valorAFavorProcesso(),
            'adiantamento'=>Yii::$app->FinQuery->valorAFavorAdiantamento(),
            'aviso_de_credito'=>Yii::$app->FinQuery->valorAFavorAvisoCredito(),
            'nota_de_credito'=>Yii::$app->FinQuery->valorAFavorNotaCredito(),
       ];
       $valor_a_dever_processo = Yii::$app->FinQuery->valorADeverProcesso();
        return $this->render('index',[
            'disponilidadeFinanceira'=>$disponilidadeFinanceira,
            'disponilidadeFinanceira'=>$disponilidadeFinanceira,
            'valor_a_favor'=>$valor_a_favor,
            'valor_a_dever_processo'=>$valor_a_dever_processo
        ]);
    }
  




}
