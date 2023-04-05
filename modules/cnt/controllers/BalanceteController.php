<?php

namespace app\modules\cnt\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\db\ActiveQuery;
use yii\db\Query;
use app\models\Model;
use yii\data\ArrayDataProvider;
use kartik\mpdf\Pdf;
use yii\data\ActiveDataProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use app\models\Ano;
use app\models\Mes;
use app\modules\cnt\models\Razao;
use app\modules\cnt\models\RazaoSearch;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\RazaoItemSearch;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoFluxoCaixa;

/**
 * Default controller for the `employee` module
 */
class BalanceteController extends Controller
{  

  public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','index-pdf','index-fluxo-caixa','index-fluxo-caixa-pdf'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->AuthService->permissiomHandler() === true;
                        }
                    ],
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
        $searchModel = new RazaoItemSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);
        $searchModel->bas_mes_id = date('m');
        $searchModel->bas_formato_id =1 ;
        $searchModel->bas_template_id =1 ;
        // $searchModel->cnt_plano_conta_id =1 ;
        return $this->render('_searchBalancete', [
            'model' => $searchModel,
        ]);
    }


    





    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndexPdf()
    {    
         $company = Yii::$app->params['company'];
         $data = Yii::$app->request->queryParams;
         $ano = Ano::findOne($data['RazaoItemSearch']['bas_ano_id'])->ano;
         $data['RazaoItemSearch']['ano'] = $ano;
         $bas_mes_descricao = Mes::findOne($data['RazaoItemSearch']['bas_mes_id'])->descricao;
        //  print_r($data);die();
         if($data['RazaoItemSearch']['bas_formato_id']==1){
        if ($data['RazaoItemSearch']['bas_template_id']==1) {
             $content = $this->renderPartial('balancete-pc-pdf',[
                 'data'=>$data,
                 'bas_ano'=>$ano,
                 'bas_mes_descricao'=>$bas_mes_descricao,
             ]);
         }elseif ($data['RazaoItemSearch']['bas_template_id']==2) {
         $titleReport =$ano.'-'.$data['RazaoItemSearch']['bas_ano_id'];
             $content = $this->renderPartial('balancete-person-pdf',[
                 'titleReport'=>$titleReport,
                 'data'=>$data,
                 'bas_ano'=>$ano,
                 'bas_mes_descricao'=>$bas_mes_descricao,
             ]);
         }

         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>25,
             'marginLeft'=>10,
             'marginRight'=>10,
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>[$company['name'].'| <br><p>Balancete de Verificação do Razão Geral</p> |Data: '.date("d/m/Y").'<br> Ano: '.date("Y").'<br> Mês: '.$bas_mes_descricao], 
                 'SetFooter'=>['<p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong><i class="fa fa-graduation-cap"></i>'.$company['adress2'].'</strong>
                      <br><strong><i class="fa fa-graduation-cap"></i> C.P. Nº '.$company['cp'].' - Tel.: '.$company['teletone'].' - FAX: '.$company['fax'].' - Praia, Santiago</strong>
                      </p> |Página {PAGENO}/{nbpg}|Emetido em: ' . date("Y-m-d H:i:s")],
             ]
         ]);
          $pdf->options = [
                 'defaultheaderline' => 0,
                 'defaultfooterline' => 0,
             ];
        
         return $pdf->render(); 


        }elseif($data['RazaoItemSearch']['bas_formato_id']==2){
            if($data['RazaoItemSearch']['bas_template_id']==1){

            $return = \app\modules\cnt\widget\BalanceteContaExcel::widget([
                    'ano' =>$ano,
                    'data'=>$data['RazaoItemSearch'],
                ]);
            $this->redirect(Url::to('@web/Spreadsheet/Balancete.xlsx'))->send(); 
            }elseif($data['RazaoItemSearch']['bas_template_id']==2){
                $return = \app\modules\cnt\widget\BalancetePersonExcel::widget([
                    'ano' =>$ano,
                    'data'=>$data['RazaoItemSearch'],
                ]);
            $this->redirect(Url::to('@web/Spreadsheet/EcxtratoPerson.xlsx'))->send(); 
            }


        }
    }




    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndexFluxoCaixa()
    {    
        $searchModel = new RazaoItemSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);
        $searchModel->bas_mes_id = date('m');
        $searchModel->bas_formato_id =1 ;
        $searchModel->bas_template_id =1 ;
        // $searchModel->cnt_plano_fluxo_caixa_id =1 ;
        return $this->render('_searchBalanceteFluxoCaixa', [
            'model' => $searchModel,
        ]);
    }


     /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndexFluxoCaixaPdf()
    {    
        $company = Yii::$app->params['company'];
        $data = Yii::$app->request->queryParams;
        $bas_ano = Ano::findOne($data['RazaoItemSearch']['bas_ano_id'])->ano;
        $bas_mes_descricao = Mes::findOne($data['RazaoItemSearch']['bas_mes_id'])->descricao;

        if ($data['RazaoItemSearch']['bas_formato_id']==1) {
             $content = $this->renderPartial('balancete-fluxo-caixa',[
                 'data'=>$data,
                 'bas_ano'=>$bas_ano,
             ]);
        // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>25,
             'marginLeft'=>10,
             'marginRight'=>10,
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                'SetHeader'=>[$company['name'].'| <br><p>Balancete de Verificação do Razão Geral</p> |Data: '.date("d/m/Y").'<br> Ano: '.date("Y").'<br> Mês: '.$bas_mes_descricao], 
                 'SetFooter'=>['<p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong><i class="fa fa-graduation-cap"></i>'.$company['adress2'].'</strong>
                      <br><strong><i class="fa fa-graduation-cap"></i> C.P. Nº '.$company['cp'].' - Tel.: '.$company['teletone'].' - FAX: '.$company['fax'].' - Praia, Santiago</strong>
                      </p> |Página {PAGENO}/{nbpg}|Emetido em: ' . date("Y-m-d H:i:s")],
             ]
         ]);
          $pdf->options = [
                 'defaultheaderline' => 0,
                 'defaultfooterline' => 0,
             ];
        
         return $pdf->render(); 

        }elseif($data['RazaoItemSearch']['bas_formato_id']==2){
            $return = \app\modules\cnt\widget\BalanceteFluxoCaixaExcel::widget([
                    'bas_ano' =>$bas_ano,
                    'bas_ano_id'=>$data['RazaoItemSearch']['bas_ano_id'],
                    'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
                    'cnt_plano_fluxo_caixa_id'=>$data['RazaoItemSearch']['cnt_plano_fluxo_caixa_id'],
                    'cnt_plano_terceiro_id'=>$data['RazaoItemSearch']['cnt_plano_terceiro_id'],
                ]);
            $this->redirect(Url::to('@web/Spreadsheet/BalanceteFluxoCaixaExcel.xlsx'))->send(); 
        }

    }

    /**
     * Finds the Diario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Diario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findData($filters)
    {
         $raz_detalhado = (new \yii\db\Query())
                        ->select(['A.cnt_plano_conta_id'
                                ,'A.cnt_plano_terceiro_id'
                                ,'C.path'
                                ,'bas_mes_id'=>'YEAR(B.documento_origem_data)'
                                ,'bas_mes_id'=>'MONTH(B.documento_origem_data)'
                                ,'debito'=>"(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )"
                                ,'credito'=>"(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"])
                        ->from(['C'=>'cnt_plano_conta'])
                        ->leftJoin(['A'=>'cnt_razao_item'],'C.id = A.cnt_plano_conta_id')
                        ->leftJoin(['B'=>'cnt_razao'],'A.cnt_razao_id=B.id')
                        ->where(['B.status'=>1]);

        $razao_agrupado = (new \yii\db\Query())
                        ->select([
                            'x.cnt_plano_conta_id'
                            ,'x.path'
                            ,'x.cnt_plano_terceiro_id'
                            ,'debito'=>'sum( CASE WHEN x.bas_mes_id = 03 THEN x.debito    ELSE 0.00  END)'
                            ,'credito'=>'sum( CASE WHEN x.bas_mes_id = 03 THEN x.credito    ELSE 0.00  END)'
                            ,'debito_acumulado'=>'sum(x.debito)'
                            ,'credito_acumulado'=>'sum(x.credito)'
                            ,'saldo_debito'=>'(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
                            ,'saldo_credito'=>'(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
                        ])
                        ->from(['x'=>$raz_detalhado])
                        ->groupBy(['x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id']);

             $balancete = (new \yii\db\Query())
                        ->select([
                            'P.id'
                            ,'P.descricao'
                            ,'debito'=>'sum(xx.debito)'
                            ,'credito'=>'sum(xx.credito)'
                            ,'debito_acumulado'=>'sum(xx.debito_acumulado)'
                            ,'credito_acumulado'=>'sum(xx.credito_acumulado)'
                            ,'saldo_debito'=>'sum(xx.saldo_debito)'
                            ,'saldo_credito'=>'sum(xx.saldo_credito)'
                        ])
                        ->from(['xx'=>$razao_agrupado])
                        ->leftJoin(['P'=>'cnt_plano_conta'],['LIKE','xx.path',new \yii\db\Expression("CONCAT('P.path','%')"), FALSE])
                        ->groupBy(['P.id' , 'P.descricao'])
                        ->orderBy('P.path')
                        ->all();



    return $balancete;

    }



    



}