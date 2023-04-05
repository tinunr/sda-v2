<?php

namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use PhpOffice\PhpSpreadsheet\IOFactory;

use app\modules\dsp\models\Despacho;
use app\modules\dsp\models\DespachoSearch;
use arogachev\excel\import\basic\Importer;
use yii\helpers\Html;
use app\modules\dsp\models\Desembaraco;
use app\models\Ano; 
/**
 * DespachoController implements the CRUD actions for Despacho model.
 */
class DespachoController extends Controller
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
     * Lists all Despacho models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new DespachoSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Despacho model.
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
     * Lists all despacho models.
     * @return mixed
     */
    public function actionImportExcel()
    {
        // $f = Yii::$app->formatter;
        $inputFileName = Yii::getAlias('@imports/Despachos.xlsx');
        $spreadsheet = IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
       //print_r($sheetData);die();
        foreach ($sheetData as $key=> $row) {
            $ano = Ano::find()->select('id')->where(['ano' => $row['A']])->scalar();
            $dsp_desembaraco_id = Desembaraco::find()->select('id')->where(['code' => $row['B']])->scalar();
 
            if (!empty($ano) && !empty($dsp_desembaraco_id) && !empty($row['D']) && !empty($row['F'])) {
        //       print_r($row);
        // die();  
                if (($model = Despacho::findOne(['id' => $row['H'], 'bas_ano_id' => $ano, 'dsp_desembaraco_id' => $dsp_desembaraco_id, 'nord' => $row['F']])) !== null) {
                } else {
                    $model = new Despacho();
                }
                $model->bas_ano_id = $ano; //Ano
                $model->dsp_desembaraco_id = $dsp_desembaraco_id; //estancia
                $model->modelo = $row['C']; //Modelo,
                $model->rg = $row['D']; //RG
                $model->declarante = $row['E'];  //Declarante
                $model->nord = $row['F']; // NORD
                $model->s_registo = $row['G']; //S-Reg,
                $model->id = $row['H']; //Nº Registo,
                $model->data_registo = (!empty($row['I']))?date('Y-m-d', strtotime($row['I'])):NULL; //Data Registo,
                $model->artigo = $row['J']; //Art.,
                $model->destinatario_nif = $row['K']; //Destinatário',
                $model->destinatario_nome = $row['L']; // Destinatário Name,
                $model->s_liquidade = $row['O']; //S_Liquidade,
                $model->numero_liquidade = $row['P']; //Nº Liquidação,
                $model->data_liquidacao = (!empty($row['Q']))?date('Y-m-d', strtotime($row['Q'])):NULL; //Data Liquidação,
                $model->n_receita = $row['R']; //Nº Receita
                $model->data_receita = (!empty($row['S']))?date('Y-m-d', strtotime($row['S'])):NULL; // Data Receita,
                $model->time_end_freeze = $row['U']; //Time end Freeze
                $model->verificador = $row['V']; //Verificador,
                $model->reverificador = $row['W']; //Reverificador,
                $model->tramitacao = $row['X']; //Tramitação,
                $model->valor = (int)$row['Y']; //Total Imposições,
                $model->cor = $row['Z']; //Cor,
                $model->anulado = $row['AA']==1?$row['AA']:0; //Anulado,
                if (!$model->save()) {
					 Yii::$app->getSession()->setFlash('warning',Html::errorSummary($model,['header'=>'Erro na linha '.$key ]));
        return $this->redirect(Yii::$app->request->referrer);
                    $errors = [
                        'modelErrors' => $model->errors,
                        'row' => $row,
                        'model' => $model,
                    ];
                    print_r($errors);
                    die();
                }
            }
        }

        Yii::$app->getSession()->setFlash('success', 'Despacho atualizado com sucesso!');
        return $this->redirect(Yii::$app->request->referrer);
    }



    /**
     * Lists all despacho models.
     * @return mixed
     */
    public function actionImportExcell()
    {
        $importer = new Importer([
            'filePath' => Yii::getAlias('@imports/Despachos.xlsx'),
            'standardModelsConfig' => [
                [
                    'className' => Despacho::className(),
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
                            'name' => 'data_prorogacao',
                            'valueReplacement' => function ($value) {
                                return \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
                            },
                        ],
                        [
                            'name' => 'data_liquidacao',
                            'valueReplacement' => function ($value) {
                                return \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
                            },
                        ],
                        [
                            'name' => 'data_receita',
                            'valueReplacement' => function ($value) {
                                return \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
                            },
                        ],
                        [
                            'name' => 'anulado',
                            'valueReplacement' => function ($value) {
                                return 0;
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
        } else {
            // \app\modules\dsp\services\DspService::ProcessoUpdateAllStatus();
            Yii::$app->getSession()->setFlash('success', 'Base de dados atualizado com sucesso');
        }





        $importer = new Importer([
            'filePath' => Yii::getAlias('@imports/DespachosAnulados.xlsx'),
            'standardModelsConfig' => [
                [
                    'className' => Despacho::className(),
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
                            'name' => 'data_prorogacao',
                            'valueReplacement' => function ($value) {
                                return \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
                            },
                        ],
                        [
                            'name' => 'data_liquidacao',
                            'valueReplacement' => function ($value) {
                                return \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
                            },
                        ],
                        [
                            'name' => 'data_receita',
                            'valueReplacement' => function ($value) {
                                return \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
                            },
                        ],
                        [
                            'name' => 'anulado',
                            'valueReplacement' => function ($value) {
                                return 1;
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
        } else {
            // \app\modules\dsp\services\DspService::ProcessoUpdateAllStatus();
            Yii::$app->getSession()->setFlash('success', 'Base de dados atualizado com sucesso');
        }




        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Creates a new Despacho model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Despacho();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Despacho model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);



        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Despacho model.
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
     * Finds the Despacho model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Despacho the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Despacho::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Despacho model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Despacho the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionValorAduaneiro($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = Despacho::findOne(['id' => $id]);
        //return $id;
        if (($model !== null) && file_exists(Yii::getAlias('@app/web/Despachos/') . $model->id . '.xml')) {

            $xmlData = simplexml_load_file(Yii::getAlias('@app/web/Despachos/') . $model->id . '.xml');

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
