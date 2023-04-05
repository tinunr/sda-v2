<?php

namespace app\modules\cnt\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;

use app\models\Parameter;

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
        $currentYear = date('Y');
        $currentMonth = date('m');
        $juro_imposto = Parameter::getValue('CONTABILIDADE','JURO_IMPOSTO');
        $dataTaxaRAI = [];
        for ($i=1; $i <= $currentMonth ; $i++) { 
        $conta6 = Yii::$app->CntQuery->getBalanceteConta($currentYear, $i,6);
        $conta7 = Yii::$app->CntQuery->getBalanceteConta($currentYear, $i,7);
        $dataTaxaRAI[$i]['ano'] = $currentYear;
        $dataTaxaRAI[$i]['mes'] = $i;
        $dataTaxaRAI[$i]['conta6'] = ($conta6['saldo_debito']-$conta6['saldo_credito']);
        $dataTaxaRAI[$i]['conta7'] = ($conta7['saldo_credito']-$conta7['saldo_debito']);
        $dataTaxaRAI[$i]['rai'] = (($conta7['saldo_credito']-$conta7['saldo_debito'])-($conta6['saldo_debito']-$conta6['saldo_credito']));
        $dataTaxaRAI[$i]['juru'] = ($juro_imposto*100).'%';
        $dataTaxaRAI[$i]['imposto'] = $juro_imposto*(($conta7['saldo_credito']-$conta7['saldo_debito'])-($conta6['saldo_debito']-$conta6['saldo_credito']));
        }
        return $this->render('index',[
            'dataTaxaRAI'=>$dataTaxaRAI,
        ]);
    }
  




}
