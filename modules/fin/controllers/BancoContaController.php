<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\db\Query;
use app\modules\fin\models\BancoConta;
use app\modules\fin\models\BancoContaSearch;
use kartik\mpdf\Pdf;

/**
 * EdeficiosController implements the CRUD actions for BancoConta model.
 */
class BancoContaController extends Controller
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
                        'actions' => ['conta-list'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','view','create','update','delete','posicao-tesouraria','posicao-tesouraria-pdf'],
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
     * Lists all BancoConta models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BancoContaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all BancoConta models.
     * @return mixed
     */
     public function actionPosicaoTesouraria()
     {
         $searchModel = new BancoContaSearch();
         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
 
         return $this->render('posicao-tesouraria', [
             'searchModel' => $searchModel,
             'dataProvider' => $dataProvider,
         ]);
     }

    /**
     * Displays a single BancoConta model.
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
     * Creates a new BancoConta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BancoConta();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BancoConta model.
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
     * Deletes an existing BancoConta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BancoConta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BancoConta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BancoConta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    
    // THE CONTROLLER
    public function actionContaList()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id = $parents[0];

                $query = new Query;
                $query->select('id as id, numero as name')
                    ->from('fin_banco_conta')
                    ->where(['fin_banco_id'=> $id])
                    ->andWhere(['status'=> 1])
                    ->all();
                $command = $query->createCommand();
                $out = $command->queryAll();
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }



    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionValidarPerson(array $keys)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $isValid = 1;
        $personId = 0;
        $valor = 0;
        foreach ($keys as $key => $value) {
          $receita = Receita::find()
                    ->where(['id'=>$value])  
                    ->one();
          if (!empty($receita) ){
            $valor = $valor + $receita->saldo;
            if ($personId==0) {
              $personId = $receita->dsp_person_id;
            }elseif($personId != $receita->dsp_person_id){
              $isValid = 0;
            }
          }
        }
        return [
          'isValid'=>$isValid,
          'valor'=>$valor,
        ];        
    }

    public function actionPosicaoTesourariaPdf() {
        
       
         $searchModel = new BancoContaSearch();
         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $company = Yii::$app->params['company'];
       
        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('posicao-tesouraria-pdf',[
            'dataProvider' => $dataProvider,
            'company' => $company,
            ]);

        
          // setup kartik\mpdf\Pdf component
          $pdf  = new Pdf([
             'mode' => Pdf::MODE_UTF8, 
             'format' => Pdf::FORMAT_A4, 
             'orientation' => Pdf::ORIENT_PORTRAIT, 
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
                     <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>POSISÃO DE TESOURARIA</strong></p>
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
        
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

}
 
