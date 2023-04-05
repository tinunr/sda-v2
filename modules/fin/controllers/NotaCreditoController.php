<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\dsp\models\Item;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaItem;
use app\modules\fin\models\NotaCredito;
use app\modules\fin\models\NotaCreditoSearch;
use app\modules\fin\models\BancoTransacao;
use app\modules\fin\models\BancoTransacaoTipo;
use app\models\Documento;
use app\models\DocumentoNumero;
use kartik\mpdf\Pdf;



/**
 * EdeficiosController implements the CRUD actions for NotaCredito model.
 */
class NotaCreditoController extends Controller
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
                        'actions' => ['index','view','create','update','delete','view-pdf','undo','historico-pdf'],
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
                    // 'undo' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all NotaCredito models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotaCreditoSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);  
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NotaCredito model.
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
     * Creates a new NotaCredito model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionCreate()
     {
         $model = new NotaCredito();
         $model->data = date('Y-m-d');
         $documentoNotaCredito = DocumentoNumero::findByDocumentId(Documento::AVISO_CREDITO);
         $model->numero = $documentoNotaCredito->getNexNumber();
            // $model->bas_ano_id = substr(date("Y", strtotime($model->data)),-2);

         if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->bas_ano_id = substr(date("Y", strtotime($model->data)),-2);
              
             $transaction = \Yii::$app->db->beginTransaction();
             if ($flag = $model->save()) { 
                             $despesa = new Despesa();
                            $despesa->of_acount = 1;
                             $despesa->fin_nota_credito_id = $model->id;
                             $despesa->data = $model->data;
                             $despesa->dsp_person_id =$model->dsp_person_id;
                             $despesa->valor =0;
                             $despesa->valor_pago = 0;
                             $despesa->saldo =$model->valor;
                             $despesa->recebido = 1;
                             $despesa->bas_ano_id = $model->bas_ano_id;
                             $despesa->status = 1;
                             $despesa->descricao = 'Despesa gerada a partir do aviso de credito nº '.$model->numero.'/'.$model->bas_ano_id;
                             if (! ($flag = $despesa->save())) {
                                 $transaction->rollBack();
                             }                                      
                           $despesaItem = new DespesaItem();
                           $despesaItem->fin_despesa_id = $despesa->id;
                           $despesaItem->item_id = 1102;
                           $despesaItem->valor = $model->valor;
                           $despesaItem->cnt_plano_terceiro_id = $model->dsp_person_id;
                             if (! ($flag = $despesaItem->save())) {
                                 $transaction->rollBack();
                             }
 
                
                if ($flag) {
                    
                    if($model->numero == $documentoNotaCredito->getNexNumber()){
                        // print_r($model->numero .$documentoNotaCredito->getNexNumber());die();
                        $documentoNotaCredito->saveNexNumber();
                    }

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
     * Updates an existing NotaCredito model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionUpdate($id)
     {
         $model = $this->findModel($id);
         $deleteDespesa = Despesa::findOne(['fin_nota_credito_id'=>$model->id]);
         if ($deleteDespesa->valor_pago > 0) {
             Yii::$app->getSession()->setFlash('warning', 'A DESPESA GERADA A PARTIR DESTA AVISO DE CREDITO JÁ FOI PAGO.');
             return $this->redirect(Yii::$app->request->referrer);
         }
 
         if ($model->load(Yii::$app->request->post()) && $model->validate()) {
              $transaction = \Yii::$app->db->beginTransaction();
             $model->bas_ano_id = substr(date("Y", strtotime($model->data)),-2);
              if ($flag = $model->save()) {
                 // $deleteDespesa = Despesa::findOne(['fin_nota_credito_id'=>$model->id]);
                 $deleteDespesaItem = DespesaItem::deleteAll(['fin_despesa_id'=>$deleteDespesa->id]);
                 $deleteDespesa->delete();
                              $despesa = new Despesa();
                              $despesa->dsp_processo_id = $model->dsp_processo_id;
                              $despesa->of_acount = 1;
                              $despesa->fin_nota_credito_id = $model->id;
                              $despesa->dsp_person_id =$model->dsp_person_id;
                              $despesa->bas_ano_id = $model->bas_ano_id;
                              $despesa->data =$model->data;
                              $despesa->valor =0;
                              $despesa->valor_pago = 0;
                              $despesa->saldo =$model->valor;
                              $despesa->recebido = 1;
                              $despesa->status = 1;
                              $despesa->descricao = 'Despesa gerada a partir do aviso de credito nº '.$model->numero.'/'.$model->bas_ano_id;
                              if (! ($flag = $despesa->save())) {
                                  $transaction->rollBack();
                              }                                     
                            $despesaItem = new DespesaItem();
                            $despesaItem->fin_despesa_id = $despesa->id;
                            $despesaItem->item_id = 1102;
                            $despesaItem->valor = $model->valor;
                            $despesaItem->cnt_plano_terceiro_id = $model->dsp_person_id;
                              if (! ($flag = $despesaItem->save())) {
                                  $transaction->rollBack();
                              }
  
                 
                 if ($flag) {
                      $transaction->commit();
                      return $this->redirect(['view', 'id' => $model->id]);
                  }else {
                      $transaction->rollBack();
                  }
              }else {
                  $transaction->rollBack();
              }
  
          }
 
         return $this->render('update', [
             'model' => $model,
         ]);
     }

    /**
     * Deletes an existing Pagamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionUndo($id)
     {
         $model = $this->findModel($id);
 
         $modelPagamento = Despesa::findOne(['fin_nota_credito_id'=>$model->id]);
         if (!empty($modelPagamento)) {
             if ($modelPagamento->valor_pago ==0) {
                 $modelPagamento->status = 0;
                 $modelPagamento->save(false);
 
                 $model->status = 0;
                 if($model->save(false)){
                     Yii::$app->getSession()->setFlash('success', 'AVISO DE CREDITO ANULADO COM SUCESSO.');
 
                 }else{
                 Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
 
                 }
 
 
             }else{
                 Yii::$app->getSession()->setFlash('warning', 'DESPESA ASSOCIADOA ESTE AVISO DE CREDITO JÁ FOI PAGO.');
 
             }
             # code...
         }
 
         return $this->redirect(['view','id'=>$id]);
     }

    /**
     * Finds the NotaCredito model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NotaCredito the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NotaCredito::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
     * Deletes an existing Pagamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionUndo2()
     {
         $modelS = NotaCredito::find()->where(['dsp_processo_id'=>null,'bas_ano_id'=>17])->all();
         if (!empty($modelS)) {
             foreach ($modelS as $key => $model) {
                 $count = \app\modules\dsp\models\Processo::find()->where(['bas_ano_id'=>$model->bas_ano_id,'dsp_person_id'=>$model->dsp_person_id])->count();
                 if($count==1){
                 $processo = \app\modules\dsp\models\Processo::find()->where(['bas_ano_id'=>$model->bas_ano_id,'dsp_person_id'=>$model->dsp_person_id])->one();
                 $model->dsp_processo_id = $processo->id;
                     $model->save(false);
                     $despesa = \app\modules\fin\models\Despesa::findOne(['fin_nota_credito_id'=>$model->id]);
                     $despesa->dsp_processo_id = $processo->id;
                     $despesa->save(false);
                 }
                 # code...
             }
             # code...
         }
 
         
     }
}
