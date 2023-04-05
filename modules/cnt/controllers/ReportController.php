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

use app\modules\cnt\models\Razao;
use app\modules\cnt\models\RazaoSearch;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\RazaoItemSearch;
use app\modules\cnt\models\Modelo106Search;
use app\models\Ano;

/**
 * Default controller for the `employee` module
 */
class ReportController extends Controller
{  

  public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['modelo106','modelo106-anexo-cliente','modelo106-anexo-fornecedor','extrato-pdf'],
                        'allow' => true,
                        'roles' => ['@'],
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
    public function actionModelo106AnexoCliente()
    {    
        $company = Yii::$app->params['company'];
        $searchModel = new RazaoItemSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);
        $searchModel->bas_mes_id = date('m');
        $searchModel->bas_formato_id = 1;
        $data = Yii::$app->request->queryParams;
        if (!empty($data['RazaoItemSearch'])) {
            $titleReport = 'IMPOSTO SOBRE O VALOR ACRESCENTADO';
            $searchModel = new RazaoItemSearch($data['RazaoItemSearch']);
            $dataProvider = $searchModel->modelo106Cliente(Yii::$app->request->queryParams);
            if ($data['RazaoItemSearch']['bas_formato_id']==2) {
                //set content type xml in response
               Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
               $headers = Yii::$app->response->headers;
               $headers->add('Content-Type', 'text/xml');
             
              //set the view and render it partial to skip the layout to be rendered as well
               return $this->renderPartial('modelo106-anexo-cliente-xml', [
                  'dataProvider'=>$dataProvider,
                         'titleReport'=>$titleReport,
                         'bas_ano_id'=>$data['RazaoItemSearch']['bas_ano_id'],
                         'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
                ]);
           }
            elseif ($data['RazaoItemSearch']['bas_formato_id']==1) {

                     $content = $this->renderPartial('modelo106-anexo-cliente-pdf',[
                         'dataProvider'=>$dataProvider,
                         'titleReport'=>$titleReport,
                         'bas_ano_id'=>$data['RazaoItemSearch']['bas_ano_id'],
                         'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
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
        return $this->render('_searchModelo106AnexoCliente', [
            'model' => $searchModel,
        ]);
    }


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionModelo106AnexoFornecedor()
    {    
        $company = Yii::$app->params['company'];
        $searchModel = new RazaoItemSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);
        $searchModel->bas_mes_id = date('m');
        $searchModel->bas_formato_id = 1;
        $data = Yii::$app->request->queryParams;
        if (!empty($data['RazaoItemSearch'])) {
            $titleReport = 'IMPOSTO SOBRE O VALOR ACRESCENTADO';
            $searchModel = new RazaoItemSearch($data['RazaoItemSearch']);
            $dataProvider = $searchModel->modelo106Fornecedor(Yii::$app->request->queryParams);
             if ($data['RazaoItemSearch']['bas_formato_id']==2) {
                //set content type xml in response
               Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
               $headers = Yii::$app->response->headers;
               $headers->add('Content-Type', 'text/xml');
               //below array $url converted into required(sitemap.xml) structure        
                $xmldata = '<?xml version="1.0" encoding="utf-8"?>'; 
                $xmldata .= '<anexo_for xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://nosiapps.gov.cv/grexsd/2014/mod106 anexo_for.xsd">';
                $xmldata .= '<linhas>';
                foreach ($dataProvider->getModels() as $url => $model)
                {
                    $origem = Yii::$app->CntQuery->origemData($model['cnt_razao_id']);
                $xmldata .='<linha origem="'.$origem->origem.'" nif="'.$origem->nif.'" designacao="'.$origem->designacao.'" tp_doc="'.$origem->tp_doc.'" num_doc="'.$origem->num_doc.'" data="'.$origem->data.'" vl_fatura="'.$model['vl_fatura'].'" vl_base_incid="'.$model['vl_base_incid'].'" tx_iva="'.$model['tx_iva'].'" iva_sup="'.$model['iva_liq'].'" direito_ded="'.$model['nao_liq_imp'].'" iva_ded="'.$model['linha_dest_mod'].'" tipologia="SRV" linha_dest_mod="'.$model['linha_dest_mod'].'" />';
                }
                $xmldata .= '<linhas>';
                $xmldata .= '<dt_entrega>2019-01-07</dt_entrega>';
                $xmldata .= '<total_fatura>274107</total_fatura>';
                $xmldata .= '<total_base_incid>237892</total_base_incid>';
                $xmldata .= '<total_suportado>35682</total_suportado>';
                $xmldata .= '<total_dedutivel>35682</total_dedutivel>';
                $xmldata .= '</anexo_for>';
                
                if(file_put_contents('sitemap.xml',$xmldata))
                {
                    echo "sitemap.xml file created on project root folder..";   
                }
             
              //set the view and render it partial to skip the layout to be rendered as well
               return  [
                  'dataProvider'=>$dataProvider->getModels(),
                         'titleReport'=>$titleReport,
                         'bas_ano_id'=>Ano::findOne($data['RazaoItemSearch']['bas_ano_id'])->ano,
                         'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
                ];
           }
            elseif ($data['RazaoItemSearch']['bas_formato_id']==1) {

                     $content = $this->renderPartial('modelo106-anexo-fornecedor-pdf',[
                         'dataProvider'=>$dataProvider,
                         'titleReport'=>$titleReport,
                         'bas_ano_id'=>$data['RazaoItemSearch']['bas_ano_id'],
                         'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
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
        return $this->render('_searchModelo106AnexoFornecedor', [
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
         $titleReport = $formatter->asDate($data['RazaoItemSearch']['dataInicio'],'MM-Y').' - '. $formatter->asDate($data['RazaoItemSearch']['dataFim'],'MM-Y');

         $searchModel = new RazaoItemSearch( $data['RazaoItemSearch']);
         $dataProvider = $searchModel->extrato(Yii::$app->request->queryParams);
         $content = $this->renderPartial('extrato-pdf',[
             'dataProvider'=>$dataProvider,
             'titleReport'=>$titleReport,
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
    public function actionBalancete()
    {    
         $searchModel = new RazaoItemSearch();
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
        $formatter = Yii::$app->formatter;
         $company = Yii::$app->params['company'];
         $data = Yii::$app->request->queryParams;
         $titleReport = $formatter->asDate($data['RazaoItemSearch']['dataInicio'],'MM-Y').' - '. $formatter->asDate($data['RazaoItemSearch']['dataFim'],'MM-Y');

         $searchModel = new RazaoItemSearch( $data['RazaoItemSearch']);
         $dataProvider = $searchModel->balancete(Yii::$app->request->queryParams);
         $content = $this->renderPartial('balancete-pdf',[
             'dataProvider'=>$dataProvider,
             'titleReport'=>$titleReport,
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
