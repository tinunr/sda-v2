<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use app\modules\fin\models\BancoConta;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\CaixaTransacaoSearch;
use app\modules\fin\models\BancoTransacao;
use app\modules\fin\models\BancoTransacaoTipo;
/**
 * EdeficiosController implements the CRUD actions for CaixaTransacao model.
 */
class CaixaTransacaoController extends Controller
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
                        'actions' => ['date-up'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index','index-pdf','keys-values'],
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
     * Lists all BancoTransacao models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CaixaTransacaoSearch();
        //$searchModel->bas_ano_id = substr(date('Y'),-2);       
        $searchModel->dataInicio = date('Y-m-d', strtotime('-1 month'));
        $searchModel->dataFim = date('Y-m-d');

        return $this->render('_search', [
            'model' => $searchModel,
        ]);
    }

     /**
      * Lists all Receita models.
      * @return mixed
      */
      public function actionIndexPdf()
      {
          $company = Yii::$app->params['company'];
          $data = Yii::$app->request->queryParams;    
 
            $titleReport ='Extrato de Conta de '.$data['CaixaTransacaoSearch']['dataInicio'].' a '.$data['CaixaTransacaoSearch']['dataFim'];
            $searchModel = new CaixaTransacaoSearch($data['CaixaTransacaoSearch']);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $content = $this->renderPartial('index-pdf',[
                'dataProvider'=>$dataProvider,
                'data' =>$data,
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


     /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionKeysValues( $fin_caixa_id, array $keys)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         $caixaTransacaoNaoValidado = CaixaTransacao::find()
                                  ->where(['fin_caixa_id'=>$fin_caixa_id])  
                                  ->andWhere(['status'=>1]) 
                                  ->asArray()
                                  ->all();
        $ids = ArrayHelper::getColumn($caixaTransacaoNaoValidado, 'id'); 
        #print_r( $ids);die();
         // true 
        return ArrayHelper::isSubset($ids,$keys);

        
    }
    

    /**
     * Finds the CaixaTransacao model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CaixaTransacao the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CaixaTransacao::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Lists all BancoTransacao models.
     * @return mixed
     */
     public function actionDateUp()
     {
         $searchModel =  CaixaTransacao::find()->all();
         foreach ($searchModel as $key => $value) {
             $value->data = $value->created_at;
             $value->save(false);
             # code...
         }
         Yii::$app->getSession()->setFlash('success', 'date uo');

     }
 
}
