<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Model; 
use app\modules\fin\models\ReportDeverSearch;
use app\models\Documento;
use kartik\mpdf\Pdf; 

/**
 * EdeficiosController implements the CRUD actions for Recebimento model.
 */
class ReportDeverController extends Controller
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
                        'actions' => ['index','valor-adever-pdf','index-fp','valor-adever-fp-pdf','index-one','valor-adever-pdf-one'],
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

  

     /**
     * Lists all Receita models.
     * @return mixed
     */
     public function actionIndex()
     {
        
         $searchModel = new ReportDeverSearch();       
         $searchModel->dataInicio = '2000-01-01'; 
         $searchModel->dataFim = date('Y-m-d');
         $searchModel->documento_id = Documento::PROCESSO_ID;
        //  $dataProvider = $searchModel->adever(Yii::$app->request->queryParams);

         return $this->render('_search', [
             'model' => $searchModel,
            //  'dataProvider' => $dataProvider,
         ]);
     }
 
     /**
      * Lists all Receita models.
      * @return mixed
      */
     public function actionValorAdeverPdf()
     {
         $company = Yii::$app->params['company'];
         $data = Yii::$app->request->queryParams; 
		 $searchModel = new ReportDeverSearch($data['ReportDeverSearch']);  
            if($data['ReportDeverSearch']['documento_id'] == Documento::PROCESSO_ID){
                $dataProvider = $searchModel->adeverProcesso(Yii::$app->request->queryParams); 
                $titleReport = 'Listagem de valores a pagos e não recebidos - Por Processo de '.$data['ReportDeverSearch']['dataInicio'].' a '.$data['ReportDeverSearch']['dataFim'];  
                $content = $this->renderPartial('_pdfValorADeverProcesso',[
                            'dataProvider'=>$dataProvider,
                        ]);
                }elseif($data['ReportDeverSearch']['documento_id'] == Documento::FATURA_PROVISORIA_ID){
                    $dataProvider = $searchModel->adeverFp(Yii::$app->request->queryParams); 
                    $titleReport = 'Listagem de valores a pagos e não recebidos - Por Fatura Provisória de '.$data['ReportDeverSearch']['dataInicio'].' a '.$data['ReportDeverSearch']['dataFim'];  
                    $content = $this->renderPartial('_pdfValorADeverFaturaProvisoria',[
                                'dataProvider'=>$dataProvider,
                            ]);
            }
         // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>20,
             'marginLeft'=>5,
             'marginRight'=>5,
             'marginBottom'=>5,
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>['
               <div class="pdf-header" style="height: 40px; padding: 0px 0; text-align: center;">
                   <div style=" height:40px; width:40px;">
                        <img style="display: block; margin-left: 0px;" src="'.$company['logo'].'" height="40" width="40" />
                   </div>
                   <div style="height:40px;  margin-left:45px; margin-top:-40px;">
                   <p style="margin: 0px 80px; text-align: left; padding: 0px; font-size: 10px;"><strong><i class="fa fa-graduation-cap"></i> '.$company['name'].' </strong></p>
                     <p style="margin: 0px 80px; text-align: left; padding:0px;font-size: 8px;"><strong><i class="fa fa-graduation-cap"></i>'.$titleReport.'</strong></p>
                     </div>
               </div>
               '], 
                 'SetFooter'=>['<p style="margin: 0px 0px; text-align: left; padding: 0px; font-size: 8px; font-weight:100; ">'.$company['adress2'].' <br> C.P. Nº '.$company['cp'].' - Tel.: '.$company['teletone'].' - FAX: '.$company['fax'].' - Praia, Santiago </p> |Página {PAGENO}/{nbpg}|Emetido em: ' . date("Y-m-d H:i:s")],
             ]
         ]);
          $pdf->options = [
                 'defaultheaderline' => 0,
                 'defaultfooterline' => 0,
             ];
        
         return $pdf->render();  
         
     }


      /**
     * Lists all Receita models.
     * @return mixed
     */
    public function actionIndexOne()
    {
       
        $searchModel = new ReportDeverSearch();       
        $searchModel->dataInicio = '2000-01-01'; 
        $searchModel->dataFim = date('Y-m-d');
        $searchModel->documento_id = Documento::PROCESSO_ID;
       //  $dataProvider = $searchModel->adever(Yii::$app->request->queryParams);

        return $this->render('_searchOne', [
            'model' => $searchModel,
           //  'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Receita models.
     * @return mixed
     */
    public function actionValorAdeverPdfOne()
    {
        $company = Yii::$app->params['company'];
        $data = Yii::$app->request->queryParams; 
        $searchModel = new ReportDeverSearch($data['ReportDeverSearch']);  
           if($data['ReportDeverSearch']['documento_id'] == Documento::PROCESSO_ID){
               $dataProvider = $searchModel->adeverProcesso(Yii::$app->request->queryParams); 
               $titleReport = 'Listagem de valores a pagos e não recebidos - Por Processo de '.$data['ReportDeverSearch']['dataInicio'].' a '.$data['ReportDeverSearch']['dataFim'];  
               $content = $this->renderPartial('_pdfValorADeverProcesso',[
                           'dataProvider'=>$dataProvider,
                       ]);
               }elseif($data['ReportDeverSearch']['documento_id'] == Documento::FATURA_PROVISORIA_ID){
                   $dataProvider = $searchModel->adeverFp(Yii::$app->request->queryParams); 
                   $titleReport = 'Listagem de valores a pagos e não recebidos - Por Fatura Provisória de '.$data['ReportDeverSearch']['dataInicio'].' a '.$data['ReportDeverSearch']['dataFim'];  
                   $content = $this->renderPartial('_pdfValorADeverFaturaProvisoria',[
                               'dataProvider'=>$dataProvider,
                           ]);
           }
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_LANDSCAPE, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'marginTop'=>20,
            'marginLeft'=>5,
            'marginRight'=>5,
            'marginBottom'=>5,
             'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
             'options' => ['title' => 'Relatório Processo'],
            'methods' => [ 
                'SetHeader'=>['
              <div class="pdf-header" style="height: 40px; padding: 0px 0; text-align: center;">
                  <div style=" height:40px; width:40px;">
                       <img style="display: block; margin-left: 0px;" src="'.$company['logo'].'" height="40" width="40" />
                  </div>
                  <div style="height:40px;  margin-left:45px; margin-top:-40px;">
                  <p style="margin: 0px 80px; text-align: left; padding: 0px; font-size: 10px;"><strong><i class="fa fa-graduation-cap"></i> '.$company['name'].' </strong></p>
                    <p style="margin: 0px 80px; text-align: left; padding:0px;font-size: 8px;"><strong><i class="fa fa-graduation-cap"></i>'.$titleReport.'</strong></p>
                    </div>
              </div>
              '], 
                'SetFooter'=>['<p style="margin: 0px 0px; text-align: left; padding: 0px; font-size: 8px; font-weight:100; ">'.$company['adress2'].' <br> C.P. Nº '.$company['cp'].' - Tel.: '.$company['teletone'].' - FAX: '.$company['fax'].' - Praia, Santiago </p> |Página {PAGENO}/{nbpg}|Emetido em: ' . date("Y-m-d H:i:s")],
            ]
        ]);
         $pdf->options = [
                'defaultheaderline' => 0,
                'defaultfooterline' => 0,
            ];
       
        return $pdf->render();  
        
    }


 
 
  

    
}
