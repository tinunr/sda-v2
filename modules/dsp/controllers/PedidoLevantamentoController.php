<?php

namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use arogachev\excel\import\basic\Importer;
use yii\helpers\Html;
use PhpOffice\PhpSpreadsheet\IOFactory;
use app\modules\dsp\models\PedidoLevantamento;
use app\modules\dsp\models\PedidoLevantamentoSearch;
use app\modules\dsp\models\Desembaraco;
use app\models\Ano;


/**
 * PedidoLevantamentoController implements the CRUD actions for PedidoLevantamento model.
 */
class PedidoLevantamentoController extends Controller
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
                        'actions' => ['get-data', 'get-item', 'valor-aduaneiro', 'import-excel'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['ajax', 'index', 'view', 'create', 'update', 'delete'],
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
     * Lists all PedidoLevantamento models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new PedidoLevantamentoSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PedidoLevantamento model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $bas_ano_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $bas_ano_id),
        ]);
    }


    /**
     * Lists all PedidoLevantamento models.
     * @return mixed
     */
    public function actionImportExcel()
    {
        $inputFileName = Yii::getAlias('@imports/PedidoLevantamento.xlsx');
        $spreadsheet = IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        foreach ($sheetData as $row) {

            $ano = Ano::find()->select('id')->where(['ano' => $row['A']])->scalar();
            $dsp_desembaraco_id = Desembaraco::find()->select('id')->where(['code' => $row['B']])->scalar();

            if (!empty($ano) && !empty($dsp_desembaraco_id) && !empty($row['D'])) {
				
                if (($model = PedidoLevantamento::findOne(['id' => $row['D'], 'bas_ano_id' => $ano, 'dsp_desembaraco_id' => $dsp_desembaraco_id])) !== null) {
                } else {
                    $model = new PedidoLevantamento();
				}
                    $model->bas_ano_id = $ano; //Ano
                    $model->dsp_desembaraco_id = $dsp_desembaraco_id; //estancia
                    $model->ser = $row['C']; //Ser,
                    $model->id = $row['D']; //id
                    $model->data_registo = (!empty($row['E']))?date('Y-m-d', strtotime($row['E'])):NULL; //Data Elaboração
                    $model->declarante = $row['F']; // Declarante
                    $model->importador_nif = $row['G']; //NIF Importador,
                    $model->importador_nome = $row['H']; //Nome Importador,
                    $model->manifesto = $row['I']; //Manifesto,
                    $model->titulo_propriedade = $row['J']; //Título propriedade,
                    $model->vereficador = $row['K']; //Vereficador',
                    $model->reverificador = $row['L']; // Reverificador,
                    $model->n_volumes = $row['M']; //Nº Volumes,
                    $model->r_volumes = $row['N']; //Rº Volumes,
                    $model->peso_bruto = $row['O']; //Peso Bruto,
                    $model->data_regularizacao = (!empty($row['P']))?date('Y-m-d', strtotime($row['P'])):NULL;// Data Regularização
                    if (!$model->save()) {
                        Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['encode' => false]));
                        return $this->redirect(Yii::$app->request->referrer);

                    }
                
            }
            
        }
		Yii::$app->getSession()->setFlash('success', 'Pedido de levantamento atualizado com sucesso!');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Lists all PedidoLevantamento models.
     * @return mixed
     */
    public function actionImportExcelll()
    {
        $importer = new Importer([
            'filePath' => Yii::getAlias('@imports/PedidoLevantamento.xlsx'),
            'standardModelsConfig' => [
                [
                    'className' => PedidoLevantamento::className(),
                    'standardAttributesConfig' => [
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
                        [
                            'name' => 'data_registo',
                            'valueReplacement' => function ($value) {
                                return \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
                            },
                        ],
                        [
                            'name' => 'data_regularizacao',
                            'valueReplacement' => function ($value) {
                                return \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
                            },
                        ],
                    ],
                ],
            ],
        ]);
        // print_r($importer);die();
        if (!$importer->run()) {
            print_r($importer->error);
            die();
            Yii::$app->getSession()->setFlash('danger', $importer->error);
            if ($importer->wrongModel) {
                Yii::$app->getSession()->setFlash('danger', Html::errorSummary($importer->wrongModel));
            }
        } else {
            \app\modules\dsp\services\DspService::ProcessoUpdateAllStatus();
            Yii::$app->getSession()->setFlash('success', 'Base de dados atualizado com sucesso');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Updates an existing PedidoLevantamento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $bas_ano_id)
    {
        $model = $this->findModel($id, $bas_ano_id);



        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PedidoLevantamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($numero, $ano, $dsp_desembaraco_id)
    {
        $this->findModel($numero, $ano, $dsp_desembaraco_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PedidoLevantamento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PedidoLevantamento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $bas_ano_id)
    {
        if (($model = PedidoLevantamento::findOne(['id' => $id, 'bas_ano_id' => $bas_ano_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the PedidoLevantamento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PedidoLevantamento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionValorAduaneiro($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = PedidoLevantamento::findOne(['id' => $id]);
        //return $id;
        if (($model !== null) && file_exists(Yii::getAlias('@app/web/PedidoLevantamentos/') . $model->id . '.xml')) {

            $xmlData = simplexml_load_file(Yii::getAlias('@app/web/PedidoLevantamentos/') . $model->id . '.xml');

            $valorAduaneiro = 0;
            foreach ($xmlData->Item as $item) {
                $valorAduaneiro = $valorAduaneiro + $item->Valuation_item->Statistical_value;
            }


            return $valorAduaneiro;
        } else {
            return ['error' => 'Error'];
        }
    }
}
