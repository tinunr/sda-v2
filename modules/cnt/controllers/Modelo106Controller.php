<?php

namespace app\modules\cnt\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\httpclient\XmlParser;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\db\ActiveQuery;
use yii\data\ArrayDataProvider;
use kartik\mpdf\Pdf;
// use yii2tech\spreadsheet\Spreadsheet;
use yii\data\ActiveDataProvider;

use app\modules\cnt\models\Modelo106;
use app\modules\cnt\models\Modelo106Search;
use app\modules\cnt\models\Modelo106Cliente;
use app\modules\cnt\models\Modelo106Fornecedor;
use app\models\Ano;
use app\models\Parameter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Modelo106Controller implements the CRUD actions for Modelo106 model.
 */
class Modelo106Controller extends Controller
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
                        'actions' => ['get-data'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'modelo106-pdf', 'update-cliente', 'update-fornecedor', 'cliente-xml', 'fornecedor-xml', 'cliente-pdf', 'fornecedor-pdf', 'cliente-create', 'fornecedor-create', 'cliente-delete', 'fornecedor-delete', 'update-cliente-cnt', 'update-fornecedor-cnt', 'modelo106-update', 'modelo106-update-auto', 'modelo106-xml', 'cliente-excel', 'fornecedor-excel', 'bloquear-modelo106'],
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
                    'bloquear-modelo106' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Lists all Modelo106 models.
     * @return mixed
     */
    public function actionIndex()
    {

        $model = new Modelo106();
        $model->ano =  substr(date('Y'), -2);
        $model->mes = date('m');
        $searchModel = new Modelo106Search();
        $searchModel->ano =  substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Modelo106 model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $model->modelo106Cliente,
            'pagination' => false,
        ]);
        $dataProviderFornecedor = new ArrayDataProvider([
            'allModels' => $model->modelo106Fornecedor,
            'pagination' => false,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'dataProviderFornecedor' => $dataProviderFornecedor,
        ]);
    }

    /**
     * Displays a single Modelo106 model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBloquearModelo106($id)
    {
        $model = $this->findModel($id);
        if ($model->bloquear_modelo == 0) {
            $model->bloquear_modelo = 1;
        } else {
            $model->bloquear_modelo = 0;
        }
        return $this->redirect('view', [
            'id' => $id,
        ]);
    }



    /**
     * Creates a new Modelo106 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionClienteExcel($cnt_modelo_106_id)
    {
        $model  = $this->findModel($cnt_modelo_106_id);
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(10);
        $sheet->getColumnDimension('M')->setWidth(10);

        $sheet->setTitle('Anexo Cliente');
        $sheet->setCellValue('C1', 'Agência de Despacho Aduaneiro Morais & Cruz, Lda');
        $sheet->setCellValue('C2', 'Modelos Fiscais - Anexo Cliente');
        $sheet->setCellValue('C3', 'Ano: ' . $model->ano . ' Mês :' . $model->mes);

        $dateTimeNow = time();
        $sheet->setCellValue('L1', 'Data:');
        $sheet->setCellValue('M1', Date::PHPToExcel($dateTimeNow));
        $sheet->getStyle('M1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

        $sheet->getStyle('C1:C3')->getFill()->getStartColor('#3c5462')->setARGB('#F1F2F3');
        $sheet->getStyle('C1:C3')->getFont()->setBold(TRUE);


        $sheet->setCellValue('A5', 'Origem');
        $sheet->setCellValue('B5', 'NIF Entidade');
        $sheet->setCellValue('C5', 'Designação da Entidade');
        $sheet->setCellValue('D5', 'Série');
        $sheet->setCellValue('E5', 'Tipo Doc.');
        $sheet->setCellValue('F5', 'Nº Doc.');
        $sheet->setCellValue('G5', 'Data');
        $sheet->setCellValue('H5', 'Valor da Fatura');
        $sheet->setCellValue('I5', 'Valor Base de Incidencia');
        $sheet->setCellValue('J5', 'Taxa IVA');
        $sheet->setCellValue('K5', 'IVA Liquidado');
        $sheet->setCellValue('L5', 'Não Liquidação  Imposto');
        $sheet->setCellValue('M5', 'Linha destino Modelo');

        // $sheet->setAutoFilter('A5:M5');
        $sheet->getStyle('A5:M5')->getAlignment()->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A5:M5')->getFont()->setBold(TRUE);
        $sheet->getStyle('A5:M5')->applyFromArray($styleArray);
        $query = Modelo106Cliente::find()->select([
            'origem',
            'nif',
            'designacao',
            'serie',
            'tp_doc',
            'num_doc',
            'data',
            'vl_fatura',
            'vl_base_incid',
            'tx_iva',
            'iva_liq',
            'nao_liq_imp',
            'linha_dest_mod',
        ])->where(['cnt_modelo_106_id' => $cnt_modelo_106_id]);
        $command = $query->createCommand();
        // $command->sql returns the actual SQL
        $rows = $command->queryAll();
        // print_r($rows);die();
        $sheet = $spreadsheet->getActiveSheet()
            ->fromArray(
                $rows,  // The data to set
                NULL,        // Array values with this value will not be set
                'A6'         // Top left coordinate of the worksheet range where
                //    we want to set these values (default is A1)
            );

        $writer = new Xlsx($spreadsheet);
        $writer->save('Spreadsheet/AnexoCliente.xlsx');
        $this->redirect(Url::to('@web/Spreadsheet/AnexoCliente.xlsx'))->send();
        // return $exporter->send('Modelo106Cliente.x');
    }


    /**
     * Creates a new Modelo106 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionFornecedorExcel($cnt_modelo_106_id)
    {
        $model  = $this->findModel($cnt_modelo_106_id);
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(10);
        $sheet->getColumnDimension('M')->setWidth(10);
        $sheet->getColumnDimension('N')->setWidth(10);

        $sheet->setTitle('Anexo Fornecedor');
        $sheet->setCellValue('C1', 'Agência de Despacho Aduaneiro Morais & Cruz, Lda');
        $sheet->setCellValue('C2', 'Modelos Fiscais - Anexo Fornecedor');
        $sheet->setCellValue('C3', 'Ano: ' . $model->ano . ' Mês :' . $model->mes);

        $dateTimeNow = time();
        $sheet->setCellValue('L1', 'Data:');
        $sheet->setCellValue('N1', Date::PHPToExcel($dateTimeNow));
        $sheet->getStyle('N1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

        $sheet->getStyle('C1:C3')->getFill()->getStartColor('#3c5462')->setARGB('#F1F2F3');
        $sheet->getStyle('C1:C3')->getFont()->setBold(TRUE);


        $sheet->setCellValue('A5', 'Origem');
        $sheet->setCellValue('B5', 'NIF Entidade');
        $sheet->setCellValue('C5', 'Designação da Entidade');
        $sheet->setCellValue('D5', 'Tipo Doc.');
        $sheet->setCellValue('E5', 'Nº Doc.');
        $sheet->setCellValue('F5', 'Data');
        $sheet->setCellValue('G5', 'Valor da Fatura');
        $sheet->setCellValue('H5', 'Valor Base de Incidencia');
        $sheet->setCellValue('I5', 'Taxa IVA');
        $sheet->setCellValue('J5', 'IVA Suportado');
        $sheet->setCellValue('K5', 'Direito Ded.');
        $sheet->setCellValue('L5', 'IVA Dedutivel');
        $sheet->setCellValue('M5', 'Tipologia');
        $sheet->setCellValue('N5', 'Linha Destino Modelo');

        $sheet->getStyle('A5:N5')->getAlignment()->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A5:N5')->getFont()->setBold(TRUE);
        $sheet->getStyle('A5:N5')->applyFromArray($styleArray);
        $query = Modelo106Fornecedor::find()->select([
            'origem',
            'nif',
            'designacao',
            'tp_doc',
            'num_doc',
            'data',
            'vl_fatura',
            'vl_base_incid',
            'tx_iva',
            'iva_sup',
            'direito_ded',
            'iva_ded',
            'tipologia',
            'linha_dest_mod',
        ])->where(['cnt_modelo_106_id' => $cnt_modelo_106_id]);
        $command = $query->createCommand();
        // $command->sql returns the actual SQL
        $rows = $command->queryAll();
        // print_r($rows);die();
        $sheet = $spreadsheet->getActiveSheet()
            ->fromArray(
                $rows,  // The data to set
                'NULL',        // Array values with this value will not be set
                'A6'         // Top left coordinate of the worksheet range where
                //    we want to set these values (default is A1)
            );

        $writer = new Xlsx($spreadsheet);
        $writer->save('Spreadsheet/AnexoFornecedor.xlsx');
        $this->redirect(Url::to('@web/Spreadsheet/AnexoFornecedor.xlsx'))->send();
    }




    /**
     * Creates a new Modelo106 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $count = 0;
        $transaction = Yii::$app->db->beginTransaction();


        $model = new Modelo106();
        if ($model->load(Yii::$app->request->post())) {
            $model->cd_af = Parameter::getValue('CONTABILIDADE', 'COMPANY_CD_AF');
            $model->nif = Parameter::getValue('CONTABILIDADE', 'COMPANY_NIF');
            $model->designacao_social = Parameter::getValue('CONTABILIDADE', 'COMPANY_DESIGNACAO_SOCIAL');
            $model->reparticao_financa = Parameter::getValue('CONTABILIDADE', 'COMPANY_LOC_APRESENTACAO');
            $model->nif_representante_legal = Parameter::getValue('CONTABILIDADE', 'COMPANY_NIF_REPRESENTANTE_LEGAL');
            $model->representante_legal = Parameter::getValue('CONTABILIDADE', 'COMPANY_REPRESENTANTE_LEGAL');
            $model->tecnico_conta_nome = Parameter::getValue('CONTABILIDADE', 'COMPANY_TECNICO_CONTA_NOME');
            $model->tecnico_conta_nif = Parameter::getValue('CONTABILIDADE', 'COMPANY_TECNICO_CONTA_NIF');
            $model->tipo_entrega = 0;
            $model->doc_cliente = 0;
            $model->doc_fornecedor = 0;
            $model->doc_reg_cliente =0;
            $model->doc_reg_fornecedor =0; 
            $model->data = date('Y-m-d');
            $ano = Ano::findOne($model->ano)->ano;

            if (!$model->save()) {
                print_r($model->errors);
                die();
                Yii::$app->getSession()->setFlash('warning', 'Erro ao executar a operação');
                $transaction->rollBack();
            }
            // print_r($model);die();
            // Anexo de Cliente
            $query = Yii::$app->CntQuery->modelo106Cliente($model->ano, $model->mes);
            foreach ($query as $key_c => $value) {
                ++$count;
                $origem = Yii::$app->CntQuery->origemData($value['cnt_razao_id']);
                $anexoCliente = new Modelo106Cliente();
                $anexoCliente->cnt_modelo_106_id = $model->id;
                $anexoCliente->origem = empty($origem->origem) ? null : $origem->origem;
                $anexoCliente->nif = empty($origem->nif) ? null : $origem->nif;
                $anexoCliente->designacao = empty($origem->designacao) ? null : $origem->designacao;
                $anexoCliente->tp_doc = empty($origem->tp_doc) ? null : $origem->tp_doc;
                $anexoCliente->num_doc = $value['numero'];
                $anexoCliente->data = !empty($origem->data) ? $origem->data : $value['data'];
                $anexoCliente->vl_fatura = ceil($value['vl_fatura']);
                $anexoCliente->vl_base_incid = ceil($value['vl_base_incid']);
                $anexoCliente->tx_iva = ceil($value['tx_iva']);
                $anexoCliente->iva_liq = ceil($value['iva_liq']);
                $anexoCliente->nao_liq_imp = null;
                $anexoCliente->linha_dest_mod = $value['linha_dest_mod'];
                if (!$anexoCliente->save()) {
                    $errors['errors'] = $anexoCliente->errors;
                    $errors['item'] = $value;
                    print_r($errors);
                    die();
                    Yii::$app->getSession()->setFlash('warning', 'Erro ao executar a operação');
                    $transaction->rollBack();
                    break;
                }
            }



            // Anexo de fornecedor
            $query2 = Yii::$app->CntQuery->modelo106Fornecedor($model->ano, $model->mes);
            foreach ($query2 as $key_f => $value) {
                ++$count;
                $origem = Yii::$app->CntQuery->origemData($value['cnt_razao_id']);
                $anexoCliente = new Modelo106Fornecedor();
                $anexoCliente->cnt_modelo_106_id = $model->id;
                $anexoCliente->origem = $origem->origem;
                $anexoCliente->nif = $origem->nif;
                $anexoCliente->designacao = $origem->designacao;
                $anexoCliente->tp_doc = $value['documento_origem_tipo'];
                $anexoCliente->num_doc = $value['numero'];
                $anexoCliente->data = !empty($origem->data) ? $origem->data : null;;
                $anexoCliente->vl_fatura = ceil($value['vl_fatura']);
                $anexoCliente->vl_base_incid = ceil($value['vl_base_incid']);
                $anexoCliente->tx_iva = $value['tx_iva'];
                $anexoCliente->iva_sup = ceil($value['iva_sup']);
                $anexoCliente->direito_ded = ceil($value['direito_ded']);
                $anexoCliente->iva_ded = ceil($value['iva_ded']);
                $anexoCliente->tipologia = $value['tipologia'];
                $anexoCliente->linha_dest_mod = $value['linha_dest_mod'];
                if (!$anexoCliente->save()) {
                    $errors = [
                        'errors' => $anexoCliente->errors,
                        'origem' => $origem,
                    ];
                    print_r($errors);
                    die();
                    $transaction->rollBack();
                    break;
                    Yii::$app->getSession()->setFlash('warning', 'Erro ao executar a operação');
                }
            }

            $model = $this->findModelAndUpdate($model->id);
        }
        $transaction->commit();

        Yii::$app->getSession()->setFlash('success', ($count) . ' ITEM DO ANEXO DOS ANEXOS ATUALIZADO COM SUCESSO');

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Updates an existing Modelo106 model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateClienteCnt($id)
    {
        $count = 0;
        $model = $this->findModel($id);
        $ano = Ano::findOne($model->ano)->ano;
        $Modelo106Cliente = Modelo106Cliente::find()->where(['cnt_modelo_106_id' => $model->id])->all();
        foreach ($Modelo106Cliente as $key_c => $value) {
            $value->delete();
        }
        $query = Yii::$app->CntQuery->modelo106Cliente($model->ano, $model->mes);
        foreach ($query as $key_c => $value) {
            $count++;
            $origem = Yii::$app->CntQuery->origemData($value['cnt_razao_id']);
            // print_r($value);die();
            $anexoCliente = new Modelo106Cliente();
            $anexoCliente->cnt_modelo_106_id = $model->id;
            $anexoCliente->origem = empty($origem->origem) ? null : $origem->origem;
            $anexoCliente->nif = empty($origem->nif) ? null : $origem->nif;
            $anexoCliente->designacao = empty($origem->designacao) ? null : $origem->designacao;
            $anexoCliente->tp_doc = empty($origem->tp_doc) ? null : $origem->tp_doc;
            $anexoCliente->num_doc = $value['numero'];
            $anexoCliente->data = $value['data'];
            $anexoCliente->vl_fatura = ceil($value['vl_fatura']);
            $anexoCliente->vl_base_incid = ceil($value['vl_base_incid']);
            $anexoCliente->tx_iva = $value['tx_iva'];
            $anexoCliente->iva_liq = ceil($value['iva_liq']);
            $anexoCliente->nao_liq_imp = null;
            $anexoCliente->linha_dest_mod = $value['linha_dest_mod'];
            if (!$anexoCliente->save()) {
                print_r($anexoCliente);
                die();
            }
        }



        $model = $this->findModelAndUpdate($id);

        Yii::$app->getSession()->setFlash('success', ($count) . ' ITEM DOS ANEXO ATUALIZADO COM SUCESSO');

        return $this->redirect(Yii::$app->request->referrer);
        // return $this->redirect(['view', 'id' => $model->id]);

    }




    /**
     * Updates an existing Modelo106 model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateFornecedorCnt($id)
    {
        $count = 0;
        $model = $this->findModel($id);
        $ano = Ano::findOne($model->ano)->ano;


        // Anexo de fornecedor
        $Modelo106Fornecedor = Modelo106Fornecedor::find()->where(['cnt_modelo_106_id' => $model->id])->all();
        foreach ($Modelo106Fornecedor as $key_f => $value) {
            $value->delete();
        }
        $query2 = Yii::$app->CntQuery->modelo106Fornecedor($model->ano, $model->mes);

        foreach ($query2 as $key_f => $value) {
            $count++;
            $origem = Yii::$app->CntQuery->origemData($value['cnt_razao_id']);
            // print_r($value);die();

            $anexoFornecedor = new Modelo106Fornecedor();
            $anexoFornecedor->cnt_modelo_106_id = $model->id;
            $anexoFornecedor->origem = $origem->origem;
            $anexoFornecedor->nif = $origem->nif;
            $anexoFornecedor->designacao = $origem->designacao;
            $anexoFornecedor->tp_doc =  $value['documento_origem_tipo'];
            $anexoFornecedor->num_doc = $value['numero'];
            $anexoFornecedor->data = $value['documento_origem_data'];
            $anexoFornecedor->vl_fatura = ceil($value['vl_fatura']);
            $anexoFornecedor->vl_base_incid = ceil($value['vl_base_incid']);
            $anexoFornecedor->tx_iva = $value['tx_iva'];
            $anexoFornecedor->iva_sup = ceil($value['iva_sup']);
            $anexoFornecedor->direito_ded = $value['direito_ded'];
            $anexoFornecedor->iva_ded = ceil($value['iva_ded']);
            $anexoFornecedor->tipologia = $value['tipologia'];
            $anexoFornecedor->linha_dest_mod = $value['linha_dest_mod'];
            if (!$anexoFornecedor->save()) {
                print_r($anexoFornecedor->errors);
                die();
                // break;
                Yii::$app->getSession()->setFlash('warning', 'Erro ao executar a operação');
            }
        }





        $model = $this->findModelAndUpdate($id);

        Yii::$app->getSession()->setFlash('success', ($count) . ' ITEM DOS ANEXO ATUALIZADO COM SUCESSO');

        return $this->redirect(Yii::$app->request->referrer);
        // return $this->redirect(['view', 'id' => $model->id]);

    }



    /**
     * Deletes an existing Modelo106 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateCliente($id, $cnt_modelo_106_id)
    {
        $model = Modelo106Cliente::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $cnt_modelo_106_id]);
        }
        return $this->render('_form_cliente', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Modelo106 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateFornecedor($id, $cnt_modelo_106_id)
    {
        $model = Modelo106Fornecedor::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $cnt_modelo_106_id]);
        }
        return $this->render('_form_fornecedor', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Modelo106 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionClienteCreate($cnt_modelo_106_id)
    {
        $model = new Modelo106Cliente;
        $model->cnt_modelo_106_id = $cnt_modelo_106_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $cnt_modelo_106_id]);
        }
        return $this->render('_form_cliente', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Modelo106 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionFornecedorCreate($cnt_modelo_106_id)
    {
        $model = new Modelo106Fornecedor;
        $model->cnt_modelo_106_id = $cnt_modelo_106_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $cnt_modelo_106_id]);
        }
        return $this->render('_form_fornecedor', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing Modelo106 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionClienteDelete($id, $cnt_modelo_106_id)
    {
        $model = Modelo106Cliente::findOne(['id' => $id, 'cnt_modelo_106_id' => $cnt_modelo_106_id]);
        if ($model->delete()) {
            Yii::$app->getSession()->setFlash('success', 'Registo removido com sucesso');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Deletes an existing Modelo106 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionFornecedorDelete($id, $cnt_modelo_106_id)
    {
        $model = Modelo106Fornecedor::findOne(['id' => $id, 'cnt_modelo_106_id' => $cnt_modelo_106_id]);
        if ($model->delete()) {
            Yii::$app->getSession()->setFlash('success', 'Registo removido com sucesso');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }



    /**
     * Updates an existing Modelo106 model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionModelo106UpdateAuto($id)
    {
        $model = $this->findModelAndUpdate($id);
        Yii::$app->getSession()->setFlash('success', 'Modelo atualizado com sucesso');
        return $this->redirect(Yii::$app->request->referrer);
    }





    /**
     * Deletes an existing Modelo106 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionModelo106Xml($cnt_modelo_106_id)
    {
        $model = $this->findModel($cnt_modelo_106_id);
        $ano = Ano::findOne($model->ano);
        $xmldata = '<?xml version="1.0" encoding="UTF-8"?>';
$xmldata .= '<modelo106 xsi:noNamespaceSchemaLocation="https://nosiapps.gov.cv/grexsd/2014/mod106 modelo106.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
    $xmldata .= '<tp_dec_anx for_reg="'.$model->doc_reg_fornecedor.'" cli_reg="'.$model->doc_reg_cliente.'" for="'.$model->doc_fornecedor.'" cli="'.$model->doc_cliente.'" dec="' . ($model->tipo_entrega == 3 ? 4 : $model->tipo_entrega) . '" />';
    $xmldata .= '<nif>' . Parameter::getValue('CONTABILIDADE', 'COMPANY_NIF') . '</nif>';
    $xmldata .= '<periodo mes="' . $model->mes . '" ano="' . $ano->ano . '" />';
    $xmldata .= '<cd_af>' . Parameter::getValue('CONTABILIDADE', 'COMPANY_CD_AF') . '</cd_af>';
    $xmldata .= '<exist_oper>0</exist_oper>';
    $xmldata .= '<cp01>' . $model->valor_1 . '</cp01>';
    $xmldata .= '<cp02>' . $model->valor_2 . '</cp02>';
    $xmldata .= '<cp03>' . $model->valor_3 . '</cp03>';
    $xmldata .= '<cp04>' . $model->valor_4 . '</cp04>';
    $xmldata .= '<cp05>' . $model->valor_5 . '</cp05>';
    $xmldata .= '<cp06>' . $model->valor_6 . '</cp06>';
    $xmldata .= '<cp07>' . $model->valor_7 . '</cp07>';
    $xmldata .= '<cp08>' . $model->valor_8 . '</cp08>';
    $xmldata .= '<cp09>' . $model->valor_9 . '</cp09>';
    $xmldata .= '<cp10>' . $model->valor_10 . '</cp10>';
    $xmldata .= '<cp11>' . $model->valor_11 . '</cp11>';
    $xmldata .= '<cp12>' . $model->valor_12 . '</cp12>';
    $xmldata .= '<cp13>' . $model->valor_13 . '</cp13>';
    $xmldata .= '<cp14>' . $model->valor_14 . '</cp14>';
    $xmldata .= '<cp15>' . $model->valor_15 . '</cp15>';
    $xmldata .= '<cp16>' . $model->valor_16 . '</cp16>';
    $xmldata .= '<cp17>' . $model->valor_17 . '</cp17>';
    $xmldata .= '<cp18>' . $model->valor_18 . '</cp18>';
    $xmldata .= '<cp19>' . $model->valor_19 . '</cp19>';
    $xmldata .= '<cp20>' . $model->valor_20 . '</cp20>';
    $xmldata .= '<cp21>' . $model->valor_21 . '</cp21>';
    $xmldata .= '<cp22>' . $model->valor_22 . '</cp22>';
    $xmldata .= '<cp23>' . $model->valor_23 . '</cp23>';
    $xmldata .= '<cp24>' . $model->valor_24 . '</cp24>';
    $xmldata .= '<cp25>' . $model->valor_25 . '</cp25>';
    $xmldata .= '<cp26>' . $model->valor_26 . '</cp26>';
    $xmldata .= '<cp27>' . $model->valor_27 . '</cp27>';
    $xmldata .= '<cp28>' . $model->valor_28 . '</cp28>';
    $xmldata .= '<cp29>' . $model->valor_29 . '</cp29>';
    $xmldata .= '<cp30>' . $model->valor_30 . '</cp30>';
    $xmldata .= '<cp31 />';
    $xmldata .= '<cp32>' . $model->valor_32 . '</cp32>';
    $xmldata .= '<cp33>' . $model->valor_33 . '</cp33>';
    $xmldata .= '<cp34>' . $model->valor_34 . '</cp34>';
    $xmldata .= '<cp35>' . $model->valor_35 . '</cp35>';
    $xmldata .= '<cp36>' . $model->valor_36 . '</cp36>';
    $xmldata .= '<cp37>' . $model->valor_37 . '</cp37>';
    $xmldata .= '<cp38>' . $model->valor_38 . '</cp38>';
    $xmldata .= '<cp39>' . $model->valor_39 . '</cp39>';
    $xmldata .= '<cp40>' . $model->valor_40 . '</cp40>';
    $xmldata .= '<cp41>' . $model->valor_41 . '</cp41>';
    $xmldata .= '<cp42>' . $model->valor_42 . '</cp42>';
    $xmldata .= '<cp43>' . $model->valor_43 . '</cp43>';
    $xmldata .= '<cp44>' . $model->valor_44 . '</cp44>';
    $xmldata .= '<cp45>' . $model->valor_45 . '</cp45>';
    $xmldata .= '<cp46>' . $model->valor_46 . '</cp46>';
    $xmldata .= '<cp47>' . $model->valor_47 . '</cp47>';
    $xmldata .= '<cp48>' . $model->valor_48 . '</cp48>';
    $xmldata .= '<cp49>' . $model->valor_49 . '</cp49>';
    $xmldata .= '<cp50>' . $model->valor_50 . '</cp50>';
    $xmldata .= '<dt_apresentacao>' . date('Y-m-d') . '</dt_apresentacao>';
    $xmldata .= '<loc_apresentacao>' . Parameter::getValue('CONTABILIDADE', 'COMPANY_LOC_APRESENTACAO') . '
    </loc_apresentacao>';
    $xmldata .= '<nif_toc>' . Parameter::getValue('CONTABILIDADE', 'COMPANY_NIF_TOC') . '</nif_toc>';
    $xmldata .= '<num_ordem_toc>' . Parameter::getValue('CONTABILIDADE', 'COMPANY_NUN_ORDEM_TOC') . '</num_ordem_toc>';
    $xmldata .= '<dt_recepcao>' . date('Y-m-d') . '</dt_recepcao>';
    $xmldata .= '<obs />';
    $xmldata .= '</modelo106>';

$patch = $ano->ano . '_' . $model->mes . '_Modelo106.xml';
if (file_put_contents(Yii::getAlias('@app/web/modelos_fiscais/') . $patch, $xmldata)) {
$this->redirect(Url::to('@web/modelos_fiscais/' . $patch))->send();
}
Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação');

return $this->redirect(Yii::$app->request->referrer);
}



/**
* Deletes an existing Modelo106 model.
* If deletion is successful, the browser will be redirected to the 'index' page.
* @param integer $id
* @return mixed
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionFornecedorXml($cnt_modelo_106_id)
{
$dt_entrega = date('Y-m-d');
$total_fatura = 0;
$total_base_incid = 0;
$total_suportado = 0;
$total_dedutivel = 0;
$model = $this->findModel($cnt_modelo_106_id);
$ano = Ano::findOne($model->ano);
// header('Content-Disposition: attachment; filename="Modelo106AnexoFornecedor.xml"');
$xmldata = '<?xml version="1.0" encoding="utf-8" ?>';
$xmldata .= '<anexo_for xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="https://nosiapps.gov.cv/grexsd/2014/mod106 anexo_for.xsd">';
    $xmldata .= '<header ano="' . $ano->ano . '" mes="' . $model->mes . '"
        cd_af="' . Parameter::getValue('CONTABILIDADE', 'COMPANY_CD_AF') . '"
        nif="' . Parameter::getValue('CONTABILIDADE', 'COMPANY_NIF') . '" />';
    $xmldata .= '<linhas>';
        foreach ($model->modelo106Fornecedor as $modelo106Fornecedor) {
        $total_fatura = $total_fatura + $modelo106Fornecedor['vl_fatura'];
        $total_base_incid = $total_base_incid + $modelo106Fornecedor['vl_base_incid'];
        $total_suportado = $total_suportado + $modelo106Fornecedor['iva_sup'];
        $total_dedutivel = $total_dedutivel + $modelo106Fornecedor['iva_ded'];
        $xmldata .= '
        <linha origem="' . $modelo106Fornecedor['origem'] . '" nif="' . $modelo106Fornecedor['nif'] . '"
            designacao="' . htmlspecialchars($modelo106Fornecedor['designacao']) . '"
            tp_doc="' . $modelo106Fornecedor['tp_doc'] . '" num_doc="' . $modelo106Fornecedor['num_doc'] . '"
            data="' . $modelo106Fornecedor['data'] . '" vl_fatura="' . $modelo106Fornecedor['vl_fatura'] . '"
            vl_base_incid="' . $modelo106Fornecedor['vl_base_incid'] . '"
            tx_iva="' . $modelo106Fornecedor['tx_iva'] . '" iva_sup="' . $modelo106Fornecedor['iva_sup'] . '"
            direito_ded="' . $modelo106Fornecedor['direito_ded'] . '" iva_ded="' . $modelo106Fornecedor['iva_ded'] . '"
            tipologia="' . $modelo106Fornecedor['tipologia'] . '"
            linha_dest_mod="' . $modelo106Fornecedor['linha_dest_mod'] . '" />';
        }
        $xmldata .= '
    </linhas>';
    $xmldata .= '<dt_entrega>' . $dt_entrega . '</dt_entrega>';
    $xmldata .= '<total_fatura>' . $total_fatura . '</total_fatura>';
    $xmldata .= '<total_base_incid>' . $total_base_incid . '</total_base_incid>';
    $xmldata .= '<total_suportado>' . $total_suportado . '</total_suportado>';
    $xmldata .= '<total_dedutivel>' . $total_dedutivel . '</total_dedutivel>';
    $xmldata .= '
</anexo_for>';

// print_r($xmldata);die();
$patch = $ano->ano . '_' . $model->mes . '_Modelo106AnexoFornecedor.xml';
if (file_put_contents(Yii::getAlias('@app/web/modelos_fiscais/') . $patch, $xmldata)) {
$this->redirect(Url::to('@web/modelos_fiscais/' . $patch))->send();
} else {
Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação');
}
return $this->redirect(Yii::$app->request->referrer);
}




/**
* Deletes an existing Modelo106 model.
* If deletion is successful, the browser will be redirected to the 'index' page.
* @param integer $id
* @return mixed
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionClienteXml($cnt_modelo_106_id)
{
// \Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
$model = $this->findModel($cnt_modelo_106_id);
$ano = Ano::findOne($model->ano);
$dt_entrega = date('Y-m-d');
$total_fatura = 0;
$total_base_incid = 0;
$total_liquidado = 0;

$xmldata = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
$xmldata .= '<anexo_cli xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://nosiapps.gov.cv/grexsd/2014/mod106 anexo_cli.xsd">';
    $xmldata .= '<header ano="' . $ano->ano . '" mes="' . $model->mes . '" cd_af="' . Parameter::getValue('CONTABILIDADE', 'COMPANY_CD_AF') . '" nif="' . Parameter::getValue('CONTABILIDADE', 'COMPANY_NIF') . '" />';
    $xmldata .= '<linhas>';
        foreach ($model->modelo106Cliente as $modelo106Cliente) {
        $total_fatura = $total_fatura + $modelo106Cliente['vl_fatura'];
        $total_base_incid = $total_base_incid + $modelo106Cliente['vl_base_incid'];
        $total_liquidado = $total_liquidado + $modelo106Cliente['iva_liq'];
        $xmldata .= '<linha origem="' . $modelo106Cliente['origem'] . '" nif="' . $modelo106Cliente['nif'] . '" designacao="' . htmlspecialchars($modelo106Cliente['designacao']) . '" serie="01" tp_doc="' . $modelo106Cliente['tp_doc'] . '" num_doc="' . $modelo106Cliente['num_doc'] . '" data="' . $modelo106Cliente['data'] . '" vl_fatura="' . $modelo106Cliente['vl_fatura'] . '" vl_base_incid="' . $modelo106Cliente['vl_base_incid'] . '" tx_iva="' . $modelo106Cliente['tx_iva'] . '" iva_liq="' . $modelo106Cliente['iva_liq'] . '" nao_liq_imp="' . $modelo106Cliente['nao_liq_imp'] . '" linha_dest_mod="' . $modelo106Cliente['linha_dest_mod'] . '" />';
        }
        $xmldata .= '</linhas>';
    $xmldata .= '<dt_entrega>' . $dt_entrega . '</dt_entrega>';
    $xmldata .= '<total_fatura>' . $total_fatura . '</total_fatura>';
    $xmldata .= '<total_base_incid>' . $total_base_incid . '</total_base_incid>';
    $xmldata .= '<total_liquidado>' . $total_liquidado . '</total_liquidado>';
    $xmldata .= '</anexo_cli>';
$patch = $ano->ano . '_' . $model->mes . '_Modelo106AnexoCliente.xml';
if (file_put_contents(Yii::getAlias('@app/web/modelos_fiscais/') . $patch, $xmldata)) {
$this->redirect(Url::to('@web/modelos_fiscais/' . $patch))->send();
} else {
Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação');
}
return $this->redirect(Yii::$app->request->referrer);
}


/**
* Renders the index view for the module
* @return string
*/
public function actionClientePdf($cnt_modelo_106_id)
{
// $model = $this->findModel($cnt_modelo_106_id);
// print_r($model->id);die();
$output = Yii::$app->iReport->generate([
'name' => 'Modelo106AnexoCliente',
'format' => 'pdf',
'parametre' => [
'ID' => (int)$cnt_modelo_106_id,
],
]);

return Yii::$app->response->sendFile($output, 'Modelo106AnexoCliente.pdf');
}


