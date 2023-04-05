<?php

namespace app\modules\cnt\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\db\ActiveQuery;
use kartik\mpdf\Pdf;
use app\modules\cnt\models\RazaoLancamento;
use app\modules\cnt\models\RazaoLancamentoSearch;
use app\models\Ano;
use yii\db\Query;

/**
 * Default controller for the `employee` module
 */
class RazaoLancamentoController extends Controller
{  

  public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','create','update','view'],
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
        $model = new RazaoLancamento();
        $model->bas_ano_id = substr(date('Y'),-2);
        $model->bas_mes_id = date('m');
        return $this->render('index', [
            'model' => $model,
        ]);
    }
  

    /**
     * Creates a new RazaoLancamento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        set_time_limit(0);
        
        $model = new RazaoLancamento();
        $request = Yii::$app->request->queryParams;
        $data = $request['RazaoLancamento'];
        
        if (!empty($data)) {

            RazaoLancamento::deleteAll(['bas_ano_id'=>$data['bas_ano_id'], 'bas_mes_id'=>$data['bas_mes_id']]);
            $ano = Ano::findOne($data['bas_ano_id'])->ano;
            $dataR = Yii::$app->CntQuery->getRazao($ano, $data['bas_mes_id']);
        foreach ($dataR as $key => $value) {
                $saldo = 0;
                $insert = new RazaoLancamento();
                $insert->bas_ano_id = $data['bas_ano_id'];
                $insert->bas_mes_id = $data['bas_mes_id'];
                $insert->cnt_plano_conta_id = $value['cnt_plano_conta_id'];
                $insert->cnt_plano_terceiro_id =empty($value['cnt_plano_terceiro_id'])?null:$value['cnt_plano_terceiro_id'];
                $insert->debito = $value['debito'];
                $insert->credito =$value['credito'] ;
                $insert->debito_acumulado = $value['debito_acumulado'];
                $insert->credito_acumulado = $value['credito_acumulado'] ;
                $saldo = $value['debito_acumulado'] - $value['credito_acumulado'];
                if($saldo>=0){
                    $insert->saldo_d = $saldo ;
                    $insert->saldo_c = 0 ;
                }else{
                    $insert->saldo_d = 0;
                    $insert->saldo_c = -($saldo);
                } 
                if(!$insert->save()){
                    $eerors = [
                        'errors'=>$insert->errors,
                        'ietm'=>$value,
                    ];
                    print_r($eerors);die();
                }
            }
            Yii::$app->getSession()->setFlash('success',' Razão atualizado com sucesso.');
        }        

        return $this->redirect(Yii::$app->request->referrer);

        
    }





}
