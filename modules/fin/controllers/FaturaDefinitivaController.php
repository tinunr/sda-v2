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
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\FaturaDefinitivaItem;
use app\modules\fin\models\FaturaDefinitivaProvisoria;
use app\modules\fin\models\FaturaDefinitivaSearch;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\Nord;
use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\DespesaItem;
use app\modules\fin\models\NotaCredito;
use app\modules\fin\models\Despesa;
use app\components\helpers\NumberHelper;
use app\modules\fin\models\FaturaDebitoCliente;
use app\modules\fin\models\FaturadebitoClienteItem;
use app\modules\fin\models\FaturaEletronica;
use app\modules\fin\models\FaturaEletronicaItem;
use app\modules\fin\services\FaturaService;

/**
 * FaturaDefinitivaController implements the CRUD actions for FaturaDefinitiva model.
 */
class FaturaDefinitivaController extends Controller
{
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          [
            'actions' => ['list-processo', 'get-items', 'get-items-parceal', 'list-fatura-provisoria','view-pdf-resumo'],
            'allow' => true,
            'roles' => ['@'],
          ],
          [
            'actions' => ['index', 'view', 'view-pdf', 'create-comofp', 'create-parceal', 'update', 'undo', 'view-pdf-new', 'send-unsend', 'report', 'report-pdf', 'create-fatura'],
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
          // 'undo' => ['post'],
          'send-unsend' => ['post'],
        ],
      ],
    ];
  }

  /**
   * Lists all FaturaDefinitiva models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new FaturaDefinitivaSearch();
    $searchModel->bas_ano_id = substr(date('Y'), -2);
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }



  /**
   * Displays a single FaturaDefinitiva model.
   * @param integer $id
   * @param integer $perfil_id
   * @return mixed
   */
  public function actionView($id)
  {

        // FaturaService::updatAllFaturaNotaDebitoCliente();

    $model = $this->findModel($id);
    return $this->render('view', [
      'company' => Yii::$app->params['company'],
      'posicao_tabela' => Yii::$app->params['posicao_tabela'],
      'posicao_tabela_desc' => Yii::$app->params['posicao_tabela_desc'],
      'model' => $model,

    ]);
  }


  /**
   * Displays a single FaturaDefinitiva model.
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
   * Creates a new FaturaDefinitiva model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreateParceal()
  {
    $model = new FaturaDefinitiva();
    $model->data = date('Y-m-d');
    // $documentoNumero = DocumentoNumero::findByDocumentId(Documento::FATURA_DEFINITIVA_ID);
    // $model->numero = $documentoNumero->getNexNumber();

    $modelsFaturaDefinitivaItem = [new FaturaDefinitivaItem];

    if ($model->load(Yii::$app->request->post())) {
      $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);

      $modelsFaturaDefinitivaItem = Model::createMultiple(FaturaDefinitivaItem::classname());
      Model::loadMultiple($modelsFaturaDefinitivaItem, Yii::$app->request->post());
      // ajax validation
      if (Yii::$app->request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ArrayHelper::merge(
          ActiveForm::validateMultiple($modelsFaturaDefinitivaItem),
          ActiveForm::validate($model)
        );
      }

      $valid = $model->validate();
      if ($valid) {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
          if ($flag = $model->save()) {
            foreach ($modelsFaturaDefinitivaItem as $modelFaturaDefinitivaItem) {
              $modelFaturaDefinitivaItem->fin_fatura_definitiva_id = $model->id;
              if (!($flag = $modelFaturaDefinitivaItem->save())) {
                $transaction->rollBack();
                break;
              }
            }

            foreach ($model->fin_fatura_provisoria_id as $key => $value) {
              $faturaDefinitivaProvisoria = new FaturaDefinitivaProvisoria();
              $faturaDefinitivaProvisoria->dsp_processo_id = $model->dsp_processo_id;
              $faturaDefinitivaProvisoria->fin_fatura_definitiva_id = $model->id;
              $faturaDefinitivaProvisoria->fin_fatura_provisoria_id = $value;
              if (!($flag = $faturaDefinitivaProvisoria->save())) {
                $transaction->rollBack();
                break;
              }
            }


            if (Yii::$app->FinQuery->isNotaCreditoParceal($model->fin_fatura_provisoria_id) > 0) {
              $documentoNotaCredito = DocumentoNumero::findByDocumentId(Documento::AVISO_CREDITO);
              $modelNotaCredito = new NotaCredito();
              $modelNotaCredito->numero = $documentoNotaCredito->getNexNumber();
              $modelNotaCredito->bas_ano_id = $model->bas_ano_id;
              $modelNotaCredito->data = $model->data;
              $modelNotaCredito->fin_fatura_defenitiva_id = $model->id;
              $modelNotaCredito->dsp_person_id = $model->dsp_person_id;
              $modelNotaCredito->dsp_processo_id = $model->dsp_processo_id;
              $modelNotaCredito->valor = Yii::$app->FinQuery->isNotaCreditoParceal($model->fin_fatura_provisoria_id);
              $modelNotaCredito->descricao = 'Aviso de credito gerado atravez da FD nº' . $model->numero . '/' . $model->bas_ano_id . ' do Processo nº ' . $model->processo->numero . '/' . $model->processo->bas_ano_id;
              if (!($flag = $modelNotaCredito->save())) {
                $transaction->rollBack();
              }
              $documentoNotaCredito->saveNexNumber();

              $despesa = new Despesa();
              $despesa->fin_nota_credito_id = $modelNotaCredito->id;
              $despesa->data = $modelNotaCredito->data;
              $despesa->bas_ano_id = $modelNotaCredito->bas_ano_id;
              $despesa->dsp_processo_id = $modelNotaCredito->dsp_processo_id;
              $despesa->dsp_person_id = $modelNotaCredito->dsp_person_id;
              $despesa->data = date('Y-m-d');
              $despesa->valor = 0;
              $despesa->valor_pago = 0;
              $despesa->saldo = $modelNotaCredito->valor;
              $despesa->recebido = 1;
              $despesa->status = 1;
              $despesa->of_acount = 1;
              $despesa->descricao = 'Despesa gerada a partir do aviso de credito nº ' . $modelNotaCredito->numero . '/' . $modelNotaCredito->bas_ano_id;
              if (!($flag = $despesa->save())) {
                $transaction->rollBack();
              }

              $despesaItem = new DespesaItem();
              $despesaItem->fin_despesa_id = $despesa->id;
              $despesaItem->item_id = 1102;
              $despesaItem->valor = $modelNotaCredito->valor;
              $despesaItem->valor_iva = 0;
              $despesaItem->cnt_plano_terceiro_id = $model->dsp_person_id;
              if (!($flag = $despesaItem->save())) {
                $transaction->rollBack();
              }
            } else {
              if (Yii::$app->FinQuery->valorRecebido($model->fin_fatura_provisoria_id) != $model->valor) {
                Yii::$app->getSession()->setFlash('warning', 'VALOR DA FATURA DIFERENTO DO VALOR RECEBIDO E NÃO FOI GERADO UMA AVISO DE CREDITO');
                $transaction->rollBack(); 
              }
            } 

            if ($flag) {
              $transaction->commit();
              return $this->redirect(['view', 'id' => $model->id]);
            } else {
              $transaction->rollBack(); 
            }
          }
        } catch (Exception $e) {
          $transaction->rollBack();
        }
      }  
    }  
      return $this->render('_form_parceal', [
        'model' => $model,
        'modelsFaturaDefinitivaItem' => (empty($modelsFaturaDefinitivaItem)) ? [new FaturaDefinitivaItem] : $modelsFaturaDefinitivaItem,
      ]); 
  }





  /**
   * Creates a new FaturaProvisoria model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreateComofp()
  {
    // $data_id = substr(date('Y'),-2);
    $model = new FaturaDefinitiva;
    $model->data = date('Y-m-d');
    $model->fin_fatura_definitiva_serie = FaturaDefinitiva::FATURA_DEFINITIVA_SERIE_B;
    $model->taxa_comunicaco = 200;
    $model->nord = null;
    $modelsFaturaDefinitivaItem = [new FaturaDefinitivaItem];
    if ($model->load(Yii::$app->request->post())) {
      $model->bas_ano_id = substr(date("Y", strtotime($model->data)), -2);
      $modelsFaturaDefinitivaItem = Model::createMultiple(FaturaDefinitivaItem::classname());
      Model::loadMultiple($modelsFaturaDefinitivaItem, Yii::$app->request->post());
      // validate all models
      $valid = $model->validate();

      if ($valid) {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
          if ($flag = $model->save()) {
            $totalRecebimento = 0;
            foreach ($modelsFaturaDefinitivaItem as $modelFaturaDefinitivaItem) {
              $modelFaturaDefinitivaItem->fin_fatura_definitiva_id = $model->id;
              $totalRecebimento = $totalRecebimento + $modelFaturaDefinitivaItem->valor;
              if (!($flag = $modelFaturaDefinitivaItem->save(false))) {
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
      'modelsFaturaDefinitivaItem' => (empty($modelsFaturaDefinitivaItem)) ? [new FaturaDefinitivaItem] : $modelsFaturaDefinitivaItem,

    ]);
  }

  /**
   * Updates an existing FaturaDefinitiva model.
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
   * Deletes an existing FaturaDefinitiva model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @param integer $perfil_id
   * @return mixed
   */
  public function actionUndo($id)
  {
      $model = $this->findModel($id);

      if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
         if (Yii::$app->CntQuery->inContabilidadeAtive(\app\modules\cnt\models\Documento::FATURA_DEFINITIVA, $id)) {
            Yii::$app->getSession()->setFlash('warning', 'Esta FD já se encontra na Contabilidade anule na Contabilidade para poder proceguir.');
            return $this->redirect(['view', 'id' => $id]);
          }
 
    $avisoCredito = NotaCredito::find()
      ->where(['fin_fatura_defenitiva_id' => $id])
      ->andWhere(['fin_fatura_defenitiva_id' => $id])
      ->andWhere(['status' => 1])
      ->all();
    if (empty($avisoCredito)) { 
      $model->status = 0;
      $model->fin_totoal_fp = $model->valor;
      if ($model->save()) {

        if($model->undo_fatura){
          FaturaService::undoFatura($model->faturaEletronica->id);
        }
        if($model->undo_nota_debito){
          FaturaService::undoNotaDebito($model->faturaDebitoCliente->id);
        }
        Yii::$app->getSession()->setFlash('success', 'FD anulado com suvcesso.');
      } else {
        // print_r($model->errors);die();
        Yii::$app->getSession()->setFlash('error', 'Ouv um erro ao efetuar a operção');
      }
    } else {
      Yii::$app->getSession()->setFlash('error', 'Esta FD tem um Aviso de credito não Anulado');
    }

    return $this->redirect(['view', 'id' => $id]); 
      }  
      return $this->render('undo', [
        'model' => $model,
      ]); 

   
  }



  /**
   * Finds the FaturaDefinitiva model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @param integer $perfil_id
   * @return FaturaDefinitiva the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = FaturaDefinitiva::findOne(['id' => $id])) !== null) {
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
    $dv = 0;
    $fotocopias = 0;
    $qt_estampilhas = 0;
    $posicao_tabela = null;
    $posicao_tabela = null;
    $dsp_regime_item_valor = null;
    $dsp_regime_item_tabela_anexa_valor = null;
    $fin_totoal_fp = 0;
	$taxa_comunicaco = 0;
	$impresso = 0;
	$deslocacao_transporte = 0;

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
      $dv = $dv + $value->dv;
      $fotocopias = $fotocopias + $value->fotocopias;
      $qt_estampilhas = $qt_estampilhas + $value->qt_estampilhas;
      $fin_totoal_fp = $fin_totoal_fp + $value->valor;
	  $taxa_comunicaco = $taxa_comunicaco + $value->taxa_comunicaco; 
	  $impresso = $impresso +  $value->impresso;
	  $deslocacao_transporte = $deslocacao_transporte + $value->deslocacao_transporte;
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
        'dv' => $dv,
        'fotocopias' => $fotocopias,
        'qt_estampilhas' => $qt_estampilhas,
        'posicao_tabela' => $posicao_tabela,
        'dsp_regime_item_valor' => $dsp_regime_item_valor,
        'dsp_regime_item_tabela_anexa_valor' => $dsp_regime_item_tabela_anexa_valor,
        'acrescimo' => $acrescimo,
        'fin_totoal_fp' => $fin_totoal_fp,
		'taxa_comunicaco' => $taxa_comunicaco, 
		'impresso' => $impresso,
		'deslocacao_transporte' => $deslocacao_transporte, 
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
    if (($itemsData = Yii::$app->FinQuery->listFaturaDefinitivaItem($id)) != null) {
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
  public function actionViewPdfResumo($id)
  {
    $company = Yii::$app->params['company'];
    $model = $this->findModel($id);
    $model->send = $model->send == FaturaDefinitiva::POR_VALIDAR ? FaturaDefinitiva::POR_VALIDAR : FaturaDefinitiva::ENVIADO;
    $model->save(false);

    $posicao_tabela = Yii::$app->params['posicao_tabela'];
    $posicao_tabela_desc = Yii::$app->params['posicao_tabela_desc'];

    $formatter = \Yii::$app->formatter;
    $faturaProvisoria = FaturaProvisoria::find()
      ->where(['dsp_processo_id' => $model->dsp_processo_id])
      ->one();

    $fpRc = Yii::$app->FinQuery->fpFaturaDefinitiva($model->id);
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
    $valorBaseHonorario = Yii::$app->FinQuery->valorBaseHonorario($model->id);

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
  

    $html .= '<div id="details" class="clearfix">
        <div id="clients">';
		 $html .= Html::tag('div', Html::img(Url::to('@web/logo.png'),['style'=>'height:44px;']), ['id' => 'logo']);
        $html .= '<div class="name1">' . $company['name'] . '</div>
              <div>' . $company['adress2'] . '</div>
              <div>NIF: ' . $company['nif'] . '</div>
        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Original</strong></div>
        
      </div>
      <br>'; 
 
	$html  .= Html::beginTag('div', ['id' => 'invoices', 'style'=>"text-align: right; margin-top: -40px; text-transform: uppercase;  "]);
    $html .= ' <div class="">Exmos.(s) Sr.(s)</div>
	<h2 class="name">' . $model->person->nome . '</h2>
          <div class="address">' . $model->person->endereco . '</div>
          <div class="address">NIF ' . $model->person->nif . '</div>
          <div class="email">' . $model->person->email . '</a></div>';
    $html .= Html::endTag('div');
	
	$html .= '<br>';

    $html .= '<p ><strong>Biblhete de despacho (b.d):</strong>   Regime: <strong>' . (empty($faturaProvisoria->dsp_regime_id) ? $model->dsp_regime_id : $faturaProvisoria->dsp_regime_id) . ' / ' . str_pad(\app\modules\dsp\services\NordService::subRegime($model->nord), 3, '0', STR_PAD_LEFT) . '</strong>, N.º Ordem <strong>' . $model->n_registo . '</strong>, de <strong>' . $formatter->asDate($model->data_registo) . '</strong>, N.º Receita <strong>' . $model->n_receita . '</strong> de <strong>' . $formatter->asDate($model->data_receita) . '</strong> , Número artigos Pautais: <strong>' . \app\modules\dsp\services\NordService::totalNumberOfItems($model->nord) . '</strong>.</p>
      <p style="margin-top: -20px; " ><strong>Mercadoria: </strong>' . $model->descricao . '</p>';
    if (strlen($model->descricao) > 120) {
      $hta = $hta + 1;
    };
    if(!empty($model->faturaEletronica->numero)):
    $html .= '<hr>';
   $html .= Html::beginTag('div', ['id' => 'companys']);
    $html .= Html::tag('h1', 'FATURA Nº ' . $model->faturaEletronica->numero . '/' . $model->faturaEletronica->bas_ano_id,['style'=>'font-size:10px;']);
    $html .= Html::tag('div ', 'FD: '.$model->numero().' PROCESSO: ' . $model->processo->numero . '/' . $model->processo->bas_ano_id . ' NORD: ' . (empty($model->processo->nord->id) ? '' : $model->processo->nord->id), ['class' => 'address']);
    $html .= Html::tag('div', $fpRc['fatura_provisorias'] . ' ' . $fpRc['recibos'], ['class' => 'address']);
    $html .= Html::tag('div', 'Data: ' . $formatter->asDate($model->data), ['class' => 'address']);
    $html .= Html::endTag('div');


    if(!empty($model->faturaEletronica->iud)){
      $qrCode = new \Mpdf\QrCode\QrCode('https://pe.efatura.cv/dfe/view/'.$model->faturaEletronica->iud);      
      $output = new \Mpdf\QrCode\Output\Png();
      $data = $output->output($qrCode, 100, [255, 255, 255], [0, 0, 0]);
      file_put_contents(Yii::getAlias('@efatura/efaturaQrcode.png'), $data);
      $html .= '<p style="float: right; margin-right: -110px; ">'.$model->faturaEletronica->iud.'</p>';
      $html .= Html::img(Yii::getAlias('@efatura/efaturaQrcode.png'),['style'=>'float: right; text-align: right; margin-top:-100px']); 
    }
	
	$html .= ' <p><strong>Honorário</strong></p>';
    $html .= '<table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
            <th class="pdf total" style="width: 5px;"><strong>Uni./Ton./Hec.</strong></th>
            <th class="pdf total" style="width: 310px;"><strong>Posição da Tabela</strong></th>
            <th class="pdf total"><strong>Total</strong></th>
          </tr>
        </thead>
        <tbody>';

    if (!$model->person->isencao_honorario && Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) > 0) {
    
      if (!empty($model->tn)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->tn * $posicao_tabela['tn']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['tn'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tn), ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['tn'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tn * $posicao_tabela['tn']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->form)) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->form * $posicao_tabela['form']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['form'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->form, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['form'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->form * $posicao_tabela['form']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->regime_normal)) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->regime_normal * $posicao_tabela['regime_normal']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['regime_normal'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->regime_normal, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['regime_normal'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->regime_normal * $posicao_tabela['regime_normal']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->regime_especial)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->regime_especial * $posicao_tabela['regime_especial'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['regime_especial'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->regime_especial, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['regime_especial'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->regime_especial * $posicao_tabela['regime_especial']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->exprevio_comercial)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->exprevio_comercial * $posicao_tabela['exprevio_comercial'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['exprevio_comercial'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->exprevio_comercial, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['exprevio_comercial'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->exprevio_comercial * $posicao_tabela['exprevio_comercial']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->expedente_matricula)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->expedente_matricula * $posicao_tabela['expedente_matricula']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['expedente_matricula'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->expedente_matricula, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['expedente_matricula'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->expedente_matricula * $posicao_tabela['expedente_matricula']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->dv)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->dv * $posicao_tabela['dv']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['dv'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->dv, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['dv'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->dv * $posicao_tabela['dv']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->gti)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->gti * $posicao_tabela['gti'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['gti'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->gti, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['gti'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->gti * $posicao_tabela['gti']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->pl)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->pl * $posicao_tabela['pl'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['pl'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->pl, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['pl'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->pl * $posicao_tabela['pl']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->tce)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->tce * $posicao_tabela['tce'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['tce'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->tce, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['tce'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tce * $posicao_tabela['tce']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->acrescimo)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->acrescimo * $regimeConfig['valorPorItem'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Acréscimo', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->acrescimo, ['class' => 'pdf total']);
        $html .= Html::tag('td', '100 por item', ['class' => 'pdf total']);
        $html .= Html::tag('td', ($model->acrescimo * $regimeConfig['valorPorItem']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->valorOutrosHonorarios())) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->valorOutrosHonorarios();
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Outros Honorários', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->valorOutrosHonorarios()), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }
	  
      $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario;

      if ($valor_tabel_honorario == 500) {
        $vthdesc = 'Valor Mininimo 500';
      } elseif (!empty($model->posicao_tabela)) {
        $vthdesc = $model->posicao_tabela;
      } elseif (empty($faturaProvisoria->regimeItem->forma)) {
        if (empty($faturaProvisoria->regimeItemItem->forma)) {
          $vthdesc = 'Valor Mininimo 500';
        } else {
          $vthdesc = $faturaProvisoria->regimeItemItem->forma;
        }
      } else {
        $vthdesc = $faturaProvisoria->regimeItem->forma;
      }

      $html .= Html::beginTag('tr');
      $html .= Html::tag('td', 'Tabela Honorário', ['class' => 'pdf desc']);
      $html .= Html::tag('td', ($valorBaseHonorario['tipo'] ? $formatter->asCurrency($valorBaseHonorario['valor']) : ''), ['class' => 'pdf total']);
      $html .= Html::tag('td', (!$valorBaseHonorario['tipo'] ? $valorBaseHonorario['valor'] : ''), ['class' => 'pdf total']);
      $html .= Html::tag('td', $vthdesc, ['class' => 'pdf desc']);
      $html .= Html::tag('td', $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario), ['class' => 'pdf total']);
      $html .= Html::endTag('tr');
	
	
	  


      $a = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario;
      $total_honorario = $total_honorario + $a;
    }

     foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->id) as $key => $modelItem) {
       //print_r($modelItem);die();
        $total_outros = $total_outros +  $modelItem['valor'];
        $hta++;
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'], ['class' => 'pdf desc', 'colspan' => '']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($modelItem['valor']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
		}
    // Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)+Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id);
    $html .= '</tbody><tfoot>
          <tr>
            <td class="pdf desc" colspan="4"><strong>Total Geral Honorário</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_outros + Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr> 
		  <tr>
            <td class="pdf desc" colspan="4"><strong>Base tributável</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr>
		  <tr>
            <td class="pdf desc" colspan="4"><strong>IVA</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>TOTAL: ' . NumberHelper::ConvertToWords($total_outros+Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_outros+Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
            </tr>
        </tfoot>

      </table>'; 
  endif;
     
    $html .= '<hr>';
if(!empty($model->faturaDebitoCliente->numero)):
    $html .= Html::beginTag('div', ['id' => 'companys']);
    $html .= Html::tag('h1', 'NOTA DE DÉBITO Nº ' . $model->faturaDebitoCliente->numero . '/' . $model->faturaDebitoCliente->bas_ano_id,['style'=>'font-size:10px;']);
    $html .= Html::tag('div ', 'FD: '.$model->numero().' PROCESSO: ' . $model->processo->numero . '/' . $model->processo->bas_ano_id . ' NORD: ' . (empty($model->processo->nord->id) ? '' : $model->processo->nord->id), ['class' => 'address']);
    $html .= Html::tag('div', $fpRc['fatura_provisorias'] . ' ' . $fpRc['recibos'], ['class' => 'address']);
    $html .= Html::tag('div', 'Data: ' . $formatter->asDate($model->data), ['class' => 'address']);
    $html .= Html::endTag('div'); 
	
    $html .= ' <p><strong>Despesas</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>';

    foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->id) as $key => $modelItem) {
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
      for ($j = $hta; $j < 16; $j++) {
        $html .= '<tr>
            <td class="pdf desc"colspan="2">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
          </tr>';
      }
    }


    $html .= '</tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>TOTAL: ' . NumberHelper::ConvertToWords($total_despesas) . ' escudos</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></td>
          </tr>
          <tr>
            <th class="pdf desc" colspan="2"><strong>TOTAL GERAL: '.NumberHelper::ConvertToWords($model->valor).' escudos </strong> </th>
            <th class="pdf total"><strong>' . $formatter->asCurrency($model->valor) . '</strong></th>
          </tr>
        </tfoot>
      </table> ';
  endif;
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
    $total_despesas = 0;
    $total_outros = 0;
    $html  = Html::beginTag('div', ['id' => $model->status ? ($model->send ? '' : 'rascunho') : 'anulado']);
  

    $html .= '<div id="details" class="clearfix">
        <div id="clients">';
		 $html .= Html::tag('div', Html::img(Url::to('@web/logo.png'),['style'=>'height:44px;']), ['id' => 'logo']);
        $html .= '<div class="name1">' . $company['name'] . '</div>
              <div>' . $company['adress2'] . '</div>
              <div>NIF: ' . $company['nif'] . '</div>
        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Contabilide</strong></div>
        
      </div>
      <br>'; 
 
	$html  .= Html::beginTag('div', ['id' => 'invoices', 'style'=>"text-align: right; margin-top: -40px; text-transform: uppercase;  "]);
    $html .= ' <div class="">Exmos.(s) Sr.(s)</div>
	<h2 class="name">' . $model->person->nome . '</h2>
          <div class="address">' . $model->person->endereco . '</div>
          <div class="address">NIF ' . $model->person->nif . '</div>
          <div class="email">' . $model->person->email . '</a></div>';
    $html .= Html::endTag('div');
	
	$html .= '<br>';

    $html .= '<p ><strong>Biblhete de despacho (b.d):</strong>   Regime: <strong>' . (empty($faturaProvisoria->dsp_regime_id) ? $model->dsp_regime_id : $faturaProvisoria->dsp_regime_id) . ' / ' . str_pad(\app\modules\dsp\services\NordService::subRegime($model->nord), 3, '0', STR_PAD_LEFT) . '</strong>, N.º Ordem <strong>' . $model->n_registo . '</strong>, de <strong>' . $formatter->asDate($model->data_registo) . '</strong>, N.º Receita <strong>' . $model->n_receita . '</strong> de <strong>' . $formatter->asDate($model->data_receita) . '</strong> , Número artigos Pautais: <strong>' . \app\modules\dsp\services\NordService::totalNumberOfItems($model->nord) . '</strong>.</p>
      <p style="margin-top: -20px; " ><strong>Mercadoria: </strong>' . $model->descricao . '</p>';
    if (strlen($model->descricao) > 120) {
      $hta = $hta + 1;
    };

$html .= '<hr>';
if(!empty($model->faturaEletronica->numero)):
   $html .= Html::beginTag('div', ['id' => 'companys']);
    $html .= Html::tag('h1', 'FATURA Nº ' . $model->faturaEletronica->numero . '/' . $model->faturaEletronica->bas_ano_id,['style'=>'font-size:10px;']);
    $html .= Html::tag('div ', 'FD: '.$model->numero().' PROCESSO: ' . $model->processo->numero . '/' . $model->processo->bas_ano_id . ' NORD: ' . (empty($model->processo->nord->id) ? '' : $model->processo->nord->id), ['class' => 'address']);
    $html .= Html::tag('div', $fpRc['fatura_provisorias'] . ' ' . $fpRc['recibos'], ['class' => 'address']);
    $html .= Html::tag('div', 'Data: ' . $formatter->asDate($model->data), ['class' => 'address']);

    if(!empty($model->faturaEletronica->iud)){
      // $qrCode = new \Mpdf\QrCode\QrCode('https://pe.efatura.cv/dfe/view/'.$model->faturaEletronica->iud);      
      // $output = new \Mpdf\QrCode\Output\Png();
      // $data = $output->output($qrCode, 100, [255, 255, 255], [0, 0, 0]);
      // file_put_contents(Yii::getAlias('@efatura/efaturaQrcode.png'), $data);
      $html .= '<p style="float: right; margin-right: -110px; ">'.$model->faturaEletronica->iud.'</p>';
      $html .= Html::img(Yii::getAlias('@efatura/efaturaQrcode.png'),['style'=>'float: right; text-align: right; margin-top:-100px']); 
    }
    $html .= Html::endTag('div');
	
	$html .= ' <p><strong>Honorário</strong></p>';
    $html .= '<table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
            <th class="pdf total" style="width: 5px;"><strong>Uni./Ton./Hec.</strong></th>
            <th class="pdf total" style="width: 310px;"><strong>Posição da Tabela</strong></th>
            <th class="pdf total"><strong>Total</strong></th>
          </tr>
        </thead>
        <tbody>';

    if (!$model->person->isencao_honorario && Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) > 0) {
      
      if (!empty($model->tn)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->tn * $posicao_tabela['tn']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['tn'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tn), ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['tn'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tn * $posicao_tabela['tn']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->form)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->form * $posicao_tabela['form']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['form'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->form, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['form'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->form * $posicao_tabela['form']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->regime_normal)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->regime_normal * $posicao_tabela['regime_normal']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['regime_normal'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->regime_normal, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['regime_normal'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->regime_normal * $posicao_tabela['regime_normal']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->regime_especial)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->regime_especial * $posicao_tabela['regime_especial'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['regime_especial'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->regime_especial, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['regime_especial'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->regime_especial * $posicao_tabela['regime_especial']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->exprevio_comercial)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->exprevio_comercial * $posicao_tabela['exprevio_comercial'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['exprevio_comercial'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->exprevio_comercial, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['exprevio_comercial'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->exprevio_comercial * $posicao_tabela['exprevio_comercial']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->expedente_matricula)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->expedente_matricula * $posicao_tabela['expedente_matricula']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['expedente_matricula'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->expedente_matricula, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['expedente_matricula'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->expedente_matricula * $posicao_tabela['expedente_matricula']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->dv)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->dv * $posicao_tabela['dv']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['dv'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->dv, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['dv'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->dv * $posicao_tabela['dv']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->gti)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->gti * $posicao_tabela['gti'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['gti'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->gti, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['gti'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->gti * $posicao_tabela['gti']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->pl)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->pl * $posicao_tabela['pl'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['pl'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->pl, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['pl'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->pl * $posicao_tabela['pl']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->tce)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->tce * $posicao_tabela['tce'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['tce'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->tce, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['tce'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tce * $posicao_tabela['tce']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->acrescimo)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->acrescimo * $regimeConfig['valorPorItem'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Acréscimo', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->acrescimo, ['class' => 'pdf total']);
        $html .= Html::tag('td', '100 por item', ['class' => 'pdf total']);
        $html .= Html::tag('td', ($model->acrescimo * $regimeConfig['valorPorItem']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

       if (!empty($model->valorOutrosHonorarios())) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->valorOutrosHonorarios();
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Outros Honorários', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->valorOutrosHonorarios()), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }
	  
      $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario;

      if ($valor_tabel_honorario == 500) {
        $vthdesc = 'Valor Mininimo 500';
      } elseif (!empty($model->posicao_tabela)) {
        $vthdesc = $model->posicao_tabela;
      } elseif (empty($faturaProvisoria->regimeItem->forma)) {
        if (empty($faturaProvisoria->regimeItemItem->forma)) {
          $vthdesc = 'Valor Mininimo 500';
        } else {
          $vthdesc = $faturaProvisoria->regimeItemItem->forma;
        }
      } else {
        $vthdesc = $faturaProvisoria->regimeItem->forma;
      }

      $html .= Html::beginTag('tr');
      $html .= Html::tag('td', 'Tabela Honorário', ['class' => 'pdf desc']);
      $html .= Html::tag('td', ($valorBaseHonorario['tipo'] ? $formatter->asCurrency($valorBaseHonorario['valor']) : ''), ['class' => 'pdf total']);
      $html .= Html::tag('td', (!$valorBaseHonorario['tipo'] ? $valorBaseHonorario['valor'] : ''), ['class' => 'pdf total']);
      $html .= Html::tag('td', $vthdesc, ['class' => 'pdf desc']);
      $html .= Html::tag('td', $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario), ['class' => 'pdf total']);
      $html .= Html::endTag('tr'); 


      $a = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario;
      $total_honorario = $total_honorario + $a;
    }

    foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->id) as $key => $modelItem) {
        $total_outros = $total_outros +  $modelItem['valor'];
        $hta++;
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'], ['class' => 'pdf desc', 'colspan' => '']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($modelItem['valor']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
    }
    // Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)+Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id);
    $html .= '</tbody><tfoot>
          <tr>
            <td class="pdf desc" colspan="4"><strong>Total Geral Honorário</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_outros + Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr> 
		  <tr>
            <td class="pdf desc" colspan="4"><strong>Base tributável</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr>
		  <tr>
            <td class="pdf desc" colspan="4"><strong>IVA</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>TOTAL: ' . NumberHelper::ConvertToWords($total_outros+Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_outros+Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
            </tr>
        </tfoot>

      </table>'; 

  endif;
$html .= '<hr>';
if(!empty($model->faturaDebitoCliente->numero)):
$html .= Html::beginTag('div', ['id' => 'companys']);
    $html .= Html::tag('h1', 'NOTA DE DÉBITO Nº ' . $model->faturaDebitoCliente->numero . '/' . $model->faturaDebitoCliente->bas_ano_id,['style'=>'font-size:10px;']);
    $html .= Html::tag('div ', 'FD: '.$model->numero().' PROCESSO: ' . $model->processo->numero . '/' . $model->processo->bas_ano_id . ' NORD: ' . (empty($model->processo->nord->id) ? '' : $model->processo->nord->id), ['class' => 'address']);
    $html .= Html::tag('div', $fpRc['fatura_provisorias'] . ' ' . $fpRc['recibos'], ['class' => 'address']);
    $html .= Html::tag('div', 'Data: ' . $formatter->asDate($model->data), ['class' => 'address']);
    $html .= Html::endTag('div');
	
   
	
    $html .= ' <p><strong>Despesas</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>';

    foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->id) as $key => $modelItem) {
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
            <td class="pdf desc" colspan="2"><strong>TOTAL: ' . NumberHelper::ConvertToWords($total_despesas) . ' escudos</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></td>
          </tr>
          <tr>
            <th class="pdf desc" colspan="2"><strong>TOTAL GERAL: '.NumberHelper::ConvertToWords($model->valor).' escudos </strong> </th>
            <th class="pdf total"><strong>' . $formatter->asCurrency($model->valor) . '</strong></th>
          </tr>
        </tfoot>
      </table> ';

    $html .= '<p style="text-align: center;">O Despachante</p>
                  <p style=" text-align: center;">' . ($model->send ? 'CONFERÊNCIA ELETRÓNICA' : '.................................') . '</p>
                  <p style=" text-align: center;">/ ' . $formatter->asDate($model->data) . ' /</p>';

    $html  .= Html::endTag('div');
  endif;

    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->SetFooter('<p style="font-size:6px" >' . Yii::$app->params['copyright'] . '</p> ||Página {PAGENO}/{nbpg}'); // call methods or set any properties

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
    $model->send = $model->send == FaturaDefinitiva::POR_VALIDAR ? FaturaDefinitiva::POR_VALIDAR : FaturaDefinitiva::ENVIADO;
    $model->save(false);

    $posicao_tabela = Yii::$app->params['posicao_tabela'];
    $posicao_tabela_desc = Yii::$app->params['posicao_tabela_desc'];

    $formatter = \Yii::$app->formatter;
    $faturaProvisoria = FaturaProvisoria::find()
      ->where(['dsp_processo_id' => $model->dsp_processo_id])
      ->one();

    $fpRc = Yii::$app->FinQuery->fpFaturaDefinitiva($model->id);
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
    $valorBaseHonorario = Yii::$app->FinQuery->valorBaseHonorario($model->id);

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
    $html .= Html::tag('h1', 'FATURA DEFINITIVA  Nº ' . $model->numero . '/' . $model->bas_ano_id);
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

    $html .= '<p><strong>Honorário</strong></p>
       <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
            <th class="pdf total" style="width: 5px;"><strong>Uni./Ton./Hec.</strong></th>
            <th class="pdf total" style="width: 310px;"><strong>Posição da Tabela</strong></th>
            <th class="pdf total"><strong>Total</strong></th>
          </tr>
        </thead>
        <tbody>';

    if (!$model->person->isencao_honorario && Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) > 0) {
      
	 

      if (!empty($model->tn)) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->tn * $posicao_tabela['tn']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['tn'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tn), ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['tn'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tn * $posicao_tabela['tn']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->form)) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->form * $posicao_tabela['form']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['form'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->form, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['form'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->form * $posicao_tabela['form']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->regime_normal)) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->regime_normal * $posicao_tabela['regime_normal']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['regime_normal'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->regime_normal, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['regime_normal'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->regime_normal * $posicao_tabela['regime_normal']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->regime_especial)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->regime_especial * $posicao_tabela['regime_especial'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['regime_especial'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->regime_especial, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['regime_especial'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->regime_especial * $posicao_tabela['regime_especial']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->exprevio_comercial)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->exprevio_comercial * $posicao_tabela['exprevio_comercial'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['exprevio_comercial'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->exprevio_comercial, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['exprevio_comercial'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->exprevio_comercial * $posicao_tabela['exprevio_comercial']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->expedente_matricula)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->expedente_matricula * $posicao_tabela['expedente_matricula']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['expedente_matricula'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->expedente_matricula, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['expedente_matricula'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->expedente_matricula * $posicao_tabela['expedente_matricula']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->dv)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->dv * $posicao_tabela['dv']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['dv'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->dv, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['dv'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->dv * $posicao_tabela['dv']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->gti)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->gti * $posicao_tabela['gti'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['gti'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->gti, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['gti'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->gti * $posicao_tabela['gti']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->pl)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->pl * $posicao_tabela['pl'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['pl'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->pl, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['pl'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->pl * $posicao_tabela['pl']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->tce)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->tce * $posicao_tabela['tce'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['tce'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->tce, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['tce'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tce * $posicao_tabela['tce']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->acrescimo)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->acrescimo * $regimeConfig['valorPorItem'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Acréscimo', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->acrescimo, ['class' => 'pdf total']);
        $html .= Html::tag('td', '100 por item', ['class' => 'pdf total']);
        $html .= Html::tag('td', ($model->acrescimo * $regimeConfig['valorPorItem']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

       if (!empty($model->valorOutrosHonorarios())) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->valorOutrosHonorarios();
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Outros Honorários', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->valorOutrosHonorarios()), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario;

      if ($valor_tabel_honorario == 500) {
        $vthdesc = 'Valor Mininimo 500';
      } elseif (!empty($model->posicao_tabela)) {
        $vthdesc = $model->posicao_tabela;
      } elseif (empty($faturaProvisoria->regimeItem->forma)) {
        if (empty($faturaProvisoria->regimeItemItem->forma)) {
          $vthdesc = 'Valor Mininimo 500';
        } else {
          $vthdesc = $faturaProvisoria->regimeItemItem->forma;
        }
      } else {
        $vthdesc = $faturaProvisoria->regimeItem->forma;
      }

      $html .= Html::beginTag('tr');
      $html .= Html::tag('td', 'Tabela Honorário', ['class' => 'pdf desc']);
      $html .= Html::tag('td', ($valorBaseHonorario['tipo'] ? $formatter->asCurrency($valorBaseHonorario['valor']) : ''), ['class' => 'pdf total']);
      $html .= Html::tag('td', (!$valorBaseHonorario['tipo'] ? $valorBaseHonorario['valor'] : ''), ['class' => 'pdf total']);
      $html .= Html::tag('td', $vthdesc, ['class' => 'pdf desc']);
      $html .= Html::tag('td', $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario), ['class' => 'pdf total']);
      $html .= Html::endTag('tr');


      $a = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario;
      $total_honorario = $total_honorario + $a;
    }
    // Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)+Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id);
    $html .= '</tbody><tfoot>
          <tr>
            <td class="pdf desc" colspan="4"><strong>Total Geral Honorário</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>IVA sobre Honorário</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>SUB. TOTAL (Honorário mais IVA de Honorário) ' . NumberHelper::ConvertToWords(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
            </tr>
        </tfoot>

      </table>';


    $html .= '<p><strong>Outros</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>';

    foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->id) as $key => $modelItem) {
      $total_outros = $total_outros +  $modelItem['valor'];
      $hta++;
      $html .= Html::beginTag('tr');
      $html .= Html::tag('td', str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'], ['class' => 'pdf desc', 'colspan' => '2']);
      $html .= Html::tag('td', $formatter->asCurrency($modelItem['valor']), ['class' => 'pdf total']);
      $html .= Html::endTag('tr');
    }

    $html .= '</tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>TOTAL: ' . NumberHelper::ConvertToWords($total_outros) . 'escudos</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_outros) . '</strong></td>
          </tr>
        </tfoot>
      </table> ';

    $html .= '<hr><p><strong>Despesas</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>';

    foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->id) as $key => $modelItem) {
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
      for ($j = $hta; $j < 16; $j++) {
        $html .= '<tr>
            <td class="pdf desc"colspan="2">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
          </tr>';
      }
    }


    $html .= '</tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>TOTAL: ' . NumberHelper::ConvertToWords($total_despesas) . 'escudos</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></td>
          </tr>
          <tr>
            <th class="pdf desc" colspan="2"><strong>TOTAL GERAL ' . NumberHelper::ConvertToWords($model->valor) . 'escudos</strong></th>
            <th class="pdf total"><strong>' . $formatter->asCurrency($model->valor) . '</strong></th>
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
    $html .= Html::tag('h1', 'FATURA DEFINITIVA  Nº ' . $model->numero . '/' . $model->bas_ano_id);
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

    $html .= '<p><strong>Honorário</strong></p>
       <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
            <th class="pdf total" style="width: 5px;"><strong>Uni./Ton./Hec.</strong></th>
            <th class="pdf total" style="width: 310px;"><strong>Posição da Tabela</strong></th>
            <th class="pdf total"><strong>Total</strong></th>
          </tr>
        </thead>
        <tbody>';

    if (!$model->person->isencao_honorario && Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) > 0) {
      if (!empty($model->taxa_comunicaco)) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->taxa_comunicaco;
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['taxa_comunicaco'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', $formatter->asCurrency($model->taxa_comunicaco), ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['taxa_comunicaco'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->taxa_comunicaco), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }
	  if (!empty($model->impresso)) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->impresso;
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Impresso', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->impresso), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }
	  if (!empty($model->deslocacao_transporte)) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->deslocacao_transporte;
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Deslocação / Transporte', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->deslocacao_transporte), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->tn)) {
        // print_r('expression');die();
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->tn * $posicao_tabela['tn']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['tn'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tn), ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['tn'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tn * $posicao_tabela['tn']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->form)) {
        // print_r('expression');die();
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->form * $posicao_tabela['form']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['form'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->form, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['form'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->form * $posicao_tabela['form']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->regime_normal)) {
        // print_r('expression');die();
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->regime_normal * $posicao_tabela['regime_normal']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['regime_normal'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->regime_normal, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['regime_normal'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->regime_normal * $posicao_tabela['regime_normal']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->regime_especial)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->regime_especial * $posicao_tabela['regime_especial'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['regime_especial'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->regime_especial, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['regime_especial'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->regime_especial * $posicao_tabela['regime_especial']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->exprevio_comercial)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->exprevio_comercial * $posicao_tabela['exprevio_comercial'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['exprevio_comercial'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->exprevio_comercial, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['exprevio_comercial'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->exprevio_comercial * $posicao_tabela['exprevio_comercial']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->expedente_matricula)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->expedente_matricula * $posicao_tabela['expedente_matricula']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['expedente_matricula'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->expedente_matricula, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['expedente_matricula'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->expedente_matricula * $posicao_tabela['expedente_matricula']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->dv)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + ($model->dv * $posicao_tabela['dv']);
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['dv'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->dv, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['dv'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->dv * $posicao_tabela['dv']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->gti)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->gti * $posicao_tabela['gti'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['gti'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->gti, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['gti'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->gti * $posicao_tabela['gti']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->pl)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->pl * $posicao_tabela['pl'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['pl'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->pl, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['pl'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->pl * $posicao_tabela['pl']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->tce)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->tce * $posicao_tabela['tce'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', $posicao_tabela_desc['tce'], ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->tce, ['class' => 'pdf total']);
        $html .= Html::tag('td', $posicao_tabela['tce'], ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->tce * $posicao_tabela['tce']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      if (!empty($model->acrescimo)) {
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->acrescimo * $regimeConfig['valorPorItem'];
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Acréscimo', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $model->acrescimo, ['class' => 'pdf total']);
        $html .= Html::tag('td', '100 por item', ['class' => 'pdf total']);
        $html .= Html::tag('td', ($model->acrescimo * $regimeConfig['valorPorItem']), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

       if (!empty($model->valorOutrosHonorarios())) { 
        $hta = $hta + 1;
        $total_honorario = $total_honorario + $model->valorOutrosHonorarios();
        $html .= Html::beginTag('tr');
        $html .= Html::tag('td', 'Outros Honorários', ['class' => 'pdf desc']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', '', ['class' => 'pdf total']);
        $html .= Html::tag('td', $formatter->asCurrency($model->valorOutrosHonorarios()), ['class' => 'pdf total']);
        $html .= Html::endTag('tr');
      }

      $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario;

      if ($valor_tabel_honorario == 500) {
        $vthdesc = 'Valor Mininimo 500';
      } elseif (!empty($model->posicao_tabela)) {
        $vthdesc = $model->posicao_tabela;
      } elseif (empty($faturaProvisoria->regimeItem->forma)) {
        if (empty($faturaProvisoria->regimeItemItem->forma)) {
          $vthdesc = 'Valor Mininimo 500';
        } else {
          $vthdesc = $faturaProvisoria->regimeItemItem->forma;
        }
      } else {
        $vthdesc = $faturaProvisoria->regimeItem->forma;
      }

      $html .= Html::beginTag('tr');
      $html .= Html::tag('td', 'Tabela Honorário', ['class' => 'pdf desc']);
      $html .= Html::tag('td', ($valorBaseHonorario['tipo'] ? $formatter->asCurrency($valorBaseHonorario['valor']) : ''), ['class' => 'pdf total']);
      $html .= Html::tag('td', (!$valorBaseHonorario['tipo'] ? $valorBaseHonorario['valor'] : ''), ['class' => 'pdf total']);
      $html .= Html::tag('td', $vthdesc, ['class' => 'pdf desc']);
      $html .= Html::tag('td', $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario), ['class' => 'pdf total']);
      $html .= Html::endTag('tr');


      $a = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorario;
      $total_honorario = $total_honorario + $a;
    }
    // Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)+Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id);
    $html .= '</tbody><tfoot>
          <tr>
            <td class="pdf desc" colspan="4"><strong>Total Geral Honorário</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>IVA sobre Honorário</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>SUB. TOTAL (Honorário mais IVA de Honorário) ' . NumberHelper::ConvertToWords(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) . '</strong></td>
            </tr>
        </tfoot>

      </table>';


    $html .= '<p><strong>Outros</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>';
    $total_outros = 0;
    foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->id) as $key => $modelItem) {
      $total_outros = $total_outros +  $modelItem['valor'];
      $hta++;
      $html .= Html::beginTag('tr');
      $html .= Html::tag('td', str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'], ['class' => 'pdf desc', 'colspan' => '2']);
      $html .= Html::tag('td', $formatter->asCurrency($modelItem['valor']), ['class' => 'pdf total']);
      $html .= Html::endTag('tr');
    }

    $html .= '</tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>SUB. TOTAL: (Outros)' . NumberHelper::ConvertToWords($total_outros) . 'escudos</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_outros) . '</strong></td>
          </tr>
        </tfoot>
      </table> ';

    $html .= '<hr><p><strong>Despesas</strong></p>
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>';
    $total_despesas = 0;
    foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->id) as $key => $modelItem) {
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
            <td class="pdf desc" colspan="2"><strong>SUB. TOTAL: (Outros)' . NumberHelper::ConvertToWords($total_despesas) . 'escudos</strong></td>
            <td class="pdf total"><strong>' . $formatter->asCurrency($total_despesas) . '</strong></td>
          </tr>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Total Transportado</strong></th>
            <th class="pdf total"><strong>' . $formatter->asCurrency($model->valor) . '</strong></th>
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
    $searchModel = new FaturaDefinitivaSearch();

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
    $titleReport = 'Listagem de Fatura Provisoória de ' . $data['FaturaDefinitivaSearch']['beginDate'] . ' a ' . $data['FaturaDefinitivaSearch']['endDate'];

    $searchModel = new FaturaDefinitivaSearch($data['FaturaDefinitivaSearch']);
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



  /**
   * Lists all FaturaProvisoria models.
   * @return mixed
   */
  public function actionCreateFatura($id)
  {

    FaturaService::createFatura($id);
    FaturaService::createDebitoCliente($id); 
    Yii::$app->getSession()->setFlash('success', 'Fatura e Nota de debito de cliente atualizado com sucesso.'); 
    return $this->redirect(['view', 'id' => $id]);
  }
}
