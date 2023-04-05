<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;

use app\modules\fin\models\AvisoCredito;
use app\modules\fin\models\AvisoCreditoSearch;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaItem;
use app\modules\fin\services\DespesaService;
use app\models\Documento;
use app\models\DocumentoNumero;
/**
 * EdeficiosController implements the CRUD actions for AvisoCredito model.
 */
class AvisoCreditoController extends Controller
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
                        'actions' => ['index','view','view-pdf','create','update','undo','delete','historico-pdf'],
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
                    'undo' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AvisoCredito models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AvisoCreditoSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);  
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AvisoCredito model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AvisoCredito model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionCreate()
     {
         $model = new AvisoCredito();
         $model->data = date('Y-m-d');
         // $model->bas_ano_id = substr(date('Y'),-2);
 
         if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->bas_ano_id = substr(date("Y", strtotime($model->data)),-2);
            
              $documentoAvisoCredito = DocumentoNumero::findByDocumentId(Documento::NOTA_CREDITO_ID);
              $model->numero = $documentoAvisoCredito->getNexNumber();
             $transaction = \Yii::$app->db->beginTransaction();
             if ($flag = $model->save()) {

                 // $despesaNumero = DocumentoNumero::findByDocumentId(Documento::DESPESA);
 
                             $despesa = new Despesa();
                             $despesa->of_acount = 1;
                             $despesa->fin_aviso_credito_id = $model->id;
                             $despesa->data = $model->data;
                             $despesa->dsp_person_id =$model->dsp_person_id;
                             $despesa->dsp_processo_id = $model->dsp_processo_id;
                             $despesa->data =$model->data;
                             $despesa->bas_ano_id =$model->bas_ano_id;
                             $despesa->valor =0;
                             $despesa->valor_pago = 0;
                             $despesa->saldo =$model->valor;
                             $despesa->recebido = 1;
                             $despesa->status = 1;
                             $despesa->descricao = 'Despesa gerada a partir do nota de credito nº '.$model->numero.'/'.$model->bas_ano_id;
                             if (! ($flag = $despesa->save(false))) {
                // print_r($despesa->errors);die();

                                 $transaction->rollBack();
                             } 
                           // $despesaNumero->saveNexNumber();                                     
                           $despesaItem = new DespesaItem();
                           $despesaItem->fin_despesa_id = $despesa->id;
                           $despesaItem->item_id = 1102;
                           $despesaItem->valor = $model->valor;
                           $despesaItem->cnt_plano_terceiro_id = $model->dsp_person_id;
                             if (! ($flag = $despesaItem->save())) {
                                 $transaction->rollBack();
                             }
 
                
                if ($flag) {
                 $documentoAvisoCredito->saveNexNumber();
                 $transaction->commit();
                 return $this->redirect(['view', 'id' => $model->id]);
             }else {
                 $transaction->rollBack();
             }
             }else {
                 $transaction->rollBack();
             }
 
         }
 
         return $this->render('create', [
             'model' => $model,
         ]);
     }
 

    /**
     * Updates an existing AvisoCredito model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AvisoCredito model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUndo($id)
    {
        //TODO anular despeza vereficar se teme pagamento
        $model = $this->findModel($id);
        $despesa = Despesa::find()->where(['fin_aviso_credito_id'=>$model->id])->one();
            if (DespesaService::temPagamento($despesa->id)) {
            Yii::$app->getSession()->setFlash('warning', 'Esta despesa já se encontra pago.');
            return $this->redirect(['view', 'id' => $id]);
        }
        $documento_id = null;
        if ($despesa->cnt_documento_id = Documento::DESPESA_FATURA_FORNECEDOR) {
            $documento_id = Documento::DESPESA_FATURA_FORNECEDOR;
        } elseif ($despesa->cnt_documento_id = Documento::FATURA_FORNECEDOR_INVESTIMENTO) {
            $documento_id = Documento::FATURA_FORNECEDOR_INVESTIMENTO;
        } elseif ($despesa->cnt_documento_id = Documento::FATURA_RECIBO) {
            $documento_id = Documento::FATURA_RECIBO;
        } 
        if (!empty($documento_id)) {
            if (Yii::$app->CntQuery->inContabilidadeAtive($documento_id, $id)) {
                Yii::$app->getSession()->setFlash('warning', 'Esta despesa já se encontra na Contabilidade anule na Contabilidade para poder proceguir.');
                return $this->redirect(['view', 'id' => $id]);
            }
        }  
        if ($despesa->is_lock) {
            Yii::$app->getSession()->setFlash('error', 'ESTA DESPESA ESTA BLOQUEADO  NÃO PODE SER ANULADO.');
        } 
        if($despesa->valor_pago > 0){
            Yii::$app->getSession()->setFlash('error', 'despesa nao pode ser anulao porque ja foi pago.');
                 return $this->redirect(['view','id'=>$id]);

        }
          $despesa->status = 0;
          $despesa->save(false);
        $model->status =0;
        $model->save();

        return $this->redirect(['view','id'=>$id]);
    }

    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
   public function actionViewPdf($id)
   { 
        $company = Yii::$app->params['company'];
        $content = $this->renderPartial('view-pdf',[
            'company' => $company,
            'model' => $this->findModel($id),
            ]);


        // setup kartik\mpdf\Pdf component
          $pdf = new Pdf([
              'mode' => Pdf::MODE_UTF8, 
              'format' => Pdf::FORMAT_A4, 
              'orientation' => Pdf::ORIENT_PORTRAIT, 
              'destination' => Pdf::DEST_BROWSER, 
              'content' => $content,  
              'cssFile' => '@app/web/css/pdf.css', 
              'marginTop'=>32,
              'marginLeft'=>10,
              'marginRight'=>10,
              'options' => ['title' => 'Relatório Processo'],
              'methods' => [ 
                  'SetHeader'=>['
                   <div id="logo"><img src="logo.png"></div>
                    <div id="company">
                      <h2 class="name">'.$company['name'].'</h2>
                      <div class="address">'.$company['adress2'].'</div>
                      <div class="address">NIF: '.$company['nif'].'</div>
                    </div>
                    </div>
                '], 
                  'SetFooter'=>['<p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong>'.$company['adress2'].'</strong>
                       <br><strong>C.P. Nº '.$company['cp'].' - Tel.: '.$company['teletone'].' - FAX: '.$company['fax'].' - Praia, Santiago</strong>
                       </p> ||Página {PAGENO}/{nbpg}'],
              ]
          ]);
           $pdf->options = [
                  'defaultheaderline' => 0.2,
                  'defaultfooterline' => 0.2,
              ];
        

        // return the pdf output as per the destination setting
        return $pdf->render();
    }




     /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
   public function actionHistoricoPdf($id)
   { 
        $company = Yii::$app->params['company'];
        $content = $this->renderPartial('historico-pdf',[
            'company' => $company,
            'model' => $this->findModel($id),
            ]);


        // setup kartik\mpdf\Pdf component
          $pdf = new Pdf([
              'mode' => Pdf::MODE_UTF8, 
              'format' => Pdf::FORMAT_A4, 
              'orientation' => Pdf::ORIENT_PORTRAIT, 
              'destination' => Pdf::DEST_BROWSER, 
              'content' => $content,  
              'cssFile' => '@app/web/css/pdf.css', 
              'marginTop'=>32,
              'marginLeft'=>10,
              'marginRight'=>10,
              'options' => ['title' => 'Relatório Processo'],
              'methods' => [ 
                  'SetHeader'=>['
                   <div id="logo"><img src="logo.png"></div>
                    <div id="company">
                      <h2 class="name">'.$company['name'].'</h2>
                      <div class="address">'.$company['adress2'].'</div>
                      <div class="address">NIF: '.$company['nif'].'</div>
                    </div>
                    </div>
                '], 
                  'SetFooter'=>['<p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong>'.$company['adress2'].'</strong>
                       <br><strong>C.P. Nº '.$company['cp'].' - Tel.: '.$company['teletone'].' - FAX: '.$company['fax'].' - Praia, Santiago</strong>
                       </p> ||Página {PAGENO}/{nbpg}'],
              ]
          ]);
           $pdf->options = [
                  'defaultheaderline' => 0.2,
                  'defaultfooterline' => 0.2,
              ];
        

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Finds the AvisoCredito model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AvisoCredito the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AvisoCredito::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