/**
* Renders the index view for the module
* @return string
*/
public function actionFornecedorPdf($cnt_modelo_106_id)
{
// $model = $this->findModel($cnt_modelo_106_id);
// print_r($model->id);die();
$output = Yii::$app->iReport->generate([
'name' => 'Modelo106AnexoFornecedor',
'format' => 'pdf',
'parametre' => [
'ID' => (int)$cnt_modelo_106_id,
],
]);

return Yii::$app->response->sendFile($output, 'Modelo106AnexoFornecedor.pdf');
}



/**
* Renders the index view for the module
* @return string
*/
public function actionModelo106Pdf($cnt_modelo_106_id)
{
// $model = $this->findModel($cnt_modelo_106_id);
$output = Yii::$app->iReport->generate([
'name' => 'Modelo106',
'format' => 'pdf',
'parametre' => [
'ID' => (int)$cnt_modelo_106_id,
],
]);

return Yii::$app->response->sendFile($output, 'Modelo106.pdf');
}

/**
* Updates an existing PlanoConta model.
* If update is successful, the browser will be redirected to the 'view' page.
* @param integer $id
* @return mixed
* @throws NotFoundHttpException if the model cannot be found
*/
public function actionModelo106Update($cnt_modelo_106_id)
{
$model = $this->findModel($cnt_modelo_106_id);
if ($model->load(Yii::$app->request->post()) && $model->save()) {
return $this->redirect(['view', 'id' => $model->id]);
}

return $this->render('_form_update_modelo106', [
'model' => $model,
]);
}


