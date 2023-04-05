<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\fin\models\NotaDebito;
use app\modules\fin\models\NotaDebitoSearch;
use app\models\Documento;
use app\models\DocumentoNumero;
use kartik\mpdf\Pdf;



/**
 * EdeficiosController implements the CRUD actions for NotaDebito model.
 */
class NotaDebitoController extends Controller
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
                    'undo' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all NotaDebito models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotaDebitoSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);  
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NotaDebito model.
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
     * Creates a new NotaDebito model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionCreate()
     {
         $model = new NotaDebito();
         $model->data = date('Y-m-d');
         $documentoNotaDebito = DocumentoNumero::findByDocumentId(Documento::NOTA_DEBITO);
         $model->numero = $documentoNotaDebito->getNexNumber();

         if ($model->load(Yii::$app->request->post())) {
            $model->bas_ano_id = substr(date("Y", strtotime($model->data)),-2);
            $model->valor_pago = 0;
            $model->saldo = $model->valor;
              
             $transaction = \Yii::$app->db->beginTransaction();
             if ($flag = $model->save()) {  
                 $documentoNotaDebito->saveNexNumber();
                 $transaction->commit();
                 return $this->redirect(['view', 'id' => $model->id]);
             }else {
                 $transaction->rollBack();
             }
             }
 
         return $this->render('create', [
             'model' => $model,
         ]);
     }
 

    /**
     * Updates an existing NotaDebito model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionUpdate($id)
     {
         $model = $this->findModel($id); 
         if ($model->valor_pago >0 || $model->status = 0) {
            Yii::$app->getSession()->setFlash('warning', 'Nota de debito pago ou anulado não pode ser alterado');
            return $this->redirect(Yii::$app->request->referrer);
    
         }
         if ($model->load(Yii::$app->request->post()) && $model->save()) {
                      return $this->redirect(['view', 'id' => $model->id]);
              
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
         if ($model->valor_pago >0) {
            Yii::$app->getSession()->setFlash('warning', 'Nota de debito recebido não pode ser anulado.');
         }else {
             $model->status = 0;
             if($model->save()){
                Yii::$app->getSession()->setFlash('succes', 'Nota de debito anulado com sucesso.');
             }else {
                Yii::$app->getSession()->setFlash('danger', 'Ocoreu um erro ao efetuar a operação');
             }
         }
        return $this->redirect(Yii::$app->request->referrer);
     }

    /**
     * Finds the NotaDebito model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NotaDebito the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NotaDebito::findOne($id)) !== null) {
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



   
}
