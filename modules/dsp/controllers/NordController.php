<?php

namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\httpclient\XmlParser;
use yii\web\UploadedFile;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\db\Query;
use app\modules\dsp\models\Nord;
use app\modules\dsp\models\Origem;
use app\modules\dsp\models\NordSearch;
use app\modules\dsp\models\Regime;
use app\modules\dsp\models\Item;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\Person;
use app\models\Documento;
use app\models\DocumentoNumero;
use arogachev\excel\import\basic\Importer;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\dsp\models\Desembaraco;
use app\models\Ano;
use app\modules\fin\models\FaturaProvisoriaItem;
use app\modules\fin\models\DespesaItem;
/**
 * NordController implements the CRUD actions for Nord model.
 */
class NordController extends Controller
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
                        'actions'=>['get-data','get-item','valor-aduaneiro','import-excel','get-item-update','desembarco-pr'],
                        'allow' => true, 
                        'roles' => ['@'],

                    ],
                    [
                        'actions' => ['ajax','index','view','create','create-2','update','undo','ativar','upload-xml','delete-xml','baixar-xml'],
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
                    'delete-xml' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Lists all Person models.
     * @return mixed
     */
     public function actionAjax($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, id AS text')
                ->from('dsp_nord')
                ->orderBy('id')
                ->filterWhere(['like', 'id', $q.'%', false])
                ->all();
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Person::findOne($id)->nome];
        }
        
        return $out;
    }

    

    /**
     * Lists all Nord models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new NordSearch();
        $searchModel->bas_ano_id = substr(date('Y'),-2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all despacho models.
     * @return mixed
     */
     public function actionImportExcel()
     {
        $importer = new Importer([
            'filePath' => Yii::getAlias('@import/Book1.xlsx'),
            'standardModelsConfig' => [
                [
                    'className' => Nord::className(),
                    'standardAttributesConfig' => [
                        [
                            'name' => 'dsp_processo_id',
                            'valueReplacement' => function ($value) {
                                return Processo::find()->select('id')->where(['numero' => $value]);
                            },
                        ],

                        [
                            'name' => 'dsp_desembaraco_id',
                            'valueReplacement' => function ($value) {
                                return Desembaraco::find()->select('id')->where(['code' => $value]);
                            },
                        ],
                        [
                            'name' => 'bas_ano_id',
                            'valueReplacement' => function ($value) {
                                return Ano::find()->select('id')->where(['ano' => $value]);
                            },
                        ],
                    ],
                ],
            ],
        ]);

        if (!$importer->run()) {
            Yii::$app->getSession()->setFlash('danger', $importer->error);
            if ($importer->wrongModel) {
                 Yii::$app->getSession()->setFlash('danger', Html::errorSummary($importer->wrongModel));
            }
        }else{
            Yii::$app->getSession()->setFlash('success', 'Base de dados atualizado com sucesso');
        }

    return $this->redirect(Yii::$app->request->referrer);
        
        
     }

    /**
     * Displays a single Nord model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
         $model = $this->findModel($id);

          $desembaraco = ($model->dsp_desembaraco_id ==2)? 'A':''; 
          $data = null;

          if (file_exists(Yii::getAlias('@nords/').$id.'.xml')) {
            //$XML = new XmlParser();
            $data = simplexml_load_file(Yii::getAlias('@nords/').$id.'.xml');
            
          }


        return $this->render('view', [
            'model' => $model,
            'data'=>$data,
        ]);
    }


    
     /**
     * Creates a new Nord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUploadXml($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $model->xmlFile = UploadedFile::getInstance($model, 'xmlFile');
            if ($model->upload()) {
                Yii::$app->getSession()->setFlash('success', 'Ficheiro anexado com sucesso');
            }else{
            Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
            }
        } 
        return $this->redirect(Yii::$app->request->referrer);
    }

     /**
     * Creates a new Nord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionDeleteXml($id)
    {
        $model = $this->findModel($id);

        if (file_exists(Yii::getAlias('@nords/').$id.'.xml')){
            unlink(Yii::getAlias('@nords/').$id.'.xml');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Creates a new Nord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionBaixarXml($id)
    {
        if (file_exists(Yii::getAlias('@nords/').$id.'.xml')){
            $this->redirect(Url::to('@web/nords/').$id.'.xml')->send();
        }
        Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação');

        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Creates a new Nord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Nord();
        $model->bas_ano_id = substr(date('Y'),-2);
        $model->dsp_desembaraco_id = 1;

        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::NORD_ID);
        $model->numero = $documentoNumero->getNexNumber();

         if ($model->load(Yii::$app->request->post())) {
            $model->id = $model->numero.$model->bas_ano_id; 
            if ($model->validate()&&$model->save()) {
                if ($model->numero==$documentoNumero->getNexNumber()&& $model->bas_ano_id == substr(date('Y'),-2)) {
                $documentoNumero->saveNexNumber();
             }
            return $this->redirect(['view', 'id' => $model->id]);
            // return $this->redirect('index');
                
            }else{
               return $this->render('create', [
                'model' => $model,
            ]); 
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Nord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate2()
    {
        $model = new Nord();
        $model->bas_ano_id = substr(date('Y'),-2);
        $model->dsp_desembaraco_id = 2;
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::NORD_ID_A);
        $model->numero = $documentoNumero->getNexNumber();

         if ($model->load(Yii::$app->request->post())) {
            $model->id = $model->numero.$model->bas_ano_id.'A'; 
            if ($model->validate()&&$model->save()) {
                if ($model->numero==$documentoNumero->getNexNumber()&& $model->bas_ano_id == substr(date('Y'),-2)) {
                $documentoNumero->saveNexNumber();
             }
                return $this->redirect(['view', 'id' => $model->id]);                
            }else{
               return $this->render('create', [
                'model' => $model,
            ]); 
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Nord model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $readonly = !Yii::$app->user->can('dsp/nord/update-cvt');
        if ($model->load(Yii::$app->request->post())) {
            if ($model->dsp_desembaraco_id == 2) {
                $model->id = $model->numero.$model->bas_ano_id.'A';  
            }else{
                $model->id = $model->numero.$model->bas_ano_id; 
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'readonly' => $readonly,
        ]);
    }


    /**
     * Deletes an existing Nord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUndo($id)
    {
        $model = $this->findModel($id);
        $model->dsp_processo_id = null;
        $model->status = Nord::STATUS_ANULADO;
        if($model->save(false)){
            Yii::$app->getSession()->setFlash('success', 'NORD anulado com sucesso');
        }else{
            Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação');
        }

        return $this->redirect(['view','id'=>$id]);
    }

    /**
     * Deletes an existing Nord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAtivar($id)
    {
        $model = $this->findModel($id);
        $model->status = Nord::STATUS_ATIVO;
        if($model->save(false)){
            Yii::$app->getSession()->setFlash('success', 'NORD ATIVADO COM SUCESSO.');
        }else{
            Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação');
        }

        return $this->redirect(['view','id'=>$id]);
    }

    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Nord::findOne(['id'=>$id])) !== null) {
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
     public function actionGetData($id)
     {
         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         $dsp_regime_id = '';
         $dsp_regime_descricao = '';
         $nord_id = '';
         $impresso_principal = 0;
         $impresso_intercalar = '';
         $mercadoria =  '';
         $titulo = '';
         $descRegime ='';

 
         $processo = Processo::findOne(['id'=>$id]);
         $mercadoria = $processo->descricao;
         $person = Person::findOne(['id'=>$processo->nome_fatura]);
         $model = Nord::findOne(['dsp_processo_id'=>$processo->id]);
         
         if (($model !== null)&&file_exists(Yii::getAlias('@nords/').$model->id.'.xml')){
             $nord_id = $model->id;
             $data = simplexml_load_file(Yii::getAlias('@nords/').$model->id.'.xml');
             $impresso_principal=(int)$data->Property->Forms->Number_of_the_form;
             $impresso_intercalar=$data->Property->Forms->Total_number_of_forms-$data->Property->Forms->Number_of_the_form;
             $nord = $data->Declarant->Reference->Number;

             foreach ($data->Item as  $item) {
                $regime_id = (int)$item->Tarification->Extended_customs_procedure;
                $titulo =  (string)$item->Previous_doc->Summary_declaration;
             }

             if(!empty($regime_id)&&is_int($regime_id)){
                $regime = Regime::findOne(['id'=>$regime_id]);
// print_r($regime_id);die();
                $dsp_regime_id = $regime->id;
                $dsp_regime_descricao = $regime->descricao;

                
                if ($regime->id == 4070) {
                    $descRegime = 'Saide de Entreposto';
                }elseif ($regime->id == 7000) {
                    $descRegime = 'Entrada de Entreposto';
                }
             }
 
             $manifesto = $data->Identification->Manifest_reference_number;
 
 
             $contraMarca = (strlen($manifesto)>16)?substr($manifesto,0,4):substr($manifesto,0,3);
             $origemCode = substr($manifesto,-11,5);
             $ano = substr($manifesto,-13,2);
              if (($dataOrg = Origem::findOne(['id'=>$origemCode])) !== null) {
                 $org = $dataOrg->descricao;
             }else{
                 $org = $origemCode.' Por defenir na tabela origem';
             }
             
             $origem = $org;
             $meioTransporte = ($model->dsp_desembaraco_id==1)?'Barco':'Aviões';
             $tipoTransport = ($model->dsp_desembaraco_id==1)?'n/m':'';
             $blcp = ($model->dsp_desembaraco_id==1)?'B.L':'C.P';
             $estanciaAduaneria =$data->Identification->Office_segment->Customs_Clearance_office_name;
             $nomeTransport = $data->Transport->Means_of_transport->Departure_arrival_information->Identity;
 
             $mercadoria = $mercadoria.'; '.$tipoTransport.' '.$nomeTransport.'; C/M '.$contraMarca.'/'.$ano.'; '.$blcp.' '.$titulo.' '.$origem.'; '.$descRegime;
         }
 
 
 
            return [
                'dsp_regime_id'=>$dsp_regime_id,
                'dsp_regime_descricao'=>$dsp_regime_descricao,
                'dsp_person_id'=>$person->id,
                'dsp_person_nome'=>$person->nome,
                'nord'=>$nord_id,
                'impresso_principal'=>$impresso_principal,
                'impresso_intercalar'=>$impresso_intercalar,
                'mercadoria'=> $mercadoria,            
         ];
     }


    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetItem($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $processo = Processo::findOne(['id'=>$id]);
        $model = Nord::findOne(['dsp_processo_id'=>$processo->id]);
        $data = []; 

        //return $id;
        if (($model !== null)&&file_exists(Yii::getAlias('@nords/').$model->id.'.xml')) {

            // print_r($model->id);die();

            $xmlData = simplexml_load_file(Yii::getAlias('@nords/').$model->id.'.xml'); 
                foreach ($xmlData->Item as $item) {  
                    if (!empty($item->Taxation->Taxation_line)) {
                         $i = 0;
                         foreach ($item->Taxation->Taxation_line as  $Taxation_line) {
                                    $i= (int)implode([$Taxation_line->Duty_tax_code]);
                                    if($Taxation_line->Duty_tax_code >0){
                                        if (array_key_exists($i,$data)) {
                                            $data[$i]['tax_amount']=   $data[$i]['tax_amount'] +($Taxation_line->Duty_tax_amount*$Taxation_line->Duty_tax_MP);
                                        }else{
                                            $data[$i]= [
                                                'tax_code'=>(int)implode([$Taxation_line->Duty_tax_code]),
                                                'tax_descricao'=>Item::findOne(['id'=>(int)implode([$Taxation_line->Duty_tax_code])])->descricao,
                                                'tax_amount'=>  ($Taxation_line->Duty_tax_amount*$Taxation_line->Duty_tax_MP),
                                                'item_origem_id'=>'X',

                                            ] ;
                                    }
                            }
                        }
                    }
                }  
                foreach ($xmlData->Global_taxes as $glItem) { 
                        foreach ($glItem->Global_tax_item_line as  $Global_tax) {
                                $i= (int)implode([$Global_tax->Global_tax_code]);
                                if($Global_tax->Global_tax_code >0){
                                    if (array_key_exists($i,$data)) {
                                        $data[$i]['tax_amount']=   $data[$i]['tax_amount'] +($Global_tax->Global_tax_Base*$Global_tax->Golbal_tax_MP);
                                    }else{
                                         $Global_tax_code = (int)implode([$Global_tax->Global_tax_code]);
                                          $item = Item::find()->where(['id'=> $Global_tax_code])->one();
                                           //return['item'=> $item->descricao,'Global_tax_code'=>$Global_tax_code ];
                                        $data[$i]= [
                                            'tax_code'=>$Global_tax_code,
                                            'tax_descricao'=>$item->descricao,
                                            'tax_amount'=>  ($Global_tax->Global_tax_Base*$Global_tax->Golbal_tax_MP),
                                            'item_origem_id'=>'X',

                                        ] ;
                                }
                        }
                        $i++;
                    }
                }

            }   


                if(($fpItemOld = FaturaProvisoriaItem::find()
                        ->join( 'INNER JOIN', 'fin_fatura_provisoria','fin_fatura_provisoria.id = fin_fatura_provisoria_item.dsp_fatura_provisoria_id')
                        ->where(['fin_fatura_provisoria.dsp_processo_id'=>$processo->id])
                        ->andWhere(['fin_fatura_provisoria.status'=>1])
                       ->all())!=null){

                    foreach ($fpItemOld as  $fpItem) {
                            $i= (int)$fpItem->dsp_item_id;
                            if($fpItem->dsp_item_id >0){
                                if (array_key_exists($i,$data)) {
                                    $data[$i]['tax_amount']=   $data[$i]['tax_amount'] - $fpItem->valor;
                                }else{
                                    $data[$i]= [
                                        'tax_code'=>(int)$fpItem->dsp_item_id,
                                        'tax_descricao'=>Item::findOne(['id'=>(int)$fpItem->dsp_item_id])->descricao,
                                        'tax_amount'=>  -$fpItem->valor,
                                        'item_origem_id'=>'X',
                                    ] ;
                            }
                    }
                    $i++;
                }}


                if(($despesasItem = DespesaItem::find()
                        ->join( 'INNER JOIN', 'fin_despesa','fin_despesa.id = fin_despesa_item.fin_despesa_id')
                        ->where(['fin_despesa.dsp_processo_id'=>$processo->id])
                        ->andWhere(['fin_despesa.recebido'=>1])
                        ->andWhere(['fin_despesa.status'=>1])
                        // ->andWhere(['>','fin_despesa.fin_recebimento_id',0])
                       ->all())!=null){

                    foreach ($despesasItem as  $dspItem) {
                            $i= $dspItem->item_id;
                            if($dspItem->item_id >0){
                                if (array_key_exists($i,$data)) {
                                    //$data[$i]['tax_amount']=   $data[$i]['tax_amount'] + $dspItem->valor;
                                }else{
                                    $data[$i]= [
                                        'tax_code'=>(int)$dspItem->item_id,
                                        'tax_descricao'=>Item::findOne(['id'=>(int)$dspItem->item_id])->descricao,
                                        'tax_amount'=>  $dspItem->valor,
                                        'item_origem_id'=>'D',
                                    ] ;
                            }
                    }
                    $i++;
                }}


            
            
           return $data;
        

        
    }




    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionGetItemUpdate($id)
     {
         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         $processo = Processo::findOne(['id'=>$id]);
         $model = Nord::findOne(['dsp_processo_id'=>$processo->id]);
         //return $id;
         if (($model !== null)&&file_exists(Yii::getAlias('@nords/').$model->id.'.xml')) {
 
             // print_r($model->id);die();
 
             $xmlData = simplexml_load_file(Yii::getAlias('@nords/').$model->id.'.xml');
 
             $data = [];                     
                 foreach ($xmlData->Item as $item) { 
                if (!empty($item->Taxation->Taxation_line)) {
                    $i = 0;
                     foreach ($item->Taxation->Taxation_line as  $Taxation_line) {
                             $i= (int)implode([$Taxation_line->Duty_tax_code]);
                             if($Taxation_line->Duty_tax_code >0){
                                 if (array_key_exists($i,$data)) {
                                     $data[$i]['tax_amount']=   $data[$i]['tax_amount'] +($Taxation_line->Duty_tax_amount*$Taxation_line->Duty_tax_MP);
                                 }else{
                                     $data[$i]= [
                                         'tax_code'=>(int)implode([$Taxation_line->Duty_tax_code]),
                                         'tax_descricao'=>Item::findOne(['id'=>(int)implode([$Taxation_line->Duty_tax_code])])->descricao,
                                         'tax_amount'=>  ($Taxation_line->Duty_tax_amount*$Taxation_line->Duty_tax_MP),
                                         'item_origem_id'=>'X',
 
                                     ] ;
                             }
                     }
                 }}}
 
                 foreach ($xmlData->Global_taxes as $glItem) { 
                     foreach ($glItem->Global_tax_item_line as  $Global_tax) {
                             $i= (int)implode([$Global_tax->Global_tax_code]);
                             if($Global_tax->Global_tax_code >0){
                                 if (array_key_exists($i,$data)) {
                                     $data[$i]['tax_amount']=   $data[$i]['tax_amount'] +($Global_tax->Global_tax_Base*$Global_tax->Golbal_tax_MP);
                                 }else{
                                     $data[$i]= [
                                         'tax_code'=>(int)implode([$Global_tax->Global_tax_code]),
                                         'tax_descricao'=>Item::findOne(['id'=>(int)implode([$Global_tax->Global_tax_code])])->descricao,
                                         'tax_amount'=>  ($Global_tax->Global_tax_Base*$Global_tax->Golbal_tax_MP),
                                         'item_origem_id'=>'X',
 
                                     ] ;
                             }
                     }
                     $i++;
                 }}
 
 
 
                 // if(($fpItemOld = FaturaProvisoriaItem::find()
                 //             ->join( 'INNER JOIN', 'fin_fatura_provisoria','fin_fatura_provisoria.id = fin_fatura_provisoria_item.dsp_fatura_provisoria_id')
                 //             ->where(['fin_fatura_provisoria.dsp_processo_id'=>$processo->id])
                 //             ->andWhere(['fin_fatura_provisoria.status'=>1])
                 //            ->all())!=null){
 
                 //         foreach ($fpItemOld as  $fpItem) {
                 //                 $i= (int)$fpItem->dsp_item_id;
                 //                 if($fpItem->dsp_item_id >0){
                 //                     if (array_key_exists($i,$data)) {
                 //                         $data[$i]['tax_amount']=   $data[$i]['tax_amount'] - $fpItem->valor;
                 //                     }else{
                 //                         $data[$i]= [
                 //                             'tax_code'=>(int)$fpItem->dsp_item_id,
                 //                             'tax_descricao'=>Item::findOne(['id'=>(int)$fpItem->dsp_item_id])->descricao,
                 //                             'tax_amount'=>  -$fpItem->valor,
                 //                             'item_origem_id'=>'X',
                 //                         ] ;
                 //                 }
                 //         }
                 //         $i++;
                 //     }
                 // }
 
                 if(($despesasItem = DespesaItem::find()
                         ->join( 'INNER JOIN', 'fin_despesa','fin_despesa.id = fin_despesa_item.fin_despesa_id')
                         ->where(['fin_despesa.dsp_processo_id'=>$processo->id])
                         ->andWhere(['fin_despesa.recebido'=>1])
                         ->andWhere(['fin_despesa.status'=>1])
                        //  ->andWhere(['>','fin_despesa.valor_pago',0])
                        ->all())!=null){
 
                     foreach ($despesasItem as  $dspItem) {
                             $i= $dspItem->item_id;
                             if($dspItem->item_id >0){
                                 if (array_key_exists($i,$data)) {
                                     //$data[$i]['tax_amount']=   $data[$i]['tax_amount'] + $dspItem->valor;
                                 }else{
                                     $data[$i]= [
                                         'tax_code'=>(int)$dspItem->item_id,
                                         'tax_descricao'=>Item::findOne(['id'=>(int)$dspItem->item_id])->descricao,
                                         'tax_amount'=>  $dspItem->valor,
                                         'item_origem_id'=>'D',
                                     ] ;
                             }
                     }
                     $i++;
                 }}
 
 
             
             
            return $data;
         }else{
             return ['error'=>'Error'];
         }
 
         
     }




     /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionValorAduaneiro($id)
     {
         Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         $model = Nord::findOne(['id'=>$id]);
         //return $id;
         if (($model !== null)&&file_exists(Yii::getAlias('@nords/').$model->id.'.xml')) {
 
             $xmlData = simplexml_load_file(Yii::getAlias('@nords/').$model->id.'.xml');
 
             $valorAduaneiro = 0;                     
                foreach ($xmlData->Item as $item) { 
            $valorAduaneiro = $valorAduaneiro + $item->Valuation_item->Statistical_value;
            }
             
             
            return $valorAduaneiro;
         }else{
             return ['error'=>'Error'];
         }
 
         
     }




    /**
     * Renders the index view for the module
     * @return string historico
     */
     public function actionDesembarcoPr()
     {
            $out = [];
            if (isset($_POST['depdrop_parents'])) {
                $parents = $_POST['depdrop_parents'];
 
                if ($parents != null) {
                    $id = $parents[0];;
                         $query = new Query;
                         $query->select('B.id as id, B.code as name')
                             ->from('dsp_nord A')
                             ->leftJoin('dsp_desembaraco B','A.dsp_desembaraco_id=B.id')
                             ->where(['A.dsp_processo_id'=> $id])
                             ->orderBy('B.code')
                             ->all();
                         $command = $query->createCommand();
                         $out = $command->queryAll();             
                     echo Json::encode(['output'=>$out, 'selected'=>'']);
                    return;
                }
            }
            echo Json::encode(['output'=>'', 'selected'=>'']);
          
     }


    
}
