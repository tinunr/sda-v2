<?php

namespace app\modules\dsp\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\db\ActiveQuery;
use kartik\mpdf\Pdf;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\ProcessoSearch;
use yii\db\Query;

/**
 * Default controller for the `employee` module
 */
class ReportController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'report-pdf', 'processo', 'processo-pdf', 'pr-liquidado', 'pr-liquidado-pdf', 'pr-registrado', 'pr-registrado-pdf', 'pr-pl-por-resolver', 'pr-pl-por-resolver-pdf'],
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
                ],
            ],
        ];
    }


    /**
     * Lists all Processo models.
     * @return mixed
     */
    public function actionProcesso()
    {
        $searchModel = new ProcessoSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('processo', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProcessoSearch();
        $searchModel->bas_ano_id = substr(date('Y'), -2);
        $data =
            $searchModel->dataInicio = '20' . $searchModel->bas_ano_id . '-01-01';
        $searchModel->dataFim = date('Y-m-d');

        return $this->render('_search', [
            'model' => $searchModel,
        ]);
    }



    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionReportPdf()
    {
        $company = Yii::$app->params['company'];
        $data = Yii::$app->request->queryParams;
        $params = $data['ProcessoSearch'];
        $status = [];


        $query = new Query;
        $query->select(['B.id', 'B.name'])
            ->from('dsp_processo A')
            ->join('LEFT JOIN', 'user B', 'B.id = A.user_id')
            ->orderBy('B.name')
            ->groupBy(['B.id', 'B.name'])
            ->where(['A.bas_ano_id' => $params['bas_ano_id']])
            ->filterWhere(['A.user_id' => $params['user_id']])
            ->andFilterWhere(['A.status' => $params['status']])
            ->andFilterWhere(['A.dsp_person_id' => $params['dsp_person_id']])
            ->andFilterWhere(['>=', 'A.data', $params['dataInicio']])
            ->andFilterWhere(['<=', 'A.data', $params['dataFim']]);
        if ($params['comPl'] == 0) {
            $query->andFilterWhere(['>', 'A.n_levantamento', $params['comPl']]);
        }
        if ($params['comPl'] == 1) {
            $query->andFilterWhere(['>', 'A.n_levantamento', $params['comPl']])
                ->andFilterWhere(['A.status' => [1, 2, 3, 4, 5, 7]]);
        }
        if ($params['comPl'] == 2) {
            $query->andFilterWhere(['>', 'A.n_levantamento', $params['comPl']])
                ->andFilterWhere(['A.status' => [6, 8, 9]]);
        }
        $users = $query->all();

        //print_r($params);die();

        if ($params['porResponsavel']) {

            // get your HTML raw content without any layouts or scripts
            $content = $this->renderPartial('pdf-porResponsavel', [
                'users' => $users,
                'status' => $params['status'] ? $params['status'] : $status,
                'dsp_person_id' => $params['dsp_person_id'],
                'bas_ano_id' => $params['bas_ano_id'],
                'dataInicio' => $params['dataInicio'],
                'dataFim' => $params['dataFim'],
                'company' => $company,
                'comPl' => $params['comPl'],

            ]);
        } elseif ($params['porCliente']) {

            $query2 = new Query;
            $query2->select(['B.id', 'B.nome'])
                ->from('dsp_processo A')
                ->join('LEFT JOIN', 'dsp_person B', 'B.id = A.dsp_person_id')
                ->orderBy('B.nome')
                ->groupBy(['B.id', 'B.nome'])
                ->where(['A.bas_ano_id' => $params['bas_ano_id']])
                ->filterWhere(['A.user_id' => $params['user_id']])
                ->andFilterWhere(['A.status' => $params['status']])
                ->andFilterWhere(['A.dsp_person_id' => $params['dsp_person_id']])
                ->andFilterWhere(['>=', 'A.data', $params['dataInicio']])
                ->andFilterWhere(['<=', 'A.data', $params['dataFim']]);
            if ($params['comPl'] == 0) {
                $query2->andFilterWhere(['>', 'A.n_levantamento', $params['comPl']]);
            }
            if ($params['comPl'] == 1) {
                $query2->andFilterWhere(['>', 'A.n_levantamento', $params['comPl']])
                    ->andFilterWhere(['A.status' => [1, 2, 3, 4, 5, 7]]);
            }
            if ($params['comPl'] == 2) {
                $query2->andFilterWhere(['>', 'A.n_levantamento', $params['comPl']])
                    ->andFilterWhere(['A.status' => [6, 8, 9]]);
            }
            $clientes = $query2->all();

            $content = $this->renderPartial('pdf-porCliente', [
                'user_id' => $params['user_id'],
                'status' => $params['status'] ? $params['status'] : $status,
                'dsp_person_id' => $params['dsp_person_id'],
                'bas_ano_id' => $params['bas_ano_id'],
                'dataInicio' => $params['dataInicio'],
                'dataFim' => $params['dataFim'],
                'company' => $company,
                'clientes' => $clientes,
                'comPl' => $params['comPl'],
            ]);
        } else {
            $content = $this->renderPartial('pdf-all', [
                'user_id' => $params['user_id'],
                'status' => $params['status'] ? $params['status'] : $status,
                'dsp_person_id' => $params['dsp_person_id'],
                'bas_ano_id' => $params['bas_ano_id'],
                'dataInicio' => $params['dataInicio'],
                'dataFim' => $params['dataFim'],
                'company' => $company,
                'comPl' => $params['comPl'],

            ]);
        }
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'marginTop' => 50,
            'marginLeft' => 5,
            'marginRight' => 5,
            // 'cssFile'=>'@app/web/css/site.css',
            'cssInline'=>'td,th{ font-size: 12px;border-bottom: 1px solid #ddd;}',
            'options' => ['title' => 'Relatório Processo'],
            'methods' => [
                'SetHeader' => ['
              <div class="pdf-header" style="height: 60px; padding: 5px 0;">
                  <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="' . $company['logo'] . '" height="60" width="150" />
                  </p>
                  <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> ' . $company['name'] . ' </strong></p>
                  <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>' . $company['adress1'] . '</strong></p>   
                    <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>Relatórios dos Processos</strong></p>
              </div>
              '],
                'SetFooter' => ['<p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong><i class="fa fa-graduation-cap"></i>' . $company['adress2'] . '</strong>
                     <br><strong><i class="fa fa-graduation-cap"></i> C.P. Nº ' . $company['cp'] . ' - Tel.: ' . $company['teletone'] . ' - FAX: ' . $company['fax'] . ' - Praia, Santiago</strong>
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





    /**
     * Lists all Receita models.
     * @return mixed
     */
    public function actionProcessoPdf()
    {
        $company = Yii::$app->params['company'];
        $titleReport = 'Lista de Processos';
        $data = Yii::$app->request->queryParams;
        if ($data['ProcessoSearch']['porResponsavel']) {
            $searchModel = new ProcessoSearch($data['ProcessoSearch']);
            $dataProvider = $searchModel->responsavel(Yii::$app->request->queryParams);
            $content = $this->renderPartial('processo-responsavel', [
                'responsavel' => $dataProvider,
                'data' => $data,

            ]);
        } elseif ($data['ProcessoSearch']['porCliente']) {
            $searchModel = new ProcessoSearch($data['ProcessoSearch']);
            $dataProvider = $searchModel->person(Yii::$app->request->queryParams);
            $content = $this->renderPartial('processo-person', [
                'person' => $dataProvider,
                'data' => $data,

            ]);
        } else {
            $searchModel = new ProcessoSearch($data['ProcessoSearch']);
            $dataProvider = $searchModel->processo(Yii::$app->request->queryParams);
            $content = $this->renderPartial('processo-all', [
                'dataProvider' => $dataProvider,
                'data' => $data,
            ]);
        }

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'marginTop' => 50,
            'marginLeft' => 5,
            'marginRight' => 5,
            'options' => ['title' => 'RelatÃ³rio Processo'],
            'methods' => [
                'SetHeader' => ['
               <div class="pdf-header" style="height: 60px; padding: 10px 0;">
                   <p align="center"><img style="text-decoration: none; display: block; margin-left: auto; margin-right: auto;>" src="' . $company['logo'] . '" height="60" width="150" />
                   </p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 14px;"><strong><i class="fa fa-graduation-cap"></i> ' . $company['name'] . ' </strong></p>
                   <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>' . $company['adress1'] . '</strong></p>   
                     <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 12px;"><strong><i class="fa fa-graduation-cap"></i>' . $titleReport . '</strong></p>
               </div>
               '],
                'SetFooter' => ['<p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong><i class="fa fa-graduation-cap"></i>' . $company['adress2'] . '</strong>
                      <br><strong><i class="fa fa-graduation-cap"></i> C.P. NÂº ' . $company['cp'] . ' - Tel.: ' . $company['teletone'] . ' - FAX: ' . $company['fax'] . ' - Praia, Santiago</strong>
                      </p> |PÃ¡gina {PAGENO}/{nbpg}|Emetido em: ' . date("Y-m-d H:i:s")],
            ]
        ]);
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        return $pdf->render();
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionPrLiquidado()
    {
        $searchModel = new ProcessoSearch();
        //  $searchModel->bas_ano_id = substr(date('Y'),-2);
        //  $searchModel->dataInicio = '20'.$searchModel->bas_ano_id.'-01-01';
        //  $searchModel->dataFim = date('Y-m-d');
        $searchModel->bas_formato_id = 1;
        $searchModel->status = 5;

        return $this->render('_searchPrLiquidado', [
            'model' => $searchModel,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionPrLiquidadoPdf()
    {
        $company = Yii::$app->params['company'];
        $data = Yii::$app->request->queryParams;
        $params = $data['ProcessoSearch'];

        $pdf = Yii::$app->pdf;
        $pdf->mode = Pdf::MODE_UTF8;
        $pdf->format = Pdf::FORMAT_A4;
        $pdf->orientation = Pdf::ORIENT_LANDSCAPE;
        $pdf->destination = Pdf::DEST_BROWSER;
        $pdf->marginTop = 25;
        $pdf->marginLeft = 5;
        $pdf->marginRight = 5;
        $pdf->marginBottom = 15;
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->defaultheaderline = 0;
        $mpdf->defaultfooterline = 0;
        $stylesheet = file_get_contents(Url::to('@app/web/css/pdf.css'));
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        $mpdf->SetHeader(\yii\helpers\Html::img(\yii\helpers\Url::to('@web/' . $company['logo']), ['width' => 60]) . '|PROCESSOS LIQUIDADOS |Gerado em: ' . date('Y-m-d h:i'));
        $mpdf->SetFooter('<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'); // call methods or set any properties

        $html = \app\modules\dsp\widget\ProcessoLiquidado::Widget([
            'status' => [5],
            'bas_ano_id' => $params['bas_ano_id'],
            'dataInicio' => $params['dataInicio'],
            'dsp_desembaraco_id' => $params['dsp_desembaraco_id'],
        ]);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        return $pdf->render();
    }




    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionPrRegistrado()
    {
        $searchModel = new ProcessoSearch();
        //  $searchModel->bas_ano_id = substr(date('Y'),-2);
        //  $searchModel->dataInicio = '20'.$searchModel->bas_ano_id.'-01-01';
        //  $searchModel->dataFim = date('Y-m-d');
        $searchModel->bas_formato_id = 1;
        $searchModel->status = 5;

        return $this->render('_searchPrRegistrado', [
            'model' => $searchModel,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionPrRegistradoPdf()
    {
        $company = Yii::$app->params['company'];
        $data = Yii::$app->request->queryParams;
        $params = $data['ProcessoSearch'];

        $pdf = Yii::$app->pdf;
        $pdf->mode = Pdf::MODE_UTF8;
        $pdf->format = Pdf::FORMAT_A4;
        $pdf->orientation = Pdf::ORIENT_LANDSCAPE;
        $pdf->destination = Pdf::DEST_BROWSER;
        $pdf->marginTop = 25;
        $pdf->marginLeft = 5;
        $pdf->marginRight = 5;
        $pdf->marginBottom = 15;
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->defaultheaderline = 0;
        $mpdf->defaultfooterline = 0;
        $stylesheet = file_get_contents(Url::to('@app/web/css/pdf.css'));
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        $mpdf->SetHeader(\yii\helpers\Html::img(\yii\helpers\Url::to('@web/' . $company['logo']), ['width' => 60]) . '|PROCESSOS REGISTRADO |Gerado em: ' . date('Y-m-d h:i'));
        $mpdf->SetFooter('<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'); // call methods or set any properties
        $html = \app\modules\dsp\widget\ProcessoRegistrado::Widget([
            'status' => [4],
            'bas_ano_id' => $params['bas_ano_id'],
            'dataInicio' => $params['dataInicio'],
            'dataFim' => $params['dataFim'],
            'dsp_desembaraco_id' => $params['dsp_desembaraco_id'],

        ]);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);


        return $pdf->render();
    }



    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionPrPlPorResolver()
    {
        $searchModel = new ProcessoSearch();
        //  $searchModel->bas_ano_id = substr(date('Y'),-2);
        //  $searchModel->dataInicio = '20'.$searchModel->bas_ano_id.'-01-01';
        //  $searchModel->dataFim = date('Y-m-d');
        $searchModel->bas_formato_id = 1;
        $searchModel->status = 5;

        return $this->render('_searchPrPlPorResolver', [
            'model' => $searchModel,
        ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionPrPlPorResolverPdf()
    {
        $searchModel = new ProcessoSearch();
        $dataProvider = $searchModel->searchProcessoPlPorResover(Yii::$app->request->queryParams);

        $company = Yii::$app->params['company'];
        $data = Yii::$app->request->queryParams;
        $params = $data['ProcessoSearch'];

        $pdf = Yii::$app->pdf;
        $pdf->mode = Pdf::MODE_UTF8;
        $pdf->format = Pdf::FORMAT_A4;
        $pdf->orientation = Pdf::ORIENT_LANDSCAPE;
        $pdf->destination = Pdf::DEST_BROWSER;
        $pdf->marginTop = 25;
        $pdf->marginLeft = 5;
        $pdf->marginRight = 5;
        $pdf->marginBottom = 15;
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->defaultheaderline = 0;
        $mpdf->defaultfooterline = 0;
        $stylesheet = file_get_contents(Url::to('@app/web/css/pdf.css'));
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        $mpdf->SetHeader(\yii\helpers\Html::img(\yii\helpers\Url::to('@web/' . $company['logo']), ['width' => 60]) . '|PROCESSOS COM PEDIDO DE LEVANTAMENTO POR RESOLVER |Gerado em: ' . date('Y-m-d h:i'));
        $mpdf->SetFooter('<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'); // call methods or set any properties

        $html = \app\modules\dsp\widget\ProcessoPlPorResolver::Widget([
            'data' => $dataProvider->getModels(),
        ]);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        return $pdf->render();
    }
}
