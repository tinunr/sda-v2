<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Model;
use app\modules\fin\models\FaturaProvisoriaSearch;
use app\modules\fin\models\RecebimentoSearch;
use app\modules\fin\models\NotaCreditoSearch;
use app\modules\fin\models\AvisoCreditoSearch;
use app\modules\dsp\models\ProcessoSearch;
use app\modules\fin\models\ReportSearch;
use app\models\Documento;
use kartik\mpdf\Pdf;

/**
 * EdeficiosController implements the CRUD actions for Recebimento model.
 */
class ReportController extends Controller
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
                        'actions' => ['valor-afavor','valor-afavor-pdf','recebimento','recebimento-pdf','valor-adever','valor-adever-pdf','valor-adever-new','valor-adever-new-pdf','fatura-provisoria','fatura-provisoria-pdf','valor-afavor-adever','valor-afavor-adever-pdf','adiantamento'],
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
     public function actionValorAfavor()
     {
        
         $searchModel = new ReportSearch();
         // $searchModel->bas_ano_id = substr(date('Y'),-2);       
         $searchModel->documento_id = 1;       
         $searchModel->dataInicio = '2001-01-01';
         $searchModel->dataFim = date('Y-m-d');
         return $this->render('_searchSaldoAfavor', [
             'model' => $searchModel,
         ]);
     }
 
     /**
      * Lists all Receita models.
      * @return mixed
      */
     public function actionValorAfavorPdf()
     {
         $company = Yii::$app->params['company'];
         $titleReport = '';
         $data = Yii::$app->request->queryParams;
         if ($data['ReportSearch']['documento_id'] ==Documento::RECEBIMENTO_ID) {
             $titleReport ='Listagem de valores a Favor dos clientes - Por Adiantamentos de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
             if ($data['ReportSearch']['por_person']) {
                 $searchModel = new RecebimentoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAfavorAdPerson(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-ad-person',[
                     'person'=>$dataProvider,
                     'data' =>$data,
                 ]);
             }else{
                 $searchModel = new RecebimentoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAfavorAd(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-ad',[
                     'dataProvider'=>$dataProvider,
                 ]);
             }
         }elseif($data['ReportSearch']['documento_id'] ==Documento::AVISO_CREDITO) {
             $titleReport ='Listagem de valores a Favor dos clientes - Por Aviso de Credito de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
             if ($data['ReportSearch']['por_person']) {
                 $searchModel = new NotaCreditoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAfavorAcPerson(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-ac-person',[
                     'person'=>$dataProvider,
                     'data' =>$data,
                 ]);
             }else{
                $searchModel = new NotaCreditoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAfavorAc(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-ac',[
                     'dataProvider'=>$dataProvider,
                 ]);
             }
         }elseif ($data['ReportSearch']['documento_id'] ==Documento::PROCESSO_ID) {
             $titleReport ='Listagem de valores a Favor dos clientes - Por Processo de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
             if ($data['ReportSearch']['por_person']) {
                 $searchModel = new ProcessoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->relatorioPerson(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-pr-person',[
                     'person'=>$dataProvider,
                     'data' =>$data,
                 ]);
             }else{
                 $searchModel = new ProcessoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->relatorio(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-pr',[
                     'dataProvider'=>$dataProvider,
                 ]);
             }
         }elseif ($data['ReportSearch']['documento_id'] ==Documento::FATURA_PROVISORIA_ID) {

            $titleReport ='Listagem de valores a Favor dos clientes - Por Factura Provisória de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
             if ($data['ReportSearch']['por_person']) {
                 $searchModel = new FaturaProvisoriaSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAfavorFpPerson(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-fp-person',[
                     'person'=>$dataProvider,
                     'data' =>$data,
                 ]);
             }else{
                $searchModel = new FaturaProvisoriaSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAfavorFp(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-fp',[
                     'dataProvider'=>$dataProvider,
                 ]);
             }
         }elseif ($data['ReportSearch']['documento_id'] ==Documento::NOTA_CREDITO_ID) {

            $titleReport ='Listagem de valores a Favor dos clientes - Por Nota de Credito de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
             if ($data['ReportSearch']['por_person']) {
                 $searchModel = new AvisoCreditoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAfavorNCPerson(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-nc-person',[
                     'person'=>$dataProvider,
                     'data' =>$data,
                 ]);
             }else{
                $searchModel = new AvisoCreditoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAfavorNC(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-afavor-nc',[
                     'dataProvider'=>$dataProvider,
                 ]);
             }
         }
 
 
 
 
         // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>50,
             'marginLeft'=>10,
             'marginRight'=>10,
             'filename'=>'Relatório valores Adever Emetido em: ' . date("Y-m-d H:i:s"),
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>['
               <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                   <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="'.$company['logo'].'" height="60" width="150" />
                   </p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> '.$company['name'].'</strong></p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'. $company['adress1'].'</strong></p>   
                     <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'.$titleReport.'</strong></p>
               </div>
               '], 
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
     * Lists all Receita models.
     * @return mixed
     */
     public function actionValorAdever()
     {
        
         $searchModel = new ReportSearch();
         $searchModel->documento_id = Documento::PROCESSO_ID;       
         $searchModel->dataInicio = '2001-01-01';
        //  $searchModel->documento_id = 1;       
         // $searchModel->dataInicio = date('Y-m-d', strtotime('-3 month'));
         $searchModel->dataFim = date('Y-m-d');
         return $this->render('_searchSaldoAdever', [
             'model' => $searchModel,
         ]);
     }
 
     /**
      * Lists all Receita models.
      * @return mixed
      */
     public function actionValorAdeverPdf()
     {
         $company = Yii::$app->params['company'];
         $titleReport = '';
         $data = Yii::$app->request->queryParams;
        if ($data['ReportSearch']['documento_id'] ==Documento::PROCESSO_ID) {
              if ($data['ReportSearch']['por_person']) {
                 $searchModel = new ProcessoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAdeverPrPerson(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-adever-pr-person',[
                     'person'=>$dataProvider,
                     'data' =>$data,
                 ]);
             }else{
                $titleReport ='Listagem de valores a pagos e não recebidos - Por Processo de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
                 $searchModel = new ProcessoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAdeverPr(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-adever-pr',[
                     'dataProvider'=>$dataProvider,
                 ]);
             }
         }elseif ($data['ReportSearch']['documento_id'] ==Documento::FATURA_PROVISORIA_ID) {
             $titleReport ='Listagem de valores a pagos e não recebidos - Por Factura Provisória de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
              if ($data['ReportSearch']['por_person']) {
                 $searchModel = new FaturaProvisoriaSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAdeverFpPerson(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-adever-fp-person',[
                     'person'=>$dataProvider,
                     'data' =>$data,
                 ]);
             }else{
                $searchModel = new FaturaProvisoriaSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAdeverFp(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-adever-fp',[
                     'dataProvider'=>$dataProvider,
                 ]);
             }
         }
 
         // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>50,
             'marginLeft'=>10,
             'marginRight'=>10,
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>['
               <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                   <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="'.$company['logo'].'" height="60" width="150" />
                   </p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> '.$company['name'].' </strong></p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'. $company['adress1'].'</strong></p>   
                     <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'.$titleReport.'</strong></p>
               </div>
               '], 
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
     * Lists all Receita models.
     * @return mixed
     */
     public function actionValorAdeverNew()
     {
        
         $searchModel = new ReportSearch();
         $searchModel->documento_id = Documento::PROCESSO_ID;       
         $searchModel->dataInicio = '2001-01-01';
        //  $searchModel->documento_id = 1;       
         // $searchModel->dataInicio = date('Y-m-d', strtotime('-3 month'));
         $searchModel->dataFim = date('Y-m-d');
         return $this->render('_searchSaldoAdeverNew', [
             'model' => $searchModel,
         ]);
     }
 
     /**
      * Lists all Receita models.
      * @return mixed
      */
     public function actionValorAdeverNewPdf()
     {
         $company = Yii::$app->params['company'];
         $titleReport = '';
         $data = Yii::$app->request->queryParams;
         
                $titleReport ='Listagem de valores a pagos e não recebidos - Por Processo de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
                 $searchModel = new ProcessoSearch($data['ReportSearch']);
                 $dataProvider = $searchModel->saldoAdeverNew(Yii::$app->request->queryParams);
                 $content = $this->renderPartial('saldo-adever-new-pr',[
                     'dataProvider'=>$dataProvider,
                 ]);
               
         // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>50,
             'marginLeft'=>10,
             'marginRight'=>10,
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>['
               <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                   <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="'.$company['logo'].'" height="60" width="150" />
                   </p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> '.$company['name'].' </strong></p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'. $company['adress1'].'</strong></p>   
                     <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'.$titleReport.'</strong></p>
               </div>
               '], 
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
     * Lists all Receita models.
     * @return mixed
     */
    public function actionRecebimento()
    {
       
        $searchModel = new ReportSearch();
        $searchModel->documento_id=Documento::RECEBIMENTO_ID;       
        $searchModel->bas_ano_id = substr(date('Y'),-2);       
        $searchModel->dataInicio = date('Y-m-d', strtotime('-3 month'));
        $searchModel->dataFim = date('Y-m-d');
        return $this->render('_searchRecebimento', [
            'model' => $searchModel,
        ]);
    }

    /**
     * Lists all Receita models.
     * @return mixed
     */
    public function actionRecebimentoPdf()
    {
       
        $company = Yii::$app->params['company'];
        $titleReport = '';
        $data = Yii::$app->request->queryParams;
        if ($data['ReportSearch']['documento_id'] ==Documento::RECEBIMENTO_ID) {
            $titleReport ='Listagem de Recebimentos de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
            if ($data['ReportSearch']['por_person']) {
                $person = (new \yii\db\Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_recebimento A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all();
                $content = $this->renderPartial('recebimento-all-person',[
                    'person'=>$person,
                    'data' =>$data,
                ]);
            }else{
                $searchModel = new RecebimentoSearch($data['ReportSearch']);
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                $content = $this->renderPartial('recebimento-all',[
                    'dataProvider'=>$dataProvider,
                ]);
            }
        }elseif ($data['ReportSearch']['documento_id'] ==Documento::FATURA_PROVISORIA_ID) {
            $titleReport ='Listagem de Recebimentos por Factura Provisória de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
            if ($data['ReportSearch']['por_person']) {
                $person = (new \yii\db\Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_recebimento A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all();
                $searchModel = new RecebimentoSearch($data['ReportSearch']);
                $dataProvider = $searchModel->searchFp(Yii::$app->request->queryParams);
                $content = $this->renderPartial('recebimento-fp-person',[
                    'data'=>$data,
                    'person'=>$person,
                ]);
            }else{
                   $searchModel = new RecebimentoSearch($data['ReportSearch']);
                $dataProvider = $searchModel->searchFp(Yii::$app->request->queryParams);
                $content = $this->renderPartial('recebimento-fp',[
                    'dataProvider'=>$dataProvider,
                ]); 
            }
            
        }




        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_LANDSCAPE, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'marginTop'=>50,
            'marginLeft'=>10,
            'marginRight'=>10,
            'options' => ['title' => 'Relatório Processo'],
            'methods' => [ 
                'SetHeader'=>['
              <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                  <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="'.$company['logo'].'" height="60" width="150" />
                  </p>
                  <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> '.$company['name'].' </strong></p>
                  <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'. $company['adress1'].'</strong></p>   
                    <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'.$titleReport.'</strong></p>
              </div>
              '], 
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
     * Lists all FaturaProvisoria models.
     * @return mixed
     */
     public function actionFaturaProvisoria()
     {
         $searchModel = new FaturaProvisoriaSearch();  
         
         return $this->render('_searchFaturaProvisoria', [
             'model' => $searchModel,
         ]);
     }


     /**
     * Lists all Receita models.
     * @return mixed
     */
    public function actionFaturaProvisoriaPdf()
    {
       
        $company = Yii::$app->params['company'];
        $data = Yii::$app->request->queryParams;
        $titleReport ='Listagem de Fatura Provisoória de '.$data['FaturaProvisoriaSearch']['beginDate'].' a '.$data['FaturaProvisoriaSearch']['endDate'];

        $searchModel = new FaturaProvisoriaSearch($data['FaturaProvisoriaSearch']);
        $dataProvider = $searchModel->searchReport(Yii::$app->request->queryParams);
        $content = $this->renderPartial('fatura-provisoria',[
            'dataProvider'=>$dataProvider,
        ]);
        // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>50,
             'marginLeft'=>10,
             'marginRight'=>10,
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>['
               <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                   <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="'.$company['logo'].'" height="60" width="150" />
                   </p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> '.$company['name'].' </strong></p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'. $company['adress1'].'</strong></p>   
                     <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'.$titleReport.'</strong></p>
               </div>
               '], 
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
     * Lists all Receita models.
     * @return mixed
     */
     public function actionValorAfavorAdever()
     {
        
         $searchModel = new ReportSearch();
         // $searchModel->bas_ano_id = substr(date('Y'),-2);       
         $searchModel->documento_id = 1;       
         $searchModel->dataInicio = '2001-01-01';
         $searchModel->dataFim = date('Y-m-d');
         return $this->render('_searchSaldoAfavorAdever', [
             'model' => $searchModel,
         ]);
     }



     /**
      * Lists all Receita models.
      * @return mixed
      */
     public function actionValorAfavorAdeverPdf()
     {
         $company = Yii::$app->params['company'];
         $titleReport = '';
         $data = Yii::$app->request->queryParams;
          
             $titleReport ='Listagem de valores a Favor dos clientes - Por Processo de '.$data['ReportSearch']['dataInicio'].' a '.$data['ReportSearch']['dataFim'];
            
            $searchModel = new ProcessoSearch($data['ReportSearch']);
            $dataProvider = $searchModel->relatorioAfavorAdever(Yii::$app->request->queryParams);
            $content = $this->renderPartial('saldo-afavor-adever',[
                'dataProvider'=>$dataProvider,
            ]); 
         // setup kartik\mpdf\Pdf component
         $pdf = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_LANDSCAPE, 
             'destination' => Pdf::DEST_BROWSER, 
             'content' => $content,  
             'marginTop'=>50,
             'marginLeft'=>10,
             'marginRight'=>10,
             'filename'=>'Relatório valores Adever Emetido em: ' . date("Y-m-d H:i:s"),
              'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css', 
              'options' => ['title' => 'Relatório Processo'],
             'methods' => [ 
                 'SetHeader'=>['
               <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                   <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="'.$company['logo'].'" height="60" width="150" />
                   </p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> '.$company['name'].'</strong></p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'. $company['adress1'].'</strong></p>   
                     <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>'.$titleReport.'</strong></p>
               </div>
               '], 
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



     public function actionAdiantamento(){
          $searchModel = new RecebimentoSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);       
        $dataProvider = $searchModel->adiantamento(Yii::$app->request->queryParams);

        return $this->render('adiantamento', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
     }

     

    
}
