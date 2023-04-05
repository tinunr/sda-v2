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
use yii2tech\spreadsheet\Spreadsheet;
use yii\data\ActiveDataProvider;

use app\models\Ano;
use app\modules\cnt\models\Razao;
use app\modules\cnt\models\RazaoSearch;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\RazaoItemSearch;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoFluxoCaixa;

/**
 * Default controller for the `employee` module
 */
class RazaoItemController extends Controller
{  

  public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['balancete','balancete-pdf','extrato','extrato-pdf','balancete-fluxo-caixa','balancete-fluxo-caixa-pdf','extrato-fluxo-caixa','extrato-fluxo-caixa-pdf'],
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
                    'create-auto' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionExtrato()
    {    
        $searchModel = new RazaoItemSearch();
        $searchModel->begin_ano = substr(date('Y'),-2);
        $searchModel->end_ano = substr(date('Y'),-2);
        $searchModel->begin_mes = date('m');
        $searchModel->end_mes = date('m');
        $searchModel->bas_template_id =1 ;
        $searchModel->bas_formato_id =1 ;
        $searchModel->cnt_plano_conta_id =1 ;
        return $this->render('_searchExtrato', [
            'model' => $searchModel,
        ]);
    }



    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionExtratoPdf()
    {    
        $formatter = Yii::$app->formatter;
         $company = Yii::$app->params['company'];
         $data = Yii::$app->request->queryParams;
         $data['RazaoItemSearch']['bas_ano_id'] = Ano::findOne($data['RazaoItemSearch']['begin_ano'])->ano;
         $titleReport =$data['RazaoItemSearch']['begin_mes'].'-'.$data['RazaoItemSearch']['bas_ano_id'].' / '.$data['RazaoItemSearch']['end_mes'].'-'.$data['RazaoItemSearch']['bas_ano_id'];
        if ($data['RazaoItemSearch']['bas_formato_id']==1) {
         $content = $this->renderPartial('extrato-pc-pdf',[
             'titleReport'=>$titleReport,
             'data'=>$data,
         ]);
        

        // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>15,
             'marginLeft'=>10,
             'marginRight'=>10,
             'filename'=>'Relatório valores Adever Emetido em: ' . date("Y-m-d H:i:s"),
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>[$company['name'].'| |'.date("d-m-Y")], 
                 'SetFooter'=>[Yii::$app->params['copyright'].' |Página {PAGENO}/{nbpg}|Emetido em: ' . date("Y-m-d H:i:s")],
             ]
         ]);
          $pdf->options = [
                 'defaultheaderline' => 0,
                 'defaultfooterline' => 0,
             ];
         return $pdf->render(); 

        } elseif($data['RazaoItemSearch']['bas_formato_id']==2){
            // print_r($data['RazaoItemSearch']['cnt_plano_conta_id']);die();

            // export data only one worksheet.
            $query = Yii::$app->CntQuery->queryExtrato($data['RazaoItemSearch']);
            $rows = $query->all();
            // alternatively, you can create DB command and execute it
            $command = $query->createCommand();
            // $command->sql returns the actual SQL
            $rows = $command->queryAll();
        $exporter = new Spreadsheet([
            'dataProvider' => new ArrayDataProvider([
                'allModels' =>$rows ,
                
            ]),
            'columns'=>[
                    // ['class' => SerialColumn::class],
                    ['attribute'=>'id','format'=>'text','header' => 'Name'],
                    ['attribute'=>'data','format'=>'date','header' => 'Data'],
                    ['attribute'=>'cnt_diario_id','format'=>'integer','header' => 'Diario'],
                    ['attribute'=>'num_doc','format'=>'integer','header' => 'Nº DOC'],
                    ['attribute'=>'terceiro','format'=>'integer','header' => 'Terceiro'],
                    ['attribute'=>'descricao','format'=>'text','header' => 'Descrição'],
                    ['attribute'=>'debito','format'=>'integer','header' => 'Debito'],
                    ['attribute'=>'credito','format'=>'integer','header' => 'Credirto'],
                ],
        ]);
        return $exporter->send('Extrato - Conta: '.$data['RazaoItemSearch']['cnt_plano_conta_id'].'.xls');        
    }
}





    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionExtratoFluxoCaixa()
    {    
        $searchModel = new RazaoItemSearch();
        $searchModel->begin_ano = substr(date('Y'),-2);
        $searchModel->end_ano = substr(date('Y'),-2);
        $searchModel->begin_mes = date('m');
        $searchModel->end_mes = date('m');
        $searchModel->bas_template_id =1 ;
        $searchModel->bas_formato_id =1 ;
        $searchModel->cnt_plano_fluxo_caixa_id =1 ;
        return $this->render('_searchExtratoFluxoCaixa', [
            'model' => $searchModel,
        ]);
    }



    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionExtratoFluxoCaixaPdf()
    {    
        $formatter = Yii::$app->formatter;
         $company = Yii::$app->params['company'];
         $data = Yii::$app->request->queryParams;
         $data['RazaoItemSearch']['bas_ano_id'] = Ano::findOne($data['RazaoItemSearch']['begin_ano'])->ano;
         $titleReport =$data['RazaoItemSearch']['begin_mes'].'-'.$data['RazaoItemSearch']['bas_ano_id'].' / '.$data['RazaoItemSearch']['end_mes'].'-'.$data['RazaoItemSearch']['bas_ano_id'];
        if ($data['RazaoItemSearch']['bas_template_id']==1) {
         $searchModel = new RazaoItemSearch($data['RazaoItemSearch']);
         $dataProvider = $searchModel->extratoContaFl(Yii::$app->request->queryParams);
         $content = $this->renderPartial('extrato-fluxo-caixa-pc-pdf',[
             'dataProvider'=>$dataProvider,
             'titleReport'=>$titleReport,
             'data'=>$data,
         ]);
        }

        elseif($data['RazaoItemSearch']['bas_template_id']==2){
         $searchModel = new RazaoItemSearch($data['RazaoItemSearch']);
         $dataProvider = $searchModel->extratoFornecedor(Yii::$app->request->queryParams);
         $content = $this->renderPartial('extrato-fluxo-caixa-pf-pdf',[
             'dataProvider'=>$dataProvider,
             'titleReport'=>$titleReport,
             'data'=>$data,
         ]); 
        }

        // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>15,
             'marginLeft'=>10,
             'marginRight'=>10,
             'filename'=>'Relatório valores Adever Emetido em: ' . date("Y-m-d H:i:s"),
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>[$company['name'].'| |'.date("d-m-Y")], 
                 'SetFooter'=>[Yii::$app->params['copyright'].' |Página {PAGENO}/{nbpg}|Emetido em: ' . date("Y-m-d H:i:s")],
             ]
         ]);
          $pdf->options = [
                 'defaultheaderline' => 0,
                 'defaultfooterline' => 0,
             ];
        
         return $pdf->render(); 
    }







    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionBalancete()
    {    
        $searchModel = new RazaoItemSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);
        $searchModel->bas_mes_id = date('m');
        $searchModel->bas_formato_id =1 ;
        $searchModel->bas_template_id =1 ;
        $searchModel->cnt_plano_conta_id =1 ;
        return $this->render('_searchBalancete', [
            'model' => $searchModel,
        ]);
    }


    





    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionBalancetePdf()
    {    
         $company = Yii::$app->params['company'];
         $data = Yii::$app->request->queryParams;
         $data['RazaoItemSearch']['bas_ano_id'] = Ano::findOne($data['RazaoItemSearch']['bas_ano_id'])->ano;
         $titleReport =$data['RazaoItemSearch']['bas_mes_id'].'-'.$data['RazaoItemSearch']['bas_ano_id'];


        if (!$data['RazaoItemSearch']['bas_template_id']==1) {
         $palnaoConta = PlanoConta::find()->orderBy('codigo')->all();
         $content = $this->renderPartial('balancete-pdf',[
             'titleReport'=>$titleReport,
             'data'=>$data,
         ]);
        }
        elseif ($data['RazaoItemSearch']['cnt_plano_conta_id']>0) {
         $palnaoConta = PlanoConta::find()->where(['id'=>$data['RazaoItemSearch']['cnt_plano_conta_id']])->orderBy('codigo')->all();
         $content = $this->renderPartial('balancete-pc-pdf',[
             'palnaoConta'=>$palnaoConta,
             'titleReport'=>$titleReport,
             'data'=>$data,
         ]);
        }


        // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>15,
             'marginLeft'=>10,
             'marginRight'=>10,
             'filename'=>'Relatório valores Adever Emetido em: ' . date("Y-m-d H:i:s"),
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>[$company['name'].'| |'.date("Y-m-d H:i:s")], 
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
    }




    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionBalanceteFluxoCaixa()
    {    
        $searchModel = new RazaoItemSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);
        $searchModel->bas_mes_id = date('m');
        $searchModel->bas_formato_id =1 ;
        $searchModel->bas_template_id =1 ;
        $searchModel->cnt_plano_fluxo_caixa_id =1 ;
        return $this->render('_searchBalanceteFluxoCaixa', [
            'model' => $searchModel,
        ]);
    }


     /**
     * Renders the index view for the module
     * @return string
     */
    public function actionBalanceteFluxoCaixaPdf()
    {    
          $company = Yii::$app->params['company'];
         $data = Yii::$app->request->queryParams;
         $data['RazaoItemSearch']['bas_ano_id'] = Ano::findOne($data['RazaoItemSearch']['bas_ano_id'])->ano;
         $titleReport =$data['RazaoItemSearch']['bas_mes_id'].'-'.$data['RazaoItemSearch']['bas_ano_id'];


        if ($data['RazaoItemSearch']['bas_template_id']==1) {
         if($data['RazaoItemSearch']['cnt_plano_fluxo_caixa_id'] >0){
             $content = $this->renderPartial('balancete-fluxo-caixa-pc-pdf',[
                 'titleReport'=>$titleReport,
                 'data'=>$data,
             ]);

         }else{
            $palnaoConta = PlanoFluxoCaixa::find()->orderBy('codigo')->all();
             $content = $this->renderPartial('balancete-fluxo-caixa-pc-pdf',[
                 'palnaoConta'=>$palnaoConta,
                 'titleReport'=>$titleReport,
                 'data'=>$data,
             ]);
         }
        }
        elseif ($data['RazaoItemSearch']['bas_template_id']==2) {
         $palnaoConta = PlanoFluxoCaixa::find()->where(['id'=>$data['RazaoItemSearch']['cnt_plano_fluxo_caixa_id']])->orderBy('codigo')->all();
         $content = $this->renderPartial('balancete-fluxo-caixa-pf-pdf',[
             'palnaoConta'=>$palnaoConta,
             'titleReport'=>$titleReport,
             'data'=>$data,
         ]);
        }


        // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>15,
             'marginLeft'=>10,
             'marginRight'=>10,
             'filename'=>'Relatório valores Adever Emetido em: ' . date("Y-m-d H:i:s"),
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>[$company['name'].'| |'.date("Y-m-d H:i:s")], 
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
    }



    



}