/**
* Finds the Modelo106 model based on its primary key value.
* If the model is not found, a 404 HTTP exception will be thrown.
* @param integer $id
* @return Modelo106 the loaded model
* @throws NotFoundHttpException if the model cannot be found
*/
protected function findModel($id)
{
if (($model = Modelo106::findOne(['id' => $id])) !== null) {
return $model;
}

throw new NotFoundHttpException('The requested page does not exist.');
}




/**
* Finds the Modelo106 model based on its primary key value.
* If the model is not found, a 404 HTTP exception will be thrown.
* @param integer $id
* @return Modelo106 the loaded model
* @throws NotFoundHttpException if the model cannot be found
*/
protected function findModelAndUpdate($id)
{
if (($model = Modelo106::findOne(['id' => $id])) !== null) {
$model->valor_1 = Yii::$app->CntQuery->getValorBTCliente($model->id, 1);
$model->valor_2 = Yii::$app->CntQuery->getValorIFECliente($model->id, 1);
$model->valor_3 = Yii::$app->CntQuery->getValorBTCliente($model->id, 3);
$model->valor_4 = Yii::$app->CntQuery->getValorIFECliente($model->id, 3);
$model->valor_5 = Yii::$app->CntQuery->getValorBTCliente($model->id, 5);
$model->valor_6 = Yii::$app->CntQuery->getValorIFECliente($model->id, 6);
$model->valor_7 = Yii::$app->CntQuery->getValorBTCliente($model->id, 7);
$model->valor_8 = Yii::$app->CntQuery->getValorBTCliente($model->id, 8);
$model->valor_9 = Yii::$app->CntQuery->getValorBTCliente($model->id, 9);
$model->valor_10 = Yii::$app->CntQuery->getValorBTCliente($model->id, 10);

$model->valor_11 = Yii::$app->CntQuery->getValorBTFornecedor($model->id, 11);
$model->valor_12 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 11);
$model->valor_13 = Yii::$app->CntQuery->getValorIFEFornecedor($model->id, 11);
$model->valor_14 = Yii::$app->CntQuery->getValorBTFornecedor($model->id, 14);
$model->valor_15 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 14);
$model->valor_16 = Yii::$app->CntQuery->getValorIFEFornecedor($model->id, 14);
$model->valor_17 = Yii::$app->CntQuery->getValorBTFornecedor($model->id, 17);
$model->valor_18 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 17);
$model->valor_19 = Yii::$app->CntQuery->getValorBTFornecedor($model->id, 19);
$model->valor_20 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 12);

