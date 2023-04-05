<?php

namespace app\modules\cnt\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Model;

use app\modules\cnt\models\Lancamento;
use app\modules\cnt\models\LancamentoItem;
use app\modules\cnt\models\LancamentoSearch;
use app\modules\cnt\models\Razao;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\RazaoLancamento;
use app\modules\cnt\models\DiarioNumero;

/**
 * EdeficiosController implements the CRUD actions for Lancamento model.
 */
class LancamentoController extends Controller
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
                        'actions' => ['json-list'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index','view','create','update','delete','razao-lancamento','delete-lancamento'],
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
                    'delete-lancamento' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Lists all Lancamento models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LancamentoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lancamento model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'modelLancamentoItem'=>$model->lancamentoItem,
        ]);
    }

     /**
     * Creates a new Despesa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionCreate()
     {
         $model = new Lancamento;
         $modelsLancamentoItem = [new LancamentoItem];
 
         if ($model->load(Yii::$app->request->post())) {
             $modelsLancamentoItem = Model::createMultiple(LancamentoItem::classname());
             Model::loadMultiple($modelsLancamentoItem, Yii::$app->request->post()); 
 
             // ajax validation
             if (Yii::$app->request->isAjax) {
                 Yii::$app->response->format = Response::FORMAT_JSON;
                 return ArrayHelper::merge(
                     ActiveForm::validateMultiple($modelsLancamentoItem),
                     ActiveForm::validate($model)
                 );
             }
             // validate all models
             $valid = $model->validate();
            // $valid = Model::validateMultiple($modelsLancamentoItem) && $valid;
            
             if ($valid) {
                 $transaction = \Yii::$app->db->beginTransaction();
                 try {
 
                     if ($flag = $model->save()) {   
                         foreach ($modelsLancamentoItem as $modelLancamentoItem) {
                             $item = new LancamentoItem();
                             $item->cnt_lancamento_id = $model->id;
                             $item->origem_id = $modelLancamentoItem->origem_id;
                             $item->destino_id = $modelLancamentoItem->destino_id; 
                             if (! ($flag = $item->save())) {
                                 $transaction->rollBack();
                                 break;
                             }                            
                         }
                     }                       
 
                     if ($flag) {                       
                         $transaction->commit();
                         return $this->redirect(['view', 'id' => $model->id]);
                     }
                 } catch (Exception $e) {
                     $transaction->rollBack();
                 }
             }
         }
         return $this->render('create', [
             'model' => $model,
             'modelsLancamentoItem' => (empty($modelsLancamentoItem)) ? [new LancamentoItem] : $modelsLancamentoItem,
             
         ]);
     }

    /**
     * Updates an existing Lancamento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelsLancamentoItem = $model->lancamentoItem;


        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsLancamentoItem, 'id', 'id');
            $modelsLancamentoItem = Model::createMultiple(LancamentoItem::classname(), $modelsLancamentoItem);
            Model::loadMultiple($modelsLancamentoItem, Yii::$app->request->post());
             $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsLancamentoItem, 'id', 'id')));
            
            // ajax validation
            // if (Yii::$app->request->isAjax) {
            //     Yii::$app->response->format = Response::FORMAT_JSON;
            //     return ArrayHelper::merge(
            //         ActiveForm::validateMultiple($modelsLancamentoItem),
            //         ActiveForm::validate($model)
            //     );
            // }
            $valid = $model->validate();
            
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {
                        if (! empty($deletedIDs)) {
                            LancamentoItem::deleteAll(['path' => $deletedIDs]);
                        }
                        foreach ($modelsLancamentoItem as $modelLancamentoItem) {
                            $modelLancamentoItem->cnt_lancamento_id = $model->id;
                            if (! ($flag = $modelLancamentoItem->save())) {
                                print_r($modelLancamentoItem);die();
                                $transaction->rollBack();
                                break;
                            }
                        }

                    }

                    
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsLancamentoItem' => (empty($modelsLancamentoItem)) ? [new LancamentoItem] : $modelsLancamentoItem,

        ]);
    }

    /**
     * Deletes an existing Lancamento model.
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
     * Finds the Lancamento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lancamento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lancamento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


     /**
     * Lists all Lancamento models.
     * @return mixed
     */
    public function actionRazaoLancamento()
    {
        $razaLancamento = RazaoLancamento::find()->all();

        return $this->render('razao-lancamento', [
            'razaLancamento' => $razaLancamento,
        ]);
    }



    /**
     * Lists all Lancamento models.
     * @return mixed
     */
    public function actionDeleteLancamento($id)
    {
        $formatter = Yii::$app->formatter;
        $razaLancamento = RazaoLancamento::findOne($id);

        $razao = Razao::find()->where(['operacao_fecho'=>$razaLancamento->id])->all();
        foreach ($razao as $key => $value) {
            if(($diarioNumero = DiarioNumero::find()
                              ->where([
                                  'cnt_diario_id'=>$value->cnt_diario_id,
                                  'ano'=>\app\models\Ano::findOne($value->bas_ano_id)->ano,
                                  'mes'=>$value->bas_mes_id
                                ])
            ->one())!=null){
            $diarioNumero->numero = ($diarioNumero->numero -1);
            $diarioNumero->save(false);
            }
           $RazaoItem = RazaoItem::find()->where(['cnt_razao_id'=>$value->id])->all();
           foreach ($RazaoItem as $key => $data) {
               $data->delete();
           }
           $value->delete();
        }
       $razaLancamento->delete();

        Yii::$app->getSession()->setFlash('succes', 'Operação Realizado com sucesso');
        return $this->redirect(Yii::$app->request->referrer);
    }
    
}