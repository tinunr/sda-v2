<?php

namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\db\Query;

use app\modules\dsp\models\Regime;
use app\modules\dsp\models\RegimeSearch;
use app\modules\dsp\models\RegimeItem;
use app\modules\dsp\models\RegimeItemItem;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\Nord;
use app\modules\fin\models\FaturaProvisoria;

/**
 * EdeficiosController implements the CRUD actions for Regime model.
 */
class RegimeController extends Controller
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
                        'actions' => ['regime-item', 'regime-item-table', 'create-regime-item', 'update-regime-item', 'get-regime-data', 'get-regime-data-update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],                                       [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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
     * Lists all Regime models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RegimeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Regime model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {


        $model = $this->findModel($id);
        $regimeData = $model->regimeItem;
        return $this->render('view', [
            'model' => $this->findModel($id),
            'regimeData' => $regimeData,
        ]);
    }

    /**
     * Creates a new Regime model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Regime();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Regime model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateRegimeItem($id)
    {
        $model = new RegimeItem();
        $model->dsp_regime_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('_form_regime_item', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Regime model.
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
     * Updates an existing Regime model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateRegimeItem($id)
    {
        $model = $this->findModelRegimeItem($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->dsp_regime_id]);
        }

        return $this->render('_form_regime_item', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Regime model.
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
     * Finds the Regime model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Regime the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Regime::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Regime model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Regime the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelRegimeItem($id)
    {
        if (($model = RegimeItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }




    /**
     * Renders the index view for the module
     * @return string historico
     */
    public function actionRegimeItem()
    {
        $out = [];
        $id = null;
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                if ($parents[0] != NULL) {
                    $dsp_processo_id = $parents[0]; # code...
                    $processo = Processo::findOne(['id' => $dsp_processo_id]);
                    if (($model = Nord::findOne(['dsp_processo_id' => $processo->id])) != NULL) {
                        if (file_exists(Yii::getAlias('@nords/') . $model->id . '.xml')) {
                            $data = simplexml_load_file(Yii::getAlias('@nords/') . $model->id . '.xml');

                            foreach ($data->Item as  $item) {
                                if (!empty($item->Tarification->Extended_customs_procedure)) {
                                    $id = (int)$item->Tarification->Extended_customs_procedure;
                                } else {
                                    $id = $parents[1];
                                }
                            }
                        }
                    } elseif ($parents[1] != NULL) {
                        $id = $parents[1];
                    }
                } elseif ($parents[1] != NULL) {
                    $id = $parents[1];
                }

                // $regime = Regime::findOne(['id'=>$id]);


                $query = new Query;
                $query->select('id as id, descricao as name')
                    ->from('dsp_regime_item')
                    ->where(['dsp_regime_id' => $id])
                    ->orderBy('name')
                    ->all();
                $command = $query->createCommand();
                $out = $command->queryAll();

                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }


    /**
     * Renders the index view for the module
     * @return string historico
     */
    public function actionRegimeItemTable()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                $id = $parents[0];
                if (($regime = RegimeItem::findOne($id)) != null) {
                    $query = new Query;
                    $query->select('id as id, descricao as name')
                        ->from('dsp_regime_item')
                        ->where(['dsp_regime_id' => $regime->dsp_regime_parent_id])
                        ->orderBy('name')
                        ->all();
                    $command = $query->createCommand();
                    $out = $command->queryAll();
                    echo Json::encode(['output' => $out, 'selected' => '']);
                    return;
                }
                echo Json::encode(['output' => '', 'selected' => '']);
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }


 /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetRegimeData($id = null, $nord = null, $id_tabela_anexa = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $params = Yii::$app->params['regimeConfig'];
        $honorario = 0;
        $dsp_regime_item_tabela_anexa_valor = '';
        $dsp_regime_item_tabela_anexa = null;
        $dsp_regime_item_valor = 0;
        $Total_number_of_items = 0;
        $valorAduaneiro = 0;
        $dsp_desembaraco_id = null;
        $isencao_honorario = 0;
        if ($id != null && $nord != null ) {

            $regimeItem = RegimeItem::findOne(['id' => $id]);
            $nordData = Nord::findOne(['id' => $nord]);
            $dsp_desembaraco_id = $nordData->dsp_desembaraco_id;
            $isencao_honorario = $nordData->processo->person->isencao_honorario;
            $dsp_regime_item_tabela_anexa = $regimeItem->dsp_regime_parent_id;
            if (!empty($regimeItem->dsp_regime_parent_id)) {
                //tabela anexa
                $honorarioItem = \app\modules\dsp\services\NordService::getHonorarioValueRegimeTabelaAnexa($id, $nord);
                $dsp_regime_item_valor = $honorarioItem['table'];

                if (!empty($id_tabela_anexa) && ($id_tabela_anexa != 'Loading ...')) {
                    $regimeItemAnexa = RegimeItem::findOne(['id' => $id_tabela_anexa]);
                    $regimeItemItemS = \app\modules\dsp\services\NordService::regimeItemItemS($id_tabela_anexa);
                    // print_r($id_tabela_anexa);die();
                    if (empty($regimeItemItemS)) {
                        $honorarioItemAnexa = \app\modules\dsp\services\NordService::getHonorarioValueRegimeItem($id_tabela_anexa, $nord);
                        $honorario = ($honorarioItemAnexa['honorario'] * $regimeItem->valor);
                        $dsp_regime_item_tabela_anexa_valor = $honorarioItemAnexa['table'];
                        #print_r($honorarioItemAnexa);die();                        
                    } else {
                        $honorarioItemAnexa = \app\modules\dsp\services\NordService::getHonorarioValueRegimeItemItem($id_tabela_anexa, $nord);
                        $honorario = ($honorarioItemAnexa['honorario'] * $regimeItem->valor);
                        $dsp_regime_item_tabela_anexa_valor = $honorarioItemAnexa['table'];
                        # print_r($honorarioItemAnexa);die();
                    }
                }
            } else {
                if (empty(\app\modules\dsp\services\NordService::regimeItemItemS($id))) {
                    $honorarioItem = \app\modules\dsp\services\NordService::getHonorarioValueRegimeItem($id, $nord);
                    $honorario = $honorarioItem['honorario'];
                    $dsp_regime_item_valor = $honorarioItem['table'];
                } else {
                    $honorarioItem = \app\modules\dsp\services\NordService::getHonorarioValueRegimeItemItem($id, $nord);
                    $honorario = $honorarioItem['honorario'];
                    $dsp_regime_item_valor = $honorarioItem['table'];
                }
            }

            # VALORMINIMO DO HONORIO E MAXIO DO XML
            $valorAduaneiro = \app\modules\dsp\services\NordService::valorAduaneiro($nord);
            #print_r($valorAduaneiro);die(); 
            if ($honorario < $params['minHonorario']) {
                $honorario = $params['minHonorario'];
            }
            if ($honorario > $params['maxHonorario']) {
                $valoPercetual = $valorAduaneiro * $params['percSobreSobraMaxHonorario'];
                if ($valoPercetual < $params['maxHonorario']) {
                    $honorario = $params['maxHonorario'];
                }
                if ($valoPercetual >= $params['maxHonorario'] && $valoPercetual < $honorario) {
                    $honorario = $valoPercetual;
                }
            }
            $dataFP = FaturaProvisoria::find()
                ->where(['dsp_processo_id' => $nordData->dsp_processo_id])
                ->andWhere(['status' => 1])
                ->all();
            foreach ($dataFP as $key => $value) {
                foreach ($value->faturaProvisoriaItem as $key => $item) {
                    if ($item->dsp_item_id == 1002) {
                        $honorario = $honorario - $item->valor;
                    }
                }
            }

            $Total_number_of_items = (\app\modules\dsp\services\NordService::totalNumberOfItems($nord) - $params['ItemaNaoCobrar']);
        }
        return [
            'dsp_regime_item_valor' => $dsp_regime_item_valor,
            'dsp_regime_item_tabela_anexa' => $dsp_regime_item_tabela_anexa,
            'honorario' => round($honorario, 2),
            'Total_number_of_items' => $Total_number_of_items,
            'valorAduaneiro' => $valorAduaneiro,
            'dsp_regime_item_tabela_anexa_valor' => $dsp_regime_item_tabela_anexa_valor,
            'dsp_desembaraco_id' => $dsp_desembaraco_id,
            'isencao_honorario' => $isencao_honorario,
        ];
    }


    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGetRegimeDataUpdate($id, $nord, $id_tabela_anexa)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $params = Yii::$app->params['regimeConfig'];
        $honorario = 0;
        $regimeItem = RegimeItem::findOne(['id' => $id]);
        $nordData = Nord::findOne(['id' => $nord]);
        $dsp_regime_item_tabela_anexa_valor = '';



        if (!empty($regimeItem->dsp_regime_parent_id)) {
            //tabela anexa
            $honorarioItem = \app\modules\dsp\services\NordService::getHonorarioValueRegimeTabelaAnexa($id, $nord);
            $dsp_regime_item_valor = $honorarioItem['table'];

            if (!empty($id_tabela_anexa)) {
                $regimeItemAnexa = RegimeItem::findOne(['id' => $id_tabela_anexa]);
                $regimeItemItemS = \app\modules\dsp\services\NordService::regimeItemItemS($id_tabela_anexa);
                //print_r($id_tabela_anexa);die();
                if (empty($regimeItemItemS)) {
                    $honorarioItemAnexa = \app\modules\dsp\services\NordService::getHonorarioValueRegimeItem($id_tabela_anexa, $nord);
                    $honorario = ($honorarioItemAnexa['honorario'] * $regimeItem->valor);
                    $dsp_regime_item_tabela_anexa_valor = $honorarioItemAnexa['table'];
                    #print_r($honorarioItemAnexa);die();                        
                } else {
                    $honorarioItemAnexa = \app\modules\dsp\services\NordService::getHonorarioValueRegimeItemItem($id_tabela_anexa, $nord);
                    $honorario = ($honorarioItemAnexa['honorario'] * $regimeItem->valor);
                    $dsp_regime_item_tabela_anexa_valor = $honorarioItemAnexa['table'];

                    # print_r($honorarioItemAnexa);die();
                }
            }
        } else {

            if (empty(\app\modules\dsp\services\NordService::regimeItemItemS($id))) {
                $honorarioItem = \app\modules\dsp\services\NordService::getHonorarioValueRegimeItem($id, $nord);
                $honorario = $honorarioItem['honorario'];
                $dsp_regime_item_valor = $honorarioItem['table'];
            } else {
                $honorarioItem = \app\modules\dsp\services\NordService::getHonorarioValueRegimeItemItem($id, $nord);
                $honorario = $honorarioItem['honorario'];
                $dsp_regime_item_valor = $honorarioItem['table'];
            }
        }





        # VALORMINIMO DO HONORIO E MAXIO DO XML
        $valorAduaneiro = \app\modules\dsp\services\NordService::valorAduaneiro($nord);
        #print_r($valorAduaneiro);die();

        if ($honorario < $params['minHonorario']) {
            $honorario = $params['minHonorario'];
        }
        if ($honorario > $params['maxHonorario']) {
            $valoPercetual = $valorAduaneiro * $params['percSobreSobraMaxHonorario'];
            if ($valoPercetual < $params['maxHonorario']) {
                $honorario = $params['maxHonorario'];
            }
            if ($valoPercetual >= $params['maxHonorario'] && $valoPercetual < $honorario) {
                $honorario = $valoPercetual;
            }
        }
        $isencao_honorario = empty($nordData->processo->person->isencao_honorario) ? 0 : $nordData->processo->person->isencao_honorario;
        return [
            'dsp_regime_item_valor' => $dsp_regime_item_valor,
            'dsp_regime_item_tabela_anexa' => $regimeItem->dsp_regime_parent_id,
            'honorario' => round($honorario, 2),
            'Total_number_of_items' => (\app\modules\dsp\services\NordService::totalNumberOfItems($nord) - $params['ItemaNaoCobrar']),
            'valorAduaneiro' => $valorAduaneiro,
            'dsp_regime_item_tabela_anexa_valor' => $dsp_regime_item_tabela_anexa_valor,
            'isencao_honorario' => $isencao_honorario,

        ];
    }
}
