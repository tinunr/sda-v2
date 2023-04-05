<?php

namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use app\components\helpers\UploadFileHelper;
use app\models\Parameter;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\ProcessoObs;
use app\modules\dsp\models\ProcessoSearch;
use app\modules\dsp\models\ProcessoHistorico;
use app\modules\dsp\models\ProcessoDespachoDocumento;
use app\modules\dsp\models\DespachoDocumento;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\ProcessoWorkflow;
use app\modules\dsp\services\ProcessoService;
use app\modules\dsp\models\ProcessoTce;
use app\modules\dsp\models\ProcessoTarefa;
use app\modules\dsp\models\FaturaClassificada;
use app\modules\dsp\models\Tarefa;
use app\modules\dsp\models\ProcessoDespesaManual;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\models\Ficheiro;


/**
 * ProcessoController implements the CRUD actions for Processo model.
 */
class ProcessoController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['update-receita', 'list-processo-fd', 'file-browser', 'dropdow-list'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['ajax', 'index', 'view', 'create', 'update', 'delete', 'update-status', 'create-obs', 'obs-status', 'relatorio', 'report-pdf', 'anular-processo'],
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
                    'delete' => ['post'],
                    'update-status' => ['post'],
                    'anular-processo' => ['post'],
                    'update-receita' => ['post'],
                    'file-browser' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Lists all Processo models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->session->remove('urlIndex');
        $searchModel = new ProcessoSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Processo models.
     * @return mixed
     */
    public function actionRelatorio()
    {
        $searchModel = new ProcessoSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->relatorio(Yii::$app->request->queryParams);

        return $this->render('relatorio', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    /**
     * Displays a single Processo model.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionView($id)
    {
        if (empty(Yii::$app->session->has('urlIndex'))) {
            Yii::$app->session->open();
            Yii::$app->session->set('urlIndex', Yii::$app->request->referrer);
        }
        UploadFileHelper::setUrlFile($id);
        $model = $this->findModel($id);
        $workflow = \app\modules\dsp\models\ProcessoWorkflow::find()->where(['dsp_processo_id' => $model->id])->orderBy(['id' => SORT_DESC])->one();
        $model->user_id = !empty($workflow->user_id) ? $workflow->user_id : null;
        $model->dsp_setor_id = !empty($workflow->dsp_setor_id) ? $workflow->dsp_setor_id : 10;
        $model->save();
        $modelFicheiro = new ProcessoDespachoDocumento();
        $modelTarefa = new ProcessoTarefa();
        $modelObs = new ProcessoObs();
        $workflow = new ProcessoWorkflow();
        $faturaClassificada = new FaturaClassificada();
        $despesaManual = new ProcessoDespesaManual();
        $tceCount = ProcessoTce::find()->where(['dsp_processo_id' => $model->id])->count();


        return $this->render('view', [
            'model' => $model,
            'modelFicheiro' => $modelFicheiro,
            'modelTarefa' => $modelTarefa,
            'modelObs' => $modelObs,
            'workflow' => $workflow,
            'faturaClassificada' => $faturaClassificada,
            'despesaManual' => $despesaManual,
            'urlIndex' => Yii::$app->session->get('urlIndex'),
            'tceCount' => $tceCount,

        ]);
    }

    /**
     * Creates a new Processo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new Processo();
        $modelCliente = new Person();
        if ($modelCliente->load(Yii::$app->request->post()) && $modelCliente->save()) {
            $model->dsp_person_id = $modelCliente->id;
            $model->nome_fatura = $modelCliente->id;
        }
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::PROCESSO_ID);
        //print_r($documentoNumero);die();
        $model->status = 1;
        $model->fin_currency_id = 1;
        $model->data = date('Y-m-d');
        $model->bas_ano_id = substr(date('Y'), -2);
        $model->numero = $documentoNumero->getNexNumber();
        $model->user_id = Parameter::getValue('PROCESSO', 'WORKFLOW_USER_DEFAULT');
        $model->dsp_setor_id = Parameter::getValue('PROCESSO', 'WORKFLOW_SETOR_DEFAULT');

        if ($model->load(Yii::$app->request->post())) {
            $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);
            // print_r($model);die();
            if ($model->save()) {
                if (!empty($model->despacho_documento)) {
                    foreach ($model->despacho_documento as $key => $despacho_documento) {
                        $new = new ProcessoDespachoDocumento();
                        $new->dsp_processo_id = $model->id;
                        $new->dsp_despacho_documento_id = $despacho_documento;
                        $new->descricao = DespachoDocumento::findOne($despacho_documento)->descricao;
                        $new->save();
                    }
                }
                if (!empty($model->processo_tce)) {
                    foreach ($model->processo_tce as $key => $tce) {
                        $new = new ProcessoTce();
                        $new->dsp_processo_id = $model->id;
                        $new->tce = $tce;
                        $new->save();
                    }
                }
                if (!empty($model->processo_tarefa)) {
                    foreach ($model->processo_tarefa as $key => $tarefa) {
                        $new = new ProcessoTarefa();
                        $new->dsp_processo_id = $model->id;
                        $new->dsp_tarefa_id = $tarefa;
                        $new->descricao = Tarefa::findOne($tarefa)->descricao;
                        $new->save();
                    }
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'modelCliente' => $modelCliente
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'modelCliente' => $modelCliente
            ]);
        }
    }
    /**
     * Creates a new Processo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateObs($id)
    {

        $model = new ProcessoObs();
        $model->dsp_processo_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save() && \app\modules\dsp\services\DspService::updateProcessoStatus($id)) {
            ProcessoService::setStatusFinanceiro($model->id);

            Yii::$app->getSession()->setFlash('success', 'Registo atualizado com sucesso');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Creates a new Processo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionObsStatus($id, $dsp_processo_id)
    {

        $model =  ProcessoObs::findOne(['id' => $id]);
        $model->status = 1;

        if ($model->save()) {
            $modelProcesso =  Processo::findOne(['id' => $model->dsp_processo_id]);
            $modelProcesso->status = 1;
            $modelProcesso->save();

            $processoHistorico = new ProcessoHistorico();
            $processoHistorico->dsp_processo_id = $modelProcesso->id;
            $processoHistorico->descricao = 'Observação ' . $model->id . ' marcado como resolvido';
            $processoHistorico->save();

            ProcessoService::setStatusFinanceiro($model->id);

            Yii::$app->getSession()->setFlash('success', 'Registo atualizado com sucesso');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Updates an existing Processo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $readonly = \app\modules\dsp\services\DspService::checkFaturaProvisoriaSend($id);
        $model = $this->findModel($id);
        $model->processo_tce = ArrayHelper::map(
            ProcessoTce::find()->where(['dsp_processo_id' => $id])->all(),
            'tce',
            'tce'
        );
        $modelCliente = new Person();
        if ($modelCliente->load(Yii::$app->request->post()) && $modelCliente->save()) {
            $model->dsp_person_id = $modelCliente->id;
            $model->nome_fatura = $modelCliente->id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ProcessoTce::deleteAll(['dsp_processo_id' => $id]);
            if (!empty($model->processo_tce)) {
                foreach ($model->processo_tce as $key => $tce) {
                    $new = new ProcessoTce();
                    $new->dsp_processo_id = $model->id;
                    $new->tce = $tce;
                    $new->save();
                }
            }
            if (!$readonly) {
                ProcessoService::alterarProcessoNomeFatura($model->id, $model->nome_fatura);
            }
            ProcessoService::setStatusFinanceiro($model->id);



            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelCliente' => $modelCliente,
                'readonly' => $readonly,
            ]);
        }
    }

    /**
     * Updates an existing Processo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        if ($model->status != ProcessoStatus::PARCEALMENTE_CONCLUIDO) {
            $model->status = ProcessoStatus::PARCEALMENTE_CONCLUIDO;
            $model->data_conclucao = new \yii\db\Expression('NOW()');
        } else {
            $model->status = 1;
            unset($model->data_conclucao);
        }
        if ($model->save()) {
            $processoHistorico = new ProcessoHistorico();
            $processoHistorico->dsp_processo_id = $model->id;
            $processoHistorico->descricao = 'Processo ' . $model->numero . ' atualizado para estado ' . $model->processoStatus->descricao;
            $processoHistorico->save();
            ProcessoService::setStatusFinanceiro($model->id);

            Yii::$app->getSession()->setFlash('success', 'Estado do processo alterado com sucesso.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Erro ao efetuar a operação');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing Processo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $perfil_id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete();
        // $processoHistorico = new ProcessoHistorico();
        // $processoHistorico->dsp_processo_id = $model->id;
        // $processoHistorico->descricao = 'Processo ' . $model->numero . ' eliminado';
        // $processoHistorico->save();
        // return $this->redirect(['index']);
    }



    /**
     * Finds the Processo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $perfil_id
     * @return Processo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Processo::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Finds the Processo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $perfil_id
     * @return Processo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAnularProcesso($id)
    {
        $model = $this->findModel($id);
        $fp = \app\modules\fin\models\FaturaProvisoria::find()->where(['status' => 1, 'dsp_processo_id' => $id])->count();
        $fd = \app\modules\fin\models\FaturaDefinitiva::find()->where(['status' => 1, 'dsp_processo_id' => $id])->count();
        $dsp = \app\modules\fin\models\Despesa::find()->where(['status' => 1, 'dsp_processo_id' => $id])->count();
        $ntc = \app\modules\fin\models\NotaCredito::find()->where(['status' => 1, 'dsp_processo_id' => $id])->count();
        if (($fp + $fd + $dsp + $ntc) > 0) {
            Yii::$app->getSession()->setFlash('warning', 'Este processo tem FP / FD / DESPESA / AVISO DE CREDITO, associado não pode ser anulado.');
            return $this->redirect(Yii::$app->request->referrer);
        } else {

            $model->status = ProcessoStatus::STATUS_ANULADO;
            $model->save();

            Yii::$app->getSession()->setFlash('success', 'Processo anulado com sucesso.');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }


    /**
     * Finds the Nord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Nord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateReceita($id)
    {
        $model = $this->findModel($id);
        $faturaPR  = $model->faturaProvisorio;
        foreach ($faturaPR as $fp) {
            $ok = Yii::$app->FinAutoFixs->bugApdateValorRecebido($fp->id);
        }

        if ($ok) {
            Yii::$app->getSession()->setFlash('success', 'OPERAÇÃO EFETUADO COM SUCESSO');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'ERRO AO EFETUAR A OPERAÇÃO.');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }




    public function actionReportPdf($bas_ano_id, array $status = null, $dsp_person_id = null, $user_id = null)
    {
        $query = new Query;
        $users = $query->select(['B.id', 'B.name'])
            ->from('dsp_processo A')
            ->join('LEFT JOIN', 'user B', 'B.id = A.user_id')
            ->orderBy('B.name')
            ->groupBy(['B.id', 'B.name'])
            ->where(['A.bas_ano_id' => $bas_ano_id])
            ->filterWhere(['A.user_id' => $user_id])
            ->andFilterWhere(['A.status' => $status])
            ->andFilterWhere(['A.dsp_person_id' => $dsp_person_id])
            ->all();



        $company = Yii::$app->params['company'];




        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('pdf-report', [
            'users' => $users,
            'status' => $status,
            'dsp_person_id' => $dsp_person_id,
            'bas_ano_id' => $bas_ano_id,
            'company' => $company,
        ]);
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'marginTop' => 50,
            'marginLeft' => 25,
            'marginRight' => 25,
            'options' => ['title' => 'Relatório Processo'],
            'methods' => [
                'SetHeader' => ['
              <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                  <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="' . $company['logo'] . '" height="60" width="150" />
                  </p>
                  <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> ' . $company['name'] . ' </strong></p>
                  <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>' . $company['adress1'] . '</strong></p>   
                    <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>Relatórios dos Processos</strong></p>
              </div>
              '],
                'SetFooter' => ['<p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong><i class="fa fa-graduation-cap"></i>' . $company['adress2'] . '</strong>
                     <br><strong><i class="fa fa-graduation-cap"></i> C.P. Nº ' . $company['cp'] . ' - Tel.: ' . $company['teletone'] . ' - FAX: ' . $company['fax'] . ' - Praia, Santiago</strong>
                     </p> ||Página {PAGENO}/{nbpg}'],
            ]
        ]);
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        // return the pdf output as per the destination setting
        return $pdf->render();
    }


    // THE CONTROLLER
    public function actionListProcessoFd($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {

            $subQuery = new Query;
            $subQuery->select(['A.fin_fatura_provisoria_id'])
                ->from('fin_fatura_definitiva_provisoria A')
                ->leftJoin('fin_fatura_provisoria B', 'B.id = A.fin_fatura_provisoria_id')
                ->leftJoin('fin_fatura_definitiva C', 'C.id = A.fin_fatura_definitiva_id')
                ->where(['B.status' => 1])
                ->andWhere(['C.status' => 1]);

            $query = new Query;
            $query->select(['B.id as id', new \yii\db\Expression("CONCAT(B.numero, '/', B.bas_ano_id) as text")])
                ->from('fin_fatura_provisoria A')
                ->leftJoin('dsp_processo B', 'B.id = A.dsp_processo_id')
                ->where(['A.status' => 1])
                ->andWhere(['not in', 'A.id', $subQuery])
                ->andWhere(['like', 'B.numero', $q . '%', false])
                ->orderBy('B.numero', 'B.bas_ano_id')
                ->all();
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out = ['results' => ['id' => '', 'text' => '']];
        }
        return $out;
    }


    public function actionFileBrowser($dsp_processo_id)
    {
        $model = new \app\models\FileBrowser();
        $model->path = \app\components\helpers\UploadFileHelper::setUrlFile($dsp_processo_id);

        if (Yii::$app->request->isPost) {
            $model->files = \yii\web\UploadedFile::getInstances($model, 'files');
            if ($model->upload()) {
                Yii::$app->getSession()->setFlash('success', 'O ficheiro foi caregado com sucesso!');
            } else {
                Yii::$app->getSession()->setFlash('error', 'Ocoreu um erro!');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDropdowList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(['id', 'text' => new \yii\db\Expression("CONCAT(`numero`, '/', `bas_ano_id`)")])
                ->from('dsp_processo')
                ->where(['numero' => $q])
                ->orderBy('bas_ano_id DESC')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $processo =  Processo::find($id);
            $out['results'] = ['id' => $id, 'text' => $processo->numero.'/'.$processo->bas_ano_id];
        }
        return $out;
    }
}
