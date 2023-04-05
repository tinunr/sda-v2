<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Model;
use yii\helpers\Json;
use kartik\mpdf\Pdf;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\modules\fin\models\FaturaDebitoCliente;
use app\modules\fin\models\FaturaDebitoClienteItem;
use app\modules\fin\models\FaturaDebitoClienteProvisoria;
use app\modules\fin\models\FaturaDebitoClienteSearch;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\Nord;
use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\DespesaItem;
use app\modules\fin\models\NotaCredito;
use app\modules\fin\models\Despesa;
use app\components\helpers\NumberHelper;

/**
 * FaturaDebitoClienteController implements the CRUD actions for FaturaDebitoCliente model.
 */
class FaturaDebitoClienteController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          [
            'actions' => ['list-processo', 'get-items', 'get-items-parceal', 'list-fatura-provisoria'],
            'allow' => true,
            'roles' => ['@'],
          ],
          [
            'actions' => ['index', 'view', 'view-pdf', 'create-comofp', 'create-parceal', 'update', 'undo', 'view-pdf-new', 'send-unsend', 'report', 'report-pdf'],
            'allow' => true,
            'roles' => ['@'],
            // 'matchCallback' => function ($rule, $action) {
            //   return Yii::$app->AuthService->permissiomHandler() === true;
            // }
          ],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'undo' => ['post'],
          'send-unsend' => ['post'],
        ],
      ],
    ];
  }

  /**
   * Lists all FaturaDebitoCliente models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new FaturaDebitoClienteSearch();
    $searchModel->bas_ano_id = substr(date('Y'), -2);
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }



  /**
   * Displays a single FaturaDebitoCliente model.
   * @param integer $id
   * @param integer $perfil_id
   * @return mixed
   */
  public function actionView($id)
  {
    $model = $this->findModel($id);
    return $this->render('view', [
      'company' => Yii::$app->params['company'],
      'posicao_tabela' => Yii::$app->params['posicao_tabela'],
      'posicao_tabela_desc' => Yii::$app->params['posicao_tabela_desc'],
      'model' => $model,

    ]);
  }


  /**
   * Displays a single FaturaDebitoCliente model.
   * @param integer $id
   * @param integer $perfil_id
   * @return mixed
   */
  public function actionSendUnsend($id)
  {
    $model = $this->findModel($id);
    $model->send = !$model->send;
    if ($model->save(false)) {
      Yii::$app->getSession()->setFlash('success', 'FATURA ' . ($model->send ? 'CONFERIDO' : 'NÃO CONFERIDO') . ' COM SUCESSO.');
    } else {
      Yii::$app->getSession()->setFlash('error', 'Ecoreu um erro ao efetuar a operção.');
    }
    return $this->redirect(Yii::$app->request->referrer);
  }









  /**
   * Creates a new FaturaProvisoria model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreateComofp()
  {
    // $data_id = substr(date('Y'),-2);
    $model = new FaturaDebitoCliente;
    $model->data = date('Y-m-d');
    $model->fin_fatura_definitiva_serie = FaturaDebitoCliente::FATURA_DEFINITIVA_SERIE_B;
    $model->taxa_comunicaco = 200;
    $model->nord = null;
    $modelsFaturaDebitoClienteItem = [new FaturaDebitoClienteItem];
    if ($model->load(Yii::$app->request->post())) {
      $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);
      $modelsFaturaDebitoClienteItem = Model::createMultiple(FaturaDebitoClienteItem::classname());
      Model::loadMultiple($modelsFaturaDebitoClienteItem, Yii::$app->request->post());
      // validate all models
      $valid = $model->validate();

      if ($valid) {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
          if ($flag = $model->save()) {
            $totalRecebimento = 0;
            foreach ($modelsFaturaDebitoClienteItem as $modelFaturaDebitoClienteItem) {
              $modelFaturaDebitoClienteItem->fin_fatura_definitiva_id = $model->id;
              $totalRecebimento = $totalRecebimento + $modelFaturaDebitoClienteItem->valor;
              if (!($flag = $modelFaturaDebitoClienteItem->save(false))) {
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
    return $this->render('_form_comofp', [
      'model' => $model,
      'modelsFaturaDebitoClienteItem' => (empty($modelsFaturaDebitoClienteItem)) ? [new FaturaDebitoClienteItem] : $modelsFaturaDebitoClienteItem,

    ]);
  }

  /**
   * Updates an existing FaturaDebitoCliente model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @param integer $perfil_id
   * @return mixed
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
      return $this->redirect(['view', 'id' => $model->id]);
    } else {
      return $this->render('update', [
        'model' => $model,
      ]);
    }
  }

  /**
   * Deletes an existing FaturaDebitoCliente model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @param integer $perfil_id
   * @return mixed
   */
  public function actionUndo($id)
  {
      $model = $this->findModel($id);

    // if (Yii::$app->CntQuery->inContabilidadeAtive(\app\modules\cnt\models\Documento::FATURA_DEFINITIVA, $id)) {
    //   Yii::$app->getSession()->setFlash('warning', 'Esta FD já se encontra na Contabilidade anule na Contabilidade para poder proceguir.');
    //   return $this->redirect(['view', 'id' => $id]);
    // }

    // print_r($id);die();
    $avisoCredito = NotaCredito::find()
      ->where(['fin_fatura_defenitiva_id' => $model->fin_fatura_definitiva_id]) 
      ->andWhere(['status' => 1])
      ->all();
    if (empty($avisoCredito)) {

      // $fpdf = FaturaDebitoClienteProvisoria::find()->where(['fin_fatura_definitiva_id'=>$model->id])->all();
      // foreach ($fpdf as $key => $value) {
      //     $value->delete();
      // }
      $model->status = 0;
      $model->fin_totoal_fp = $model->valor;
      if ($model->save()) {
        Yii::$app->getSession()->setFlash('success', 'Debito de cliente anulado com suvcesso.');
      } else {
        // print_r($model->errors);die();
        Yii::$app->getSession()->setFlash('error', 'Ouve um erro ao efetuar a operção');
      }
    } else {
      Yii::$app->getSession()->setFlash('error', 'Este debito de cliente tem um Aviso de credito não Anulado');
    }

    return $this->redirect(['view', 'id' => $id]);
  }



  /**
   * Finds the FaturaDebitoCliente model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @param integer $perfil_id
   * @return FaturaDebitoCliente the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = FaturaDebitoCliente::findOne(['id' => $id])) !== null) {
      return $model;
    } else {
      throw new NotFoundHttpException('The requested page does not exist.');
    }
  }




  /**
   * Finds the Nord model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Nord the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionGetItemsParceal($id, array $fin_fatura_provisoria_id)
  {
    $regimeConfig = Yii::$app->params['regimeConfig'];
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    // vereficar se existe processo para associar a fatura provisória
    if (Yii::$app->FinQuery->checkDespesaFaturaProcesso($id)) {
      return ['error' => 'EXISTE NESTE PROCESSO DESPESA QUE NÃO FOI ASSOCIADO A NENHUMA FATURA PROVISÓRIA.'];
    }
    if (Yii::$app->FinQuery->checkDespesaFaturaProcesso($id)) {
      return ['error' => 'EXISTE NESTE PROCESSO DESPESA QUE NÃO FOI ASSOCIADO A NENHUMA FATURA PROVISÓRIA.'];
    }


    $isAdicional = 0;
    $impresso_principal = 0;
    $impresso_intercalar = 0;
    $pl = 0;
    $gti = 0;
    $tce = 0;
    $tn = 0;
    $form = 0;
    $regime_normal = 0;
    $regime_especial = 0;
    $exprevio_comercial = 0;
    $expedente_matricula = 0;
    $taxa_comunicaco = 0;
    $dv = 0;
    $fotocopias = 0;
    $qt_estampilhas = 0;
    $posicao_tabela = null;
    $posicao_tabela = null;
    $dsp_regime_item_valor = null;
    $dsp_regime_item_tabela_anexa_valor = null;
    $fin_totoal_fp = 0;

    $data = [];



    foreach (Yii::$app->FinQuery->getFaturaProvisoriaData($fin_fatura_provisoria_id) as $key => $value) {
      $impresso_principal = $impresso_principal + $value->impresso_principal;
      $impresso_intercalar = $impresso_intercalar + $value->impresso_intercalar;
      $pl = $pl + $value->pl;
      $gti = $gti + $value->gti;
      $tce = $tce + $value->tce;
      $tn = $tn + $value->tn;
      $form = $form + $value->form;
      $regime_normal = $regime_normal + $value->regime_normal;
      $regime_especial = $regime_especial + $value->regime_especial;
      $exprevio_comercial = $exprevio_comercial + $value->exprevio_comercial;
      $expedente_matricula = $expedente_matricula + $value->expedente_matricula;
      $taxa_comunicaco = $taxa_comunicaco + $value->taxa_comunicaco;
      $dv = $dv + $value->dv;
      $fotocopias = $fotocopias + $value->fotocopias;
      $qt_estampilhas = $qt_estampilhas + $value->qt_estampilhas;
      $fin_totoal_fp = $fin_totoal_fp + $value->valor;
    }

    // despachante
    if (($itemsData = Yii::$app->FinQuery->listDespachanteItemParceal($fin_fatura_provisoria_id)) != null) {
      // print_r($itemsData);die();
      foreach ($itemsData as $key => $item) {
        if ($item['id'] > 0) {
          $data[$item['id']] = [
            'dsp_item_id' => $item['id'],
            'descricao' => $item['descricao'],
            'valor' => $item['valor'],
          ];
        }
      }
    } // end despachante

    // item de despesa
    if (($itemsData2 = Yii::$app->FinQuery->listDespesaItemParceal($fin_fatura_provisoria_id)) != null) {
      foreach ($itemsData2 as $key2 => $item2) {
        if ($item2['id'] > 0) {
          $data[$item2['id']] = [
            'dsp_item_id' => $item2['id'],
            'descricao' => $item2['descricao'],
            'valor' => $item2['valor'],
          ];
        }
      }
    } // end item de despesa


    $processo = Processo::findOne($id);
    $faturaProvisoria = FaturaProvisoria::find()->where(['id' => $fin_fatura_provisoria_id])->orderBy('id')->one();
    if (!empty($faturaProvisoria->regimeItem->forma)) {
      $posicao_tabela = $faturaProvisoria->regimeItem->forma;
      $dsp_regime_item_valor = $faturaProvisoria->dsp_regime_item_valor;
      $dsp_regime_item_tabela_anexa_valor = $faturaProvisoria->dsp_regime_item_tabela_anexa_valor;
    }


    // aviso de crediro
    $avisoCredito = Yii::$app->FinQuery->isNotaCreditoParceal($fin_fatura_provisoria_id);
    // vereficar se existe processo para associar a fatura provisória
    if (Yii::$app->FinQuery->checkDespesaFaturaProcesso($id)) {
      return ['error' => 'EXISTE NESTE PROCESSO DESPESA QUE NÃO FOI ASSOCIADO A NENHUMA FATURA PROVISÓRIA.'];
      # code...
    }
    $acrescimo = 0;
    if (!empty($processo->nord->id)) {
      $acrescimo = (\app\modules\dsp\services\NordService::totalNumberOfItems($processo->nord->id) - $regimeConfig['ItemaNaoCobrar']);
    }

    // vereficar se não tem saldo
    if (Yii::$app->FinQuery->IsRecebidoPagoParceal($fin_fatura_provisoria_id)) {
      return ['error' => 'RECEBIMENTO OU PAGAMENTO PENDENTE NESTE(S) FATURA(S).'];
    } else {
      if (empty($data)) {
        return ['error' => 'NEHUM ITEM PARA APRESENTAR.'];
      }
      return [
        'item' => $data,
        'nord' => empty($processo->nord->id) ? '' : $processo->nord->id,
        'dsp_person_id' => empty($processo->nome_fatura) ? '' : $processo->nome_fatura,
        'dsp_person_nome' => empty($processo->nome_fatura) ? '' : $processo->nomeFatura->nome,
        'n_registo' => empty($processo->nord->despacho->id) ? '' : $processo->nord->despacho->id,
        'data_registo' => empty($processo->nord->despacho->data_registo) ? '' : $processo->nord->despacho->data_registo,
        'n_receita' => empty($processo->nord->despacho->n_receita) ? '' : $processo->nord->despacho->n_receita,
        'data_receita' => empty($processo->nord->despacho->data_receita) ? '' : $processo->nord->despacho->data_receita,
        'descricao' => $faturaProvisoria->mercadoria,
        'avisoCredito' => $avisoCredito,
        'isAdicional' => $isAdicional,
        'impresso_principal' => $impresso_principal,
        'impresso_intercalar' => $impresso_intercalar,
        'pl' => $pl,
        'gti' => $gti,
        'tce' => $tce,
        'tn' => $tn,
        'form' => $form,
        'regime_normal' => $regime_normal,
        'regime_especial' => $regime_especial,
        'exprevio_comercial' => $exprevio_comercial,
        'expedente_matricula' => $expedente_matricula,
        'taxa_comunicaco' => $taxa_comunicaco,
        'dv' => $dv,
        'fotocopias' => $fotocopias,
        'qt_estampilhas' => $qt_estampilhas,
        'posicao_tabela' => $posicao_tabela,
        'dsp_regime_item_valor' => $dsp_regime_item_valor,
        'dsp_regime_item_tabela_anexa_valor' => $dsp_regime_item_tabela_anexa_valor,
        'acrescimo' => $acrescimo,
        'fin_totoal_fp' => $fin_totoal_fp,

      ];
    }
  }

  /**
   * Finds the Nord model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Nord the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionGetNotaCredito($id)
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $data = [];
    if (($itemsData = Yii::$app->FinQuery->listFaturaDebitoClienteItem($id)) != null) {
      foreach ($itemsData as $key => $item) {
        $data[$key] = [
          'dsp_item_id' => $item['id'],
          'descricao' => $item['descricao'],
          'recebido' => $item['recebido'],
          'despesa' => $item['despesa'],
        ];
      }
      return $data;
    } else {
      return ['error' => 'Error'];
    }
  }




  // THE CONTROLLER
  public function actionListProcesso()
  {
    $out = [];
    if (isset($_POST['depdrop_parents'])) {
      $parents = $_POST['depdrop_parents'];
      if ($parents != null) {
        $id = $parents[0];

        $query = new Query;
        $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
          ->from('fin_fatura_definitiva A')
          ->where(['A.dsp_processo_id' => $id])
          ->orderBy('A.numero')
          ->all();
        $command = $query->createCommand();
        $out = $command->queryAll();
        echo Json::encode(['output' => $out, 'selected' => '']);
        return;
      }
    }
    echo Json::encode(['output' => '', 'selected' => '']);
  }


  // THE CONTROLLER
  public function actionListFaturaProvisoria()
  {
    $out = [];
    if (isset($_POST['depdrop_parents'])) {
      $parents = $_POST['depdrop_parents'];
      if ($parents != null) {
        $id = $parents[0];

        $subQuery = new Query;
        $subQuery->select(['A.fin_fatura_provisoria_id'])
          ->from('fin_fatura_definitiva_provisoria A')
          ->leftJoin('fin_fatura_provisoria B', 'B.id = A.fin_fatura_provisoria_id')
          ->leftJoin('fin_fatura_definitiva C', 'C.id = A.fin_fatura_definitiva_id')
          ->where(['B.status' => 1])
          ->andWhere(['C.status' => 1])
          ->andWhere(['A.dsp_processo_id' => $id]);


        $query = new Query;
        $query->select(['A.id as id', new \yii\db\Expression("CONCAT(A.numero, '/', A.bas_ano_id) as name")])
          ->from('fin_fatura_provisoria A')
          ->where(['A.dsp_processo_id' => $id])
          ->andWhere(['A.status' => 1])
          ->andWhere(['not in', 'A.id', $subQuery])
          ->orderBy('A.numero')
          ->all();
        $command = $query->createCommand();
        $out = $command->queryAll();
        $selected = ArrayHelper::getColumn($out, 'id');

        echo Json::encode(['output' => $out, 'selected' => $selected]);
        return;
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
  public function actionViewPdf($id)
  {
    $company = Yii::$app->params['company'];
    $model = $this->findModel($id);

    $content = $this->renderPartial('view-pdf', [
      'company' => $company,
      'posicao_tabela' => Yii::$app->params['posicao_tabela'],
      'posicao_tabela_desc' => Yii::$app->params['posicao_tabela_desc'],
      'model' => $model,
    ]);


    // setup kartik\mpdf\Pdf component
    $pdf = new Pdf([
      'mode' => Pdf::MODE_UTF8,
      'format' => Pdf::FORMAT_A4,
      'orientation' => Pdf::ORIENT_PORTRAIT,
      'destination' => Pdf::DEST_BROWSER,
      'content' => $content,
      'cssFile' => '@app/web/css/pdf.css',
      'marginTop' => 5,
      'marginLeft' => 5,
      'marginRight' => 5,
      'marginBottom' => 5,
      'options' => ['title' => 'Relatório Processo'],
      'methods' => [
        'SetFooter' => ['<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'],
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
   * Finds the Nord model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Nord the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionViewPdfNew($id)
  {
    $company = Yii::$app->params['company'];
    $model = $this->findModel($id);
    $model->send = $model->send == FaturaDebitoCliente::POR_VALIDAR ? FaturaDebitoCliente::POR_VALIDAR : FaturaDebitoCliente::ENVIADO;
    $model->save(false);

    $posicao_tabela = Yii::$app->params['posicao_tabela'];
    $posicao_tabela_desc = Yii::$app->params['posicao_tabela_desc'];

    $formatter = \Yii::$app->formatter;
    $faturaProvisoria = FaturaProvisoria::find()
      ->where(['dsp_processo_id' => $model->dsp_processo_id])
      ->one();

    $fpRc = Yii::$app->FinQuery->fpFaturaDefinitiva($model->fin_fatura_definitiva_id);
    $total_honorario = 0;
    $total_outros = 0;
    $total_despesas = 0;
    $total_honorariob = 0;
    $total_outrosb = 0;
    $total_despesasb = 0;
    $hta = 0;
    $break = 0;
    $htb = 0;
    $regimeConfig = Yii::$app->params['regimeConfig'];
    $valorBaseHonorario = Yii::$app->FinQuery->valorBaseHonorario($model->fin_fatura_definitiva_id);

    $pdf = Yii::$app->pdf;
    $pdf->mode = Pdf::MODE_UTF8;
    $pdf->format = Pdf::FORMAT_A4;
    $pdf->orientation = Pdf::ORIENT_PORTRAIT;
    $pdf->destination = Pdf::DEST_BROWSER;
    $pdf->marginTop = 5;
    $pdf->marginLeft = 5;
    $pdf->marginRight = 5;
    $pdf->marginBottom = 5;
    $mpdf = $pdf->api; // fetches mpdf api
    $mpdf->defaultheaderline = 0;
    $mpdf->defaultfooterline = 0;

    // print_r(Url::to('@app/web/css/pdf.css'));die();
    $stylesheet = file_get_contents(Url::to('@app/web/css/pdf.css'));
    $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);


    // ORIGINAL


    $html  = Html::beginTag('div', ['id' => $model->status ? ($model->send ? '' : 'rascunho') : 'anulado']);
    $html  .= Html::beginTag('div', ['id' => 'invoice']);
    $html .= Html::tag('div', Html::img(Url::to('@web/logo.png')), ['id' => 'logo']);
    $html .= Html::beginTag('div', ['id' => 'company']);
    $html .= Html::tag('h1', 'NOTA DE DÉBITO - CLIENTES Nº ' . $model->numero . '/' . $model->bas_ano_id);
    $html .= Html::tag('div', 'PROCESSO: ' . $model->processo->numero . '/' . $model->processo->bas_ano_id . ' NORD: ' . (empty($model->processo->nord->id) ? '' : $model->processo->nord->id), ['class' => 'address']);
    $html .= Html::tag('div', $fpRc['fatura_provisorias'] . ' ' . $fpRc['recibos'], ['class' => 'address']);
    $html .= Html::tag('div', 'Data: ' . $formatter->asDate($model->data), ['class' => 'address']);
    $html .= Html::endTag('div');
    $html .= Html::endTag('div');

    $html .= '<div id="details" class="clearfix">
        <div id="client">
        <div class="name1">' . $company['name'] . '</div>
              <div>' . $company['adress2'] . '</div>
              <div>NIF: ' . $company['nif'] . '</div>
        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Original</strong></div>
        <div id="invoice">
        <div class="">Exmos.(s) Sr.(s)</div>
          <h2 class="name">' . $model->person->nome . '</h2>
          <div class="address">' . $model->person->endereco . '</div>
          <div class="address">NIF ' . $model->person->nif . '</div>
          <div class="email">' . $model->person->email . '</a></div>
        </div>
      </div>
      <br>';

    $html .= '<p ><strong>Biblhete de despacho (b.d):</strong>   Regime: <strong>' . (empty($faturaProvisoria->dsp_regime_id) ? $model->dsp_regime_id : $faturaProvisoria->dsp_regime_id) . ' / ' . str_pad(\app\modules\dsp\services\NordService::subRegime($model->nord), 3, '0', STR_PAD_LEFT) . '</strong>, N.º Ordem <strong>' . $model->n_registo . '</strong>, de <strong>' . $formatter->asDate($model->data_registo) . '</strong>, N.º Receita <strong>' . $model->n_receita . '</strong> de <strong>' . $formatter->asDate($model->data_receita) . '</strong> , Número artigos Pautais: <strong>' . \app\modules\dsp\services\NordService::totalNumberOfItems($model->nord) . '</strong>.</p>
      <p style="margin-top: -20px; " ><strong>Mercadoria: </strong>' . $model->descricao . '</p>';
    if (strlen($model->descricao) > 120) {
      $hta = $hta + 1;
    };





    $html .= '<p><strong>Despesas</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>';

    foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->fin_fatura_definitiva_id) as $key => $modelItem) {
      $total_despesas = $total_despesas +  $modelItem['valor'];
      $html .= Html::beginTag('tr');
      $html .= Html::tag('td', str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'], ['class' => 'pdf desc', 'colspan' => '2']);
      $html .= Html::tag('td', $formatter->asCurrency($modelItem['valor']), ['class' => 'pdf total']);
      $html .= Html::endTag('tr');
      if ($hta == 20) {
        $html .= '</tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>Total a transportar</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></td>
          </tr>
        </tfoot>
      </table> ';
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->SetFooter('<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'); // call methods or set any properties

        $mpdf->AddPage();
        $hta = 0;
        $break = 1;
        $html = '<hr><p><strong>Despesas</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Total Transportado</strong></th>
            <th class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></th>
          </tr>
        </thead>
        <tbody>';
      }


      $hta++;
    }

    if ($break) {
      for ($j = $hta; $j < 44; $j++) {
        $html .= '<tr>
            <td class="pdf desc"colspan="2">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
          </tr>';
      }
    } else {
      for ($j = $hta; $j < 20; $j++) {
        $html .= '<tr>
            <td class="pdf desc"colspan="2">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
          </tr>';
      }
    }


    $html .= '</tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong> TOTAL: ' . NumberHelper::ConvertToWords($total_despesas) . ' escudos</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></td>
          </tr>
          
        </tfoot>
      </table> ';

    $html .= '<p style="text-align: center;">O Despachante</p>
                  <p style=" text-align: center;">' . ($model->send ? 'CONFERÊNCIA ELETRÓNICA' : '.................................') . '</p>
                  <p style=" text-align: center;">/ ' . $formatter->asDate($model->data) . ' /</p>';

    $html  .= Html::endTag('div');


    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->SetFooter('<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'); // call methods or set any properties

    // $mpdf->SetHTMLHeader($header);





















    // CONTABILIDADE
    $mpdf->AddPage();

    $hta = 0;
    $total_honorario = 0;
    $html  = Html::beginTag('div', ['id' => $model->status ? ($model->send ? '' : 'rascunho') : 'anulado']);

    $html  .= Html::beginTag('div', ['id' => 'invoice']);
    $html .= Html::tag('div', Html::img(Url::to('@web/logo.png')), ['id' => 'logo']);
    $html .= Html::beginTag('div', ['id' => 'company']);
    $html .= Html::tag('h1', 'NOTA DE DÉBITO - CLIENTES Nº ' . $model->numero . '/' . $model->bas_ano_id);
    $html .= Html::tag('div', 'PROCESSO: ' . $model->processo->numero . '/' . $model->processo->bas_ano_id . ' NORD: ' . (empty($model->processo->nord->id) ? '' : $model->processo->nord->id), ['class' => 'address']);
    $html .= Html::tag('div', $fpRc['fatura_provisorias'] . ' ' . $fpRc['recibos'], ['class' => 'address']);
    $html .= Html::tag('div', 'Data: ' . $formatter->asDate($model->data), ['class' => 'address']);
    $html .= Html::endTag('div');
    $html .= Html::endTag('div');

    $html .= '<div id="details" class="clearfix">
        <div id="client">
        <div class="name1">' . $company['name'] . '</div>
              <div>' . $company['adress2'] . '</div>
              <div>NIF: ' . $company['nif'] . '</div>
        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>contabilidade</strong></div>
        <div id="invoice">
        <div class="">Exmos.(s) Sr.(s)</div>
          <h2 class="name">' . $model->person->nome . '</h2>
          <div class="address">' . $model->person->endereco . '</div>
          <div class="address">NIF ' . $model->person->nif . '</div>
          <div class="email">' . $model->person->email . '</a></div>
        </div>
      </div>
      <br>';

    $html .= '<p ><strong>Biblhete de despacho (b.d):</strong>   Regime: <strong>' .  (empty($faturaProvisoria->dsp_regime_id) ? $model->dsp_regime_id : $faturaProvisoria->dsp_regime_id)  . ' / ' . str_pad(\app\modules\dsp\services\NordService::subRegime($model->nord), 3, '0', STR_PAD_LEFT) . '</strong>, N.º Ordem <strong>' . $model->n_registo . '</strong>, de <strong>' . $formatter->asDate($model->data_registo) . '</strong>, N.º Receita <strong>' . $model->n_receita . '</strong> de <strong>' . $formatter->asDate($model->data_receita) . '</strong> , Número artigos Pautais: <strong>' . \app\modules\dsp\services\NordService::totalNumberOfItems($model->nord) . '</strong>.</p>
       <p style="margin-top: -20px; " ><strong>Mercadoria: </strong>' . $model->descricao . '</p>';
    if (strlen($model->descricao) > 120) {
      $hta = $hta + 1;
    };





    $html .= '<p><strong>Despesas</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>';
    $total_despesas = 0;
    foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->fin_fatura_definitiva_id) as $key => $modelItem) {
      $total_despesas = $total_despesas +  $modelItem['valor'];
      $html .= Html::beginTag('tr');
      $html .= Html::tag('td', str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'], ['class' => 'pdf desc', 'colspan' => '2']);
      $html .= Html::tag('td', $formatter->asCurrency($modelItem['valor']), ['class' => 'pdf total']);
      $html .= Html::endTag('tr');
      if ($hta == 20) {
        $html .= '</tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>Total a transportar</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></td>
          </tr>
        </tfoot>
      </table> ';
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->SetFooter('<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'); // call methods or set any properties

        $mpdf->AddPage();
        $hta = 0;
        $break = 1;
        $html = '<hr><p><strong>Despesas</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Total Transportado</strong></th>
            <th class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></th>
          </tr>
        </thead>
        <tbody>';
      }


      $hta++;
    }

    if ($break) {
      for ($j = $hta; $j < 44; $j++) {
        $html .= '<tr>
            <td class="pdf desc"colspan="2">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
          </tr>';
      }
    } else {
      for ($j = $hta; $j < 20; $j++) {
        $html .= '<tr>
            <td class="pdf desc"colspan="2">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
          </tr>';
      }
    }


    $html .= '</tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>TOTAL:  ' . NumberHelper::ConvertToWords($total_despesas) . '  escudos</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></td>
          </tr>
        </tfoot>
      </table> ';

    $html .= '<p style="text-align: center;">O Despachante</p>
                  <p style=" text-align: center;">' . ($model->send ? 'CONFERÊNCIA ELETRÓNICA' : '.................................') . '</p>
                  <p style=" text-align: center;">/ ' . $formatter->asDate($model->data) . ' /</p>';

    $html  .= Html::endTag('div');


    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->SetFooter('<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'); // call methods or set any properties

    return $pdf->render();
  }


  /**
   * Lists all FaturaProvisoria models.
   * @return mixed
   */
  public function actionReport()
  {
    $searchModel = new FaturaDebitoClienteSearch();

    return $this->render('_searchReport', [
      'model' => $searchModel,
    ]);
  }

  /**
   * Lists all Receita models.
   * @return mixed
   */
  public function actionReportPdf()
  {

    $company = Yii::$app->params['company'];
    $data = Yii::$app->request->queryParams;
    $titleReport = 'Listagem de Fatura Provisoória de ' . $data['FaturaDebitoClienteSearch']['beginDate'] . ' a ' . $data['FaturaDebitoClienteSearch']['endDate'];

    $searchModel = new FaturaDebitoClienteSearch($data['FaturaDebitoClienteSearch']);
    $dataProvider = $searchModel->searchReport(Yii::$app->request->queryParams);
    $content = $this->renderPartial('report', [
      'dataProvider' => $dataProvider,
    ]);
    // setup kartik\mpdf\Pdf component
    $pdf = new Pdf([
      'mode' => Pdf::MODE_UTF8,
      'format' => Pdf::FORMAT_A4,
      'orientation' => Pdf::ORIENT_LANDSCAPE,
      'destination' => Pdf::DEST_BROWSER,
      'content' => $content,
      'marginTop' => 50,
      'marginLeft' => 10,
      'marginRight' => 10,
      'cssFile' => '@app/web/css/kv-mpdf-bootstrap.css',
      'options' => ['title' => 'Relatório Processo'],
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
                      <br><strong><i class="fa fa-graduation-cap"></i> C.P. Nº ' . $company['cp'] . ' - Tel.: ' . $company['teletone'] . ' - FAX: ' . $company['fax'] . ' - Praia, Santiago</strong>
                      </p> |Página {PAGENO}/{nbpg}|Emetido em: ' . date("Y-m-d H:i:s")],
      ]
    ]);
    $pdf->options = [
      'defaultheaderline' => 0,
      'defaultfooterline' => 0,
    ];

    return $pdf->render();
  }
}