$model->valor_21 = Yii::$app->CntQuery->getValorBTFornecedor($model->id, 21);
$model->valor_22 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 21);
$model->valor_23 = Yii::$app->CntQuery->getValorBTFornecedor($model->id, 23);
$model->valor_24 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 23);
$model->valor_25 = Yii::$app->CntQuery->getValorBTFornecedor($model->id, 25);
$model->valor_26 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 28);
$model->valor_27 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 27);
$model->valor_28 = Yii::$app->CntQuery->getValorIFEFornecedor($model->id, 27);
$model->valor_29 = Yii::$app->CntQuery->getValorIFSPFornecedor($model->id, 29);
$model->valor_30 = Yii::$app->CntQuery->getValorIFEFornecedor($model->id, 29);

$model->valor_31 = 0;
$model->valor_32 =
($model->valor_1 + $model->valor_3 + $model->valor_5 + $model->valor_7 + $model->valor_8 + $model->valor_9 +
$model->valor_10 + $model->valor_11 + $model->valor_14 + $model->valor_17 + $model->valor_19 + $model->valor_21 +
$model->valor_23 + $model->valor_25);
$model->valor_33 =
($model->valor_12 + $model->valor_15 + $model->valor_18 + $model->valor_20 + $model->valor_22 + $model->valor_24 +
$model->valor_26 + $model->valor_27 + $model->valor_29);
$model->valor_34 =
($model->valor_2 + $model->valor_4 + $model->valor_6 + $model->valor_13 + $model->valor_16 + $model->valor_28 +
$model->valor_30);
$model->valor_35 = ($model->valor_34 > $model->valor_33) ? ($model->valor_34 - $model->valor_33) : 0;
$model->valor_36 = ($model->valor_33 > $model->valor_34) ? ($model->valor_33 - $model->valor_34) : 0;
$model->valor_37 = 0;
$model->valor_38 = ($model->valor_35 - $model->valor_37);
$model->valor_39 = ($model->valor_36 + $model->valor_37);
$model->valor_40 = 0;

$model->valor_41 = 0;
$model->valor_42 = 0;
$model->valor_43 = 0;
$model->valor_44 = 0;
$model->valor_45 = 0;
$model->valor_46 = 0;
$model->valor_47 = 0;
$model->valor_48 = $model->valor_8;
$model->valor_49 = 0;
$model->valor_50 = 0;
if ($model->save()) {
return $model;
} else {
// print_r($model->errors);die();
$this->render('update', [
'model' => $model,
]);
throw new NotFoundHttpException('Houve um erro ao executar esta operação.');
}
}

throw new NotFoundHttpException('The requested page does not exist.');
}
}
