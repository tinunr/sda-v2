<?php

namespace app\modules\cnt\components;


use yii;
use yii\base\Component;
use yii\helpers\Url;
use yii\db\Query;

use app\modules\cnt\models\Razao;
use app\modules\cnt\models\Documento;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\Pagamento;
use app\modules\fin\models\Recebimento;
use app\modules\fin\models\Transferencia;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\fin\models\Despesa; 
use app\modules\fin\models\FaturaEletronica; 
use app\modules\fin\models\FaturaDebitoCliente;  

/**
 * 
 */
class CntQuery extends Component
{
  /**
   * Lists all User models.
   * @return mixed
   */
  public function inContabilidade($cnt_documento_id, $documento_origem_id)
  {
    $inContabilidade = \app\modules\cnt\models\Razao::find()
      ->where(['cnt_documento_id' => $cnt_documento_id])
      ->andWhere(['status' => 1])
      ->andWhere(['documento_origem_id' => $documento_origem_id])
      ->one();
    if (!empty($inContabilidade->id)) {
      return $inContabilidade->id;
    }
    return false;
  }

  /**
   * Lists all User models.
   * @return mixed
   */
  public function inContabilidadeAtive($cnt_documento_id, $documento_origem_id)
  {
    if (($inContabilidade = \app\modules\cnt\models\Razao::find()
      ->where(['cnt_documento_id' => $cnt_documento_id])
      ->andWhere(['status' => 1])
      ->andWhere(['documento_origem_id' => $documento_origem_id])
      ->one()) != null) {
      if ($inContabilidade->status) {
        return true;
      }
    }

    return false;
  }


  /**
   * Lists all User models.
   * @return mixed
   */
  public function vereficarDebitoCredito($cnt_razao_id)
  {
    $razao = Razao::findOne($cnt_razao_id);
    $debito = (new \yii\db\Query())
      ->from('cnt_razao_item ')
      ->where(['cnt_plano_conta_id' => $cnt_razao_id])
      ->andWhere(['cnt_natureza_id' => 'D'])
      ->sum('valor');
    $crediro = (new \yii\db\Query())
      ->from('cnt_razao_item ')
      ->where(['cnt_plano_conta_id' => $cnt_razao_id])
      ->andWhere(['cnt_natureza_id' => 'C'])
      ->sum('valor');

    if ($debito != $crediro) {
      $razao->validacao_debito_credito = 0;
      $razao->save();
    }
  }


  /**
   * Lists all User models.
   * @return mixed
   */
  public function origem($cnt_razao_id)
  {
    $url = '';
    if (($model = Razao::findOne($cnt_razao_id)) != null) {
      if ($model->cnt_documento_id == Documento::FATURA_DEFINITIVA) {
        $url = Url::to(['/fin/fatura-definitiva/view', 'id' => $model->documento_origem_id]);
      }
      if (in_array($model->cnt_documento_id, [Documento::RECEBIMENTO_FATURA_PROVISORIA, Documento::RECEBIMENTO_ADIANTAMENTO, Documento::RECEBIMENTO_REEMBOLSO, Documento::RECEBIMENTO_TESOURARIO])) {
        $url = Url::to(['/fin/recebimento/view', 'id' => $model->documento_origem_id]);
      }
      if ($model->cnt_documento_id == Documento::PAGAMENTO) {
        $url = Url::to(['/fin/pagamento/view', 'id' => $model->documento_origem_id]);
      }
      if ($model->cnt_documento_id == Documento::MOVIMENTO_INTERNO) {
        $url = Url::to(['/fin/transferencia/view', 'id' => $model->documento_origem_id]);
      }
      if ($model->cnt_documento_id == Documento::MOVIMENTO_INTERNO) {
        $url = Url::to(['/fin/transferencia/view', 'id' => $model->documento_origem_id]);
      }
      if (in_array($model->cnt_documento_id, [Documento::DESPESA_FATURA_FORNECEDOR, Documento::FATURA_FORNECEDOR_INVESTIMENTO])) {
        $url = Url::to(['/fin/despesa/view', 'id' => $model->documento_origem_id]);
      }
    }
    return $url;
  }


  /**
   * Lists all User models.
   * @return mixed
   */
  public function origemData($cnt_razao_id)
  {
    $data = new \stdClass();
    if (($model = Razao::findOne($cnt_razao_id)) != null) {

      if ($model->cnt_documento_id == Documento::FACTURA ) {
        $origem = FaturaEletronica::findOne($model->documento_origem_id);
        $documento = Documento::findOne(Documento::FACTURA);
        $data->num_doc = $origem->numero . '/' . $origem->bas_ano_id;
        $data->origem = $origem->person->pais->abreviatura;
        $data->data = $origem->data;
        $data->valor = $origem->valor;
        $data->tp_doc = $documento->codigo;
        $data->nif = $origem->person->nif;
        $data->designacao = $origem->person->nome;
      }
       if ($model->cnt_documento_id == Documento::NOTA_DE_DEBITO_CLIENTE) {
        $origem = FaturaDebitoCliente::findOne($model->documento_origem_id);
        $documento = Documento::findOne(Documento::NOTA_DE_DEBITO_CLIENTE);
        $data->num_doc = $origem->numero . '/' . $origem->bas_ano_id;
        $data->origem = $origem->person->pais->abreviatura;
        $data->data = $origem->data;
        $data->valor = $origem->valor;
        $data->tp_doc = $documento->codigo;
        $data->nif = $origem->person->nif;
        $data->designacao = $origem->person->nome;
      }
       if ($model->cnt_documento_id == Documento::FATURA_DEFINITIVA) {
        $origem = FaturaDefinitiva::findOne($model->documento_origem_id);
        $documento = Documento::findOne(Documento::FATURA_DEFINITIVA);
        $data->num_doc = $origem->numero . '/' . $origem->bas_ano_id;
        $data->origem = $origem->person->pais->abreviatura;
        $data->data = $origem->data;
        $data->valor = $origem->valor;
        $data->tp_doc = $documento->codigo;
        $data->nif = $origem->person->nif;
        $data->designacao = $origem->person->nome;
      }
      if (in_array($model->cnt_documento_id, [Documento::RECEBIMENTO_FATURA_PROVISORIA, Documento::RECEBIMENTO_ADIANTAMENTO, Documento::RECEBIMENTO_REEMBOLSO, Documento::RECEBIMENTO_TESOURARIO])) {
        $origem = Recebimento::findOne($model->documento_origem_id);
        $documento = Documento::findOne(Documento::RECEBIMENTO_FATURA_PROVISORIA);
        $data->num_doc = $origem->numero . '/' . $origem->bas_ano_id;
        $data->origem = $origem->person->pais->abreviatura;
        $data->data = $origem->data;
        $data->valor = $origem->valor;
        $data->tp_doc = $documento->codigo;
        $data->nif = $origem->person->nif;
        $data->designacao = $origem->person->nome;
      }
      if ($model->cnt_documento_id == Documento::PAGAMENTO) {
        $documento = Documento::findOne(Documento::PAGAMENTO);
        $origem = Pagamento::findOne($model->documento_origem_id);
        $data->num_doc = $origem->numero . '/' . $origem->bas_ano_id;
        $data->origem = $origem->person->pais->abreviatura;
        $data->data = $origem->data;
        $data->valor = $origem->valor;
        $data->tp_doc = $documento->codigo;
        $data->nif = $origem->person->nif;
        $data->designacao = $origem->person->nome;
      }
      if ($model->cnt_documento_id == Documento::MOVIMENTO_INTERNO) {
        $documento = Documento::findOne(Documento::MOVIMENTO_INTERNO);
        $origem = Transferencia::findOne($model->documento_origem_id);
        $data->num_doc = $origem->numero . '' . $origem->bas_ano_id;
        $data->origem = $origem->person->pais->abreviatura;
        $data->data = $origem->data;
        $data->valor = $origem->valor;
        $data->tp_doc = $documento->codigo;
        $data->nif = $origem->person->nif;
        $data->designacao = $origem->person->nome;
      }
      if (in_array($model->cnt_documento_id, [Documento::DESPESA_FATURA_FORNECEDOR, Documento::FATURA_FORNECEDOR_INVESTIMENTO])) {
        $origem = Despesa::findOne($model->documento_origem_id);
        $documento = Documento::findOne(Documento::DESPESA_FATURA_FORNECEDOR);
        $data->num_doc = $origem->numero . '' . $origem->bas_ano_id;
        $data->origem =  $origem->person->pais->abreviatura;
        $data->data = $origem->data;
        $data->valor = $origem->valor;
        $data->tp_doc = $documento->codigo;
        $data->nif = $origem->person->nif;
        $data->designacao = $origem->person->nome;
      }
    }
    return $data;
  }


  /**
   * Lists all User models.
   * @return mixed
   */
  public function listFaruraDefinitivaItems($fin_fatura_definitiva_id)
  {
    return (new \yii\db\Query())
      ->select(['numero' => "CONCAT(B.numero, '/', B.bas_ano_id)", 'A.id', 'C.descricao', 'cnt_plano_terceiro_id' => 'B.dsp_person_id', 'C.cnt_plano_conta_id', 'C.cnt_plano_iva_id', 'C.cnt_plano_fluxo_caixa_id', 'A.valor'])
      ->from('fin_fatura_definitiva_item A')
      ->leftJoin('fin_fatura_definitiva B', 'B.id = A.fin_fatura_definitiva_id')
      ->innerJoin('dsp_item C', 'A.dsp_item_id = C.id')
      ->where('B.status=1')
      ->andWhere(['B.id' => $fin_fatura_definitiva_id])
      ->all();
  }


  /**
   * Lists all User models.
   * @return mixed
   */
  public function listDespesaItems($fin_despesa_id)
  {
    return (new \yii\db\Query())
      ->select(['numero' => "CONCAT(B.numero, '/', B.bas_ano_id)", 'A.id', 'C.descricao', 'cnt_plano_terceiro_id' => 'B.dsp_person_id', 'C.cnt_plano_conta_id', 'C.cnt_plano_iva_id', 'C.cnt_plano_fluxo_caixa_id', 'A.valor'])
      ->from('fin_despesa_item A')
      ->leftJoin('fin_despesa B', 'B.id = A.fin_despesa_id')
      ->innerJoin('dsp_item C', 'A.item_id = C.id')
      ->where('B.status=1')
      ->andWhere(['B.id' => $fin_despesa_id])
      ->all();
  }




  /**
   * Lists all User models.
   * @return mixed
   */
  public function listRecebimentoItems($id)
  {
    return (new \yii\db\Query())
      ->select(['A.id', 'numero' => "CONCAT(B.numero, '/', B.bas_ano_id)", 'C.descricao', 'cnt_plano_terceiro_id' => 'B.dsp_person_id', 'C.cnt_plano_conta_id', 'C.cnt_plano_iva_id', 'C.cnt_plano_fluxo_caixa_id', 'valor' => 'A.valor_recebido'])
      ->from('fin_recebimento_item A')
      ->leftJoin('fin_recebimento B', 'B.id = A.fin_recebimento_id')
      ->leftJoin('dsp_item C', 'C.id = A.dsp_item_id')
      ->where(['B.id' => $id])
      ->all();
  }

  /**
   * Lists all User models.
   * @return mixed
   */
  public function listPagamentoDespesas($id)
  {
    return (new \yii\db\Query())
      ->select([
        'B.id',
        'fin_despesa_id' => 'A.id',
        'A.id',
        'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)",
        'cnt_plano_terceiro_id' => 'C.dsp_person_id',
        'valor' => 'B.valor',
        'descricao' => 'A.numero',
        'A.cnt_documento_id',
        'D.cnt_plano_fluxo_caixa_id',
        'documento_origem_tipo' => 'D.codigo'
      ])
      ->from('fin_despesa A')
      ->leftJoin('fin_pagamento_item B', 'B.fin_despesa_id = A.id')
      ->leftJoin('fin_pagamento C', 'C.id = B.fin_pagamento_id')
      ->leftJoin('cnt_documento D', 'D.id = A.cnt_documento_id')
      ->where(['C.id' => $id])
      ->andWhere(['D.cnt_documento_tipo_id' => 1])
      ->all();
  }




  /**
   * Lists all User models.
   * @return mixed
   */
  public function listPagamentoDespesasClienteItemsProcesso($id)
  {
    return (new \yii\db\Query())
      ->select([
        'A.id',
        'fin_despesa_id' => 'B.id',
        'numero' => "CONCAT(B.numero, '/', B.bas_ano_id)",
        'descricao' => "CONCAT(B.numero, '/', B.bas_ano_id)", 'cnt_plano_terceiro_id' => 'F.nome_fatura',
        'E.cnt_plano_conta_id',
        'E.cnt_plano_iva_id',
        'E.cnt_plano_fluxo_caixa_id',
        'valor' => 'C.valor',
        'documento_origem_tipo' => 'G.codigo'
      ])
      ->from('fin_despesa_item A')
      ->leftJoin('fin_despesa B', 'B.id = A.fin_despesa_id')
      ->leftJoin('fin_pagamento_item C', 'C.fin_despesa_id = B.id')
      ->leftJoin('fin_pagamento D', 'D.id = C.fin_pagamento_id')
      ->leftJoin('dsp_item E', 'E.id = A.item_id')
      ->leftJoin('dsp_processo F', 'F.id = B.dsp_processo_id')
      ->leftJoin('cnt_documento G', 'G.id = B.cnt_documento_id')
      ->where(['D.id' => $id])
      ->andWhere(['>', 'B.dsp_processo_id', 0])
      ->groupBy(['B.id'])
      ->all();
  }


  /**
   * Lists all User models.
   * @return mixed
   */
  public function listPagamentoDespesasItems($id)
  {
    return (new \yii\db\Query())
      ->select(['A.id', 'numero' => "CONCAT(D.numero, '/', D.bas_ano_id)", 'E.descricao', 'cnt_plano_terceiro_id' => 'D.dsp_person_id', 'E.cnt_plano_conta_id', 'E.cnt_plano_iva_id', 'E.cnt_plano_fluxo_caixa_id', 'valor' => 'C.valor'])
      ->from('fin_despesa_item A')
      ->leftJoin('fin_despesa B', 'B.id = A.fin_despesa_id AND B.cnt_documento_id=10')
      ->leftJoin('fin_pagamento_item C', 'C.fin_despesa_id = B.id')
      ->leftJoin('fin_pagamento D', 'D.id = C.fin_pagamento_id')
      ->leftJoin('dsp_item E', 'E.id = A.item_id')
      ->where(['D.id' => $id])
      ->all();
  }


  /**
   * Lists all User models.
   * @return mixed
   */
  public function listPagamentoDespesasAgenciaItems($id)
  {
    return (new \yii\db\Query())
      ->select([
        'B.id',
        'fin_despesa_id' => 'B.id',
        'numero' => "CONCAT(B.numero, '/', B.bas_ano_id)",
        'descricao' => 'A.item_descricao',
        'cnt_plano_terceiro_id' => 'A.cnt_plano_terceiro_id',
        'E.cnt_plano_conta_id',
        'E.cnt_plano_iva_id',
        'E.cnt_plano_fluxo_caixa_id',
        'valor' => 'A.valor',
        'valor_iva' => 'A.valor_iva',
        'documento_origem_tipo' => 'F.codigo',
      ])
      ->from('fin_despesa_item A')
      ->leftJoin('fin_despesa B', 'B.id = A.fin_despesa_id')
      ->leftJoin('fin_pagamento_item C', 'C.fin_despesa_id = B.id')
      ->leftJoin('fin_pagamento D', 'D.id = C.fin_pagamento_id')
      ->leftJoin('dsp_item E', 'E.id = A.item_id')
      ->leftJoin('cnt_documento F', 'F.id = B.cnt_documento_id')
      ->where(['D.id' => $id])
      ->andWhere(['F.cnt_documento_tipo_id' => 2])
      ->all();
  }



  /**
   * Despesa de cliente não comtabilistico  sem processo
   * @return mixed
   */
  public function listPagamentoDespesasClienteItems($id)
  {
    return (new \yii\db\Query())
      ->select([
        'A.id',
        'fin_despesa_id' => 'B.id',
        'numero' => "CONCAT(B.numero, '/', B.bas_ano_id)",
        'descricao' => "CONCAT(B.numero, '/', B.bas_ano_id)",
        'cnt_plano_terceiro_id' => 'A.cnt_plano_terceiro_id',
        'E.cnt_plano_conta_id',
        'E.cnt_plano_iva_id',
        'E.cnt_plano_fluxo_caixa_id',
        'valor' => 'C.valor',
        'documento_origem_tipo' => 'G.codigo',
      ])
      ->from('fin_despesa_item A')
      ->leftJoin('fin_despesa B', 'B.id = A.fin_despesa_id')
      ->leftJoin('fin_pagamento_item C', 'C.fin_despesa_id = B.id')
      ->leftJoin('fin_pagamento D', 'D.id = C.fin_pagamento_id')
      ->leftJoin('dsp_item E', 'E.id = A.item_id')
      ->leftJoin('cnt_documento G', 'G.id = B.cnt_documento_id')
      ->leftJoin('fin_recebimento H', 'H.id = B.fin_recebimento_id')
      ->where(['D.id' => $id])
      ->andWhere(['H.fin_recebimento_tipo_id' => 2])
      ->andWhere(['B.dsp_processo_id' => NULL])
      ->groupBy(['B.id'])
      ->all();
  }












  /**
   * Lists all User models.
   * @return mixed
   */
  public function listRazaoItems($cnt_documento_id, $documento_origem_id)
  {
    if ($cnt_documento_id == Documento::FATURA_DEFINITIVA) {
      $origem = FaturaDefinitiva::findOne($documento_origem_id);
      $query = (new \yii\db\Query())
        ->select(['numero' => "CONCAT(B.numero, '/', B.bas_ano_id)", 'A.id', 'C.descricao', 'cnt_plano_terceiro_id' => 'B.dsp_person_id', 'C.cnt_plano_conta_id', 'C.cnt_plano_iva_id', 'C.cnt_plano_fluxo_caixa_id', 'A.valor'])
        ->from('fin_fatura_definitiva_item A')
        ->leftJoin('fin_fatura_definitiva B', 'B.id = A.fin_fatura_definitiva_id')
        ->innerJoin('dsp_item C', 'A.dsp_item_id = C.id')
        ->where('B.status=1')
        ->andWhere(['B.id' => $documento_origem_id])
        ->all();
    }
    if ($cnt_documento_id == Documento::RECEBIMENTO) {
      $command = Yii::$app->db->createCommand('SELECT A.id, A.descricao , SUM(B.valor) as valor
                       FROM fin_pagamento A
                  LEFT JOIN fin_pagamento_item B
                         ON(A.id = B.fin_pagamento_id )
                      WHERE A.id =:documento_origem_id AND A.status =:estado');
      $command->bindValues([':documento_origem_id' => $documento_origem_id, ':estado' => 1]);
      $result = $command->queryAll();
    }
    if ($cnt_documento_id == Documento::PAGAMENTO) {
      $command = Yii::$app->db->createCommand('SELECT A.id, A.descricao , SUM(B.valor) as valor
                       FROM fin_pagamento A
                  LEFT JOIN fin_pagamento_item B
                         ON(A.id = B.fin_pagamento_id )
                      WHERE A.id =:documento_origem_id AND A.status =:estado');
      $command->bindValues([':documento_origem_id' => $documento_origem_id, ':estado' => 1]);
      $result = $command->queryAll();
    }

    //MI
    if ($cnt_documento_id == Documento::MOVIMENTO_INTERNO) {
      $command = Yii::$app->db->createCommand('SELECT A.id, A.descricao , SUM(B.valor) as valor
                       FROM fin_pagamento A
                  LEFT JOIN fin_pagamento_item B
                         ON(A.id = B.fin_pagamento_id )
                      WHERE A.id =:documento_origem_id AND A.status =:estado');
      $command->bindValues([':documento_origem_id' => $documento_origem_id, ':estado' => 1]);
      $result = $command->queryAll();
    }


    return $query;
  }



  /**
   * Lists all User models.
   * @return mixed
   */
  public function modelo106Cliente($bas_ano_id, $bas_mes_id)
  {
    $query = new Query();
    $query->select([
      'numero' => 'A.documento_origem_numero', 'cnt_razao_item_id' => 'A.id', 'cnt_razao_id' => 'A.cnt_razao_id', 'vl_fatura' => 'B.valor_credito', 'vl_base_incid' => 'SUM(A.valor)', 'tx_iva' => '(C.taxa*100)', 'iva_liq' => '(SUM(A.valor)*C.taxa)', 'nao_liq_imp' => 'A.id', 'linha_dest_mod' => 'D.numero_destino', 'A.cnt_plano_conta_id', 'data' => 'B.documento_origem_data'
    ])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id=A.cnt_razao_id')
      ->leftJoin('cnt_plano_iva C', 'C.id=A.cnt_plano_iva_id')
      ->leftJoin('cnt_tipologia D', 'D.id=C.cnt_tipologia_id')
      ->where('B.status=1')
      ->andWhere(['A.cnt_plano_conta_id' => [721, 722]])
      ->andWhere(['B.bas_ano_id' => $bas_ano_id])
      ->andWhere(['B.bas_mes_id' => $bas_mes_id])
      ->groupBy('A.cnt_plano_conta_id,A.cnt_razao_id')
      ->orderBy('B.numero');

    return $query->all();
  }




  /**
   * Lists all User models.
   * @return mixed
   */
  public function modelo106Fornecedor($bas_ano_id, $bas_mes_id)
  {
    $query = new Query();
    $query->select([
      'numero' => 'A.documento_origem_numero', 'cnt_razao_item_id' => 'A.id', 'cnt_razao_id' => 'A.cnt_razao_id', 'vl_fatura' => 'B.valor_credito', 'vl_base_incid' => 'cnt_106_fornecedor_vl_base_incid(A.cnt_plano_conta_id, A.cnt_razao_id, A.cnt_plano_iva_id)', 'tx_iva' => '(C.taxa*100)', 'iva_sup' => 'SUM(A.valor)', 'direito_ded' => '(C.deducao*100)', 'iva_ded' => '(SUM(A.valor)*C.deducao)', 'tipologia' => 'D.abreviatura', 'linha_dest_mod' => 'D.numero_destino', 'A.cnt_plano_conta_id', 'B.documento_origem_data', 'A.documento_origem_tipo', 'A.cnt_plano_iva_id'
    ])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id=A.cnt_razao_id')
      ->leftJoin('cnt_plano_iva C', 'C.id=A.cnt_plano_iva_id')
      ->leftJoin('cnt_tipologia D', 'D.id=C.cnt_tipologia_id')
      ->leftJoin('cnt_plano_conta E', 'E.id=A.cnt_plano_conta_id')
      ->where('B.status=1')
      ->andWhere('E.is_plano_conta_iva=1')
      ->andWhere('C.cnt_plano_conta_id>0')
      ->andWhere('A.dsp_item_id>0')
      ->andWhere(['B.bas_ano_id' => $bas_ano_id])
      ->andWhere(['B.bas_mes_id' => $bas_mes_id])
      ->groupBy('A.cnt_razao_id,A.cnt_plano_conta_id,A.cnt_plano_iva_id')
      ->orderBy('B.numero');
    // print_r($query->createCommand()->getRawSql());die();
    return $query->all();
  }



  /**
   * @return \yii\db\ActiveQuery
   */
  public function getAdao($cnt_plano_conta_id)
  {
    $array = [];
    $strlen = strlen($cnt_plano_conta_id);
    for ($i = $strlen; $i >= 0; $i--) {
      if ($cnt_plano_conta_id > 0) {
        $array[$i] = $cnt_plano_conta_id;
      }
      $cnt_plano_conta_id = substr($cnt_plano_conta_id, 0, -1);
    }

    return $array;
  }



  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPaisPlanoFuxoCaixa($cnt_plano_conta_id)
  {
    $array = [];
    $planoConta = PlanoFluxoCaixa::findOne($cnt_plano_conta_id);
    $array[$planoConta->id] = [
      'id' => $planoConta->id,
      'parent_id' => $planoConta->cnt_plano_fluxo_caixa_id,
      'descricao' => $planoConta->descricao,
    ];
    $pais =  PlanoFluxoCaixa::find()->where(['id' => $planoConta->cnt_plano_fluxo_caixa_id])->all();
    foreach ($pais as $key => $pai) {
      $array[] = $this->getPaisPlanoFuxoCaixa($pai->id);
    }
    print_r($this->buildTree($array));
    return $array;
  }


  /**
   * @return \yii\db\ActiveQuery
   */
  public function getFilosPlanoFuxoCaixa($cnt_plano_conta_id)
  {
    $conta = PlanoFluxoCaixa::findOne($cnt_plano_conta_id);
    $array[$cnt_plano_conta_id] = [
      'id' => $conta->id,
      'parent_id' => $conta->cnt_plano_fluxo_caixa_id,
      'descricao' => $conta->descricao,
    ];
    $filhos =  PlanoFluxoCaixa::find()
      ->where(['cnt_plano_fluxo_caixa_id' => $conta->id])
      ->orderBy('codigo')
      ->all();
    foreach ($filhos as $filho) {

      $array[] = $this->getFilosPlanoFuxoCaixa($filho->id);
      // print_r($array);die();
    }
    print_r($array);
    die();
    return $this->buildTree($array);
  }



  /**
   * @return \yii\db\ActiveQuery
   */
  public function buildTree(array $elements, $parentId = 0)
  {
    $branch = array();
    // print_r($elements);die();
    foreach ($elements as $element) {
      if (!empty($element['parent_id']) && $element['parent_id'] == $parentId) {
        $children = $this->buildTree($elements, $element['id']);
        if ($children) {
          $element['children'] = $children;
        }
        $branch[] = $element;
      }
    }

    return $branch;
  }



  /**
   * @return \yii\db\ActiveQuery
   */
  public function orderEva($array)
  {
    $data = [];
    foreach ($array as $key => $value) {
      # if is am array
      if (is_array($value)) {
        # we need to loop through it
        $this->orderEva($value);
      } else {
        $data[$value] = $value;
      }
    }

    return $data;
  }







  /**
   * @return \yii\db\ActiveQuery
   */
  public function getDebitoAtaulValor($cnt_plano_conta_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $array= 0;
    $query = new Query();
    $query->select(['SUM(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_natureza_id' => 'D'])
      ->andWhere(['A.cnt_plano_conta_id' => $cnt_plano_conta_id])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['MONTH(B.documento_origem_data)' => $bas_mes_id]);
    if ($bas_template_id == 2) {
      $query = $query->andWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
    }
    $query = $query->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);



    return $query->scalar();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCreditoAtaulValor($cnt_plano_conta_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $array= 0;
    $query = new Query();
    $query->select(['SUM(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_natureza_id' => 'C'])
      ->andWhere(['A.cnt_plano_conta_id' => $cnt_plano_conta_id])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['MONTH(B.documento_origem_data)' => $bas_mes_id]);
    if ($bas_template_id == 2) {
      $query = $query->andWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
    }
    $query = $query->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);

    return $query->scalar();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getDebitoAnteriorValor($cnt_plano_conta_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $array= 0;
    $query = new Query();
    $query->select(['SUM(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_natureza_id' => 'D'])
      ->andWhere(['A.cnt_plano_conta_id' => $cnt_plano_conta_id])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $bas_mes_id])
      ->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);
    if ($bas_template_id == 2) {
      $query = $query->andWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
    }
    $query = $query->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);

    return $query->scalar();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCrediroAnteriorValor($cnt_plano_conta_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $array= 0;
    $query = new Query();
    $query->select([
      'SUM(A.valor)'
    ])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_natureza_id' => 'C'])
      ->andWhere(['A.cnt_plano_conta_id' => $cnt_plano_conta_id])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $bas_mes_id])
      ->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);
    if ($bas_template_id == 2) {
      $query = $query->andWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
    }
    $query = $query->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);

    return $query->scalar();
  }


  /**
   * @return \yii\db\ActiveQuery
   */
  public function getDebitoAnterior($cnt_plano_conta_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    $planoConta = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();
    $query = new Query();
    $query->select(['sum(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id=A.cnt_razao_id')
      ->leftJoin('cnt_plano_conta C', 'C.id=A.cnt_plano_conta_id')
      ->where('B.status=1')
      ->andWhere(['A.cnt_natureza_id' => 'D'])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $bas_mes_id])
      ->andWhere(['LIKE', 'C.path', $planoConta['path'] . '%', false]);
    return $query->scalar();
  }
  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCreditoAnterior($cnt_plano_conta_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    $planoConta = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();
    $query = new Query();
    $query->select(['sum(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id=A.cnt_razao_id')
      ->leftJoin('cnt_plano_conta C', 'C.id=A.cnt_plano_conta_id')
      ->where('B.status=1')
      ->andWhere(['A.cnt_natureza_id' => 'C'])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $bas_mes_id])
      ->andWhere(['LIKE', 'C.path', $planoConta['path'] . '%', false]);
    return $query->scalar();
  }
  /**
   * @return \yii\db\ActiveQuery
   */
  public function getDebitoAtual($path, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $planoConta = PlanoConta::find()->where(['id'=>$cnt_plano_conta_id])->asArray()->one();
    $query = new Query();
    $query->select(['sum(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id=A.cnt_razao_id')
      ->leftJoin('cnt_plano_conta C', 'C.id=A.cnt_plano_conta_id')
      ->where('B.status=1')
      ->andWhere(['A.cnt_natureza_id' => 'D'])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['MONTH(B.documento_origem_data)' => $bas_mes_id])
      ->andWhere(['LIKE', 'C.path', $path . '%', false]);
    // print_r($query->createCommand()->sql);die();
    return $query->scalar();
  }
  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCreditoAtual($cnt_plano_conta_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    $planoConta = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();
    $query = new Query();
    $query->select(['sum(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id=A.cnt_razao_id')
      ->leftJoin('cnt_plano_conta C', 'C.id=A.cnt_plano_conta_id')
      ->where('B.status=1')
      ->andWhere(['A.cnt_natureza_id' => 'C'])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['MONTH(B.documento_origem_data)' => $bas_mes_id])
      ->andWhere(['LIKE', 'C.path', $planoConta['path'] . '%', false]);
    return $query->scalar();
  }



  /**
   * @return \yii\db\ActiveQuery
   */
  public function getDebitoAtaulValorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $array= 0;
    $query = new Query();
    $query->select(['SUM(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_natureza_id' => 'D'])
      ->andWhere(['A.cnt_plano_fluxo_caixa_id' => $cnt_plano_fluxo_caixa_id])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['MONTH(B.documento_origem_data)' => $bas_mes_id]);
    if ($bas_template_id == 2) {
      $query = $query->andWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
    }
    $query = $query->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);



    return $query->scalar();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCreditoAtaulValorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $array= 0;
    $query = new Query();
    $query->select(['SUM(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_natureza_id' => 'C'])
      ->andWhere(['A.cnt_plano_fluxo_caixa_id' => $cnt_plano_fluxo_caixa_id])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['MONTH(B.documento_origem_data)' => $bas_mes_id]);
    if ($bas_template_id == 2) {
      $query = $query->andWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
    }
    $query = $query->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);

    return $query->scalar();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getDebitoAnteriorValorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $array= 0;
    $query = new Query();
    $query->select(['SUM(A.valor)'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_natureza_id' => 'D'])
      ->andWhere(['A.cnt_plano_fluxo_caixa_id' => $cnt_plano_fluxo_caixa_id])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $bas_mes_id])
      ->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);
    if ($bas_template_id == 2) {
      $query = $query->andWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
    }
    $query = $query->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);

    return $query->scalar();
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCrediroAnteriorValorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    // $array= 0;
    $query = new Query();
    $query->select([
      'SUM(A.valor)'
    ])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_natureza_id' => 'C'])
      ->andWhere(['A.cnt_plano_fluxo_caixa_id' => $cnt_plano_fluxo_caixa_id])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $bas_mes_id])
      ->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);
    if ($bas_template_id == 2) {
      $query = $query->andWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
    }
    $query = $query->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);

    return $query->scalar();
  }


  /**
   * @return \yii\db\ActiveQuery
   */
  public function getDebitoAnteriorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    $valor = 0;
    $planoConta = PlanoFluxoCaixa::findOne($cnt_plano_fluxo_caixa_id);
    $valor = $valor + $this->getDebitoAnteriorValorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id);
    $PlanoFluxoCaixaFilhos =  PlanoFluxoCaixa::find()->where(['cnt_plano_fluxo_caixa_id' => $planoConta->id])->all();
    foreach ($PlanoFluxoCaixaFilhos as $key => $filho) {
      $valor = $valor + $this->getDebitoAnteriorPFC($filho->id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id);
    }

    return $valor;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCreditoAnteriorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    $valor = 0;
    $planoConta = PlanoFluxoCaixa::findOne($cnt_plano_fluxo_caixa_id);
    $valor = $valor + $this->getCrediroAnteriorValorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id);
    $planoContaFilhos =  PlanoFluxoCaixa::find()->where(['cnt_plano_fluxo_caixa_id' => $planoConta->id])->all();
    foreach ($planoContaFilhos as $key => $filho) {
      $valor = $valor + $this->getCreditoAnteriorPFC($filho->id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id);
    }

    return $valor;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getDebitoAtualPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    $valor = 0;
    $planoConta = PlanoFluxoCaixa::findOne($cnt_plano_fluxo_caixa_id);
    $valor = $valor + $this->getDebitoAtaulValorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id);
    $planoContaFilhos =  PlanoFluxoCaixa::find()->where(['cnt_plano_fluxo_caixa_id' => $planoConta->id])->all();
    foreach ($planoContaFilhos as $key => $filho) {
      $valor = $valor + $this->getDebitoAtualPFC($filho->id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id);
    }

    return $valor;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCreditoAtualPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id)
  {
    $valor = 0;
    $planoConta = PlanoFluxoCaixa::findOne($cnt_plano_fluxo_caixa_id);
    $valor = $valor + $this->getCreditoAtaulValorPFC($cnt_plano_fluxo_caixa_id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id);
    $planoContaFilhos =  PlanoFluxoCaixa::find()->where(['cnt_plano_fluxo_caixa_id' => $planoConta->id])->all();
    foreach ($planoContaFilhos as $key => $filho) {
      $valor = $valor + $this->getCreditoAtualPFC($filho->id, $bas_ano_id, $bas_mes_id, $cnt_plano_terceiro_id, $bas_template_id);
    }

    return $valor;
  }




  /**
   * @return \yii\db\ActiveQuery
   */
  public function getValorBTCliente($cnt_modelo_106_id, $linha_dest_mod)
  {
    $query = new Query();
    $query->select([
      'SUM(A.vl_base_incid)'
    ])
      ->from('cnt_modelo_106_cliente A')
      ->leftJoin('cnt_modelo_106 B', 'B.id = A.cnt_modelo_106_id')
      ->where(['A.cnt_modelo_106_id' => $cnt_modelo_106_id])
      ->andWhere(['A.linha_dest_mod' => $linha_dest_mod]);
    return $query->scalar() ? $query->scalar() : 0;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getValorBTFornecedor($cnt_modelo_106_id, $linha_dest_mod)
  {
    $query = new Query();
    $query->select([
      'SUM(A.vl_base_incid)'
    ])
      ->from('cnt_modelo_106_fornecedor A')
      ->leftJoin('cnt_modelo_106 B', 'B.id = A.cnt_modelo_106_id')
      ->where(['A.cnt_modelo_106_id' => $cnt_modelo_106_id])
      ->andWhere(['A.linha_dest_mod' => $linha_dest_mod]);
    return $query->scalar() ? $query->scalar() : 0;
  }


  /**
   * @return \yii\db\ActiveQuery
   */
  public function getValorIFSPFornecedor($cnt_modelo_106_id, $linha_dest_mod)
  {
    $query = new Query();
    $query->select([
      'SUM(A.iva_ded)'
    ])
      ->from('cnt_modelo_106_fornecedor A')
      ->leftJoin('cnt_modelo_106 B', 'B.id = A.cnt_modelo_106_id')
      ->where(['A.cnt_modelo_106_id' => $cnt_modelo_106_id])
      ->andWhere(['A.linha_dest_mod' => $linha_dest_mod]);
    return $query->scalar() ? $query->scalar() : 0;
  }



  /**
   * @return \yii\db\ActiveQuery
   */
  public function getValorIFECliente($cnt_modelo_106_id, $linha_dest_mod)
  {
    $query = new Query();
    $query->select([
      'SUM(A.iva_liq)'
    ])
      ->from('cnt_modelo_106_cliente A')
      ->leftJoin('cnt_modelo_106 B', 'B.id = A.cnt_modelo_106_id')
      ->where(['A.cnt_modelo_106_id' => $cnt_modelo_106_id])
      ->andWhere(['A.linha_dest_mod' => $linha_dest_mod]);
    return $query->scalar() ? $query->scalar() : 0;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getValorIFEFornecedor($cnt_modelo_106_id, $linha_dest_mod)
  {
    // $query = new Query();
    // $query->select([
    //   'SUM(A.iva_ded)'])
    // ->from('cnt_modelo_106_fornecedor A')
    // ->leftJoin('cnt_modelo_106 B','B.id = A.cnt_modelo_106_id')
    // ->where(['A.cnt_modelo_106_id'=>$cnt_modelo_106_id])
    // ->andWhere(['A.linha_dest_mod'=>$linha_dest_mod]);
    return 0;
  }




  /**
   * @return \yii\db\ActiveQuery
   */
  public function queryExtrato(array $data)
  {
    // print_r($data);die();
    $query = new Query();
    $query->select([
      'A.id',
      'B.bas_mes_id',
      'B.bas_ano_id',
      'A.cnt_plano_conta_id',
      'descricao' => 'A.descricao',
      'B.cnt_diario_id',
      'debito' => new \yii\db\Expression("CASE WHEN  A.cnt_natureza_id ='D' THEN A.valor ELSE 0 END"),
      'credito' => new \yii\db\Expression("CASE WHEN  A.cnt_natureza_id ='C' THEN A.valor ELSE 0 END"),
      'cnt_documento_id',
      'data' => 'B.documento_origem_data',
      'num_doc' => 'B.numero',
      'terceiro' => 'A.cnt_plano_terceiro_id'
    ])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['A.cnt_plano_conta_id' => $data['cnt_plano_conta_id']])
      ->andWhere(['B.bas_ano_id' => $data['bas_ano_id']])
      ->andWhere(['>=', 'B.bas_mes_id', $data['begin_mes']])
      ->andWhere(['<=', 'B.bas_mes_id', $data['end_mes']])
      ->orderBy('B.documento_origem_data');
    $query->andFilterWhere([
      'A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id'],
    ]);

    return $query;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function queryExtratoSaldoMesAnterior(array $data)
  {
    $debito = 0;
    $credito = 0;
    $mes = (int)$data['begin_mes'] - 1;
    $query = new Query();
    $query->select(['debito' => new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='D' THEN SUM(A.valor) ELSE 0 END"), 'credito' => new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='C' THEN SUM(A.valor) ELSE 0 END")])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['B.bas_ano_id' => $data['bas_ano_id']])
      ->andWhere(['<=', 'B.bas_mes_id', $mes])
      ->andWhere(['A.cnt_plano_conta_id' => $data['cnt_plano_conta_id']])
      ->groupBy('A.cnt_natureza_id')
      ->orderBy('B.documento_origem_data');
    $query->andFilterWhere([
      'A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id'],
    ]);
    $data = $query->All();
    foreach ($data as $key => $value) {
      $debito = $debito + $value['debito'];
      $credito = $credito + $value['credito'];
    }
    return $debito - $credito;
  }


  /**
   * @return \yii\db\ActiveQuery
   */
  public function queryExtratoExcel(array $data)
  {
    $query = new Query();
    $query->select([
      'A.cnt_plano_conta_id',
      'cnt_plano_conta_descricao' => 'C.descricao',
      'descricao' => 'A.descricao',
      'B.cnt_diario_id',
      'cnt_diario_descricao' => 'E.descricao',
      'debito' => new \yii\db\Expression("CASE WHEN  A.cnt_natureza_id ='D' THEN A.valor ELSE 0 END"),
      'credito' => new \yii\db\Expression("CASE WHEN  A.cnt_natureza_id ='C' THEN A.valor ELSE 0 END"),
      'cnt_documento_id',
      'cnt_documento_descricao' => 'F.descricao',
      'data' => 'B.documento_origem_data',
      'num_doc' => 'B.numero',
      'terceiro' => 'A.cnt_plano_terceiro_id',
      'cnt_plano_terceiro_nome' => 'D.nome'
    ])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->leftJoin('cnt_plano_conta C', 'C.id = A.cnt_plano_conta_id')
      ->leftJoin('dsp_person D', 'D.id = A.cnt_plano_terceiro_id')
      ->leftJoin('cnt_diario E', 'E.id = B.cnt_diario_id')
      ->leftJoin('cnt_documento F', 'F.id = B.cnt_documento_id')
      ->where(['B.status' => 1])
      ->andWhere(['B.bas_ano_id' => $data['bas_ano_id']])
      ->andWhere(['>=', 'B.bas_mes_id', $data['begin_mes']])
      ->andWhere(['<=', 'B.bas_mes_id', $data['end_mes']])
      ->andWhere(['LIKE', 'C.path', $data['path'] . '%', false])
      ->filterWhere(['A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id']])
      ->orderBy('C.path,B.numero');


    return $query;
  }




  /**
   * @return \yii\db\ActiveQuery
   */
  public function queryExtratoFluxoCaixa(array $data)
  {
    // print_r($data);die();
    $query = new Query();
    $query->select([
      'A.id',
      'A.cnt_plano_conta_id',
      'descricao' => 'A.descricao',
      'B.cnt_diario_id',
      'debito' => new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='D' THEN A.valor ELSE 0 END"),
      'credito' => new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='C' THEN A.valor ELSE 0 END"),
      'cnt_documento_id',
      'data' => 'B.documento_origem_data',
      'num_doc' => 'B.numero',
      'terceiro' => 'A.cnt_plano_terceiro_id'
    ])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->andWhere(['B.bas_ano_id' => $data['bas_ano_id']])
      ->andWhere(['>=', 'B.bas_mes_id', $data['begin_mes']])
      ->andWhere(['<=', 'B.bas_mes_id', $data['end_mes']])
      ->orderBy('B.documento_origem_data');
    $query->andFilterWhere([
      'A.cnt_plano_fluxo_caixa_id' => $data['cnt_plano_fluxo_caixa_id'],
      'A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id'],
    ]);

    return $query;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function queryExtratoFluxoCaixaSaldoMesAnterior(array $data)
  {
    $debito = 0;
    $credito = 0;
    $mes = $data['begin_mes'] - 1;
    $query = new Query();
    $query->select(['A.id', 'A.cnt_plano_conta_id', 'descricao' => 'A.descricao', 'B.cnt_diario_id', 'debito' => new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='D' THEN A.valor ELSE 0 END"), 'credito' => new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='C' THEN A.valor ELSE 0 END"), 'cnt_documento_id', 'data' => 'B.documento_origem_data', 'num_doc' => 'B.numero', 'terceiro' => 'A.cnt_plano_terceiro_id'])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->where(['B.status' => 1])
      ->orderBy('B.documento_origem_data');
    $query->andFilterWhere([
      'YEAR(B.documento_origem_data)' => $data['bas_ano_id'],
      'A.cnt_plano_fluxo_caixa_id' => $data['cnt_plano_fluxo_caixa_id'],
      'A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id'],
    ]);
    $query->andFilterWhere(['<=', 'MONTH(B.documento_origem_data)', $mes]);
    $data = $query->All();
    foreach ($data as $key => $value) {
      $debito = $debito + $value['debito'];
      $credito = $credito + $value['credito'];
    }
    return $debito - $credito;
  }



  /**
   * @return \yii\db\ActiveQuery
   */
  public function getRazao($bas_ano_id, $bas_mes_id)
  {
    $query = new Query();
    $query->select([
      'A.cnt_plano_conta_id',
      'A.cnt_plano_terceiro_id',
      'debito' => new \yii\db\Expression("CASE WHEN  a.cnt_plano_terceiro_id IS NULL THEN cnt_razao_debito(YEAR(B.documento_origem_data),MONTH(B.documento_origem_data),A.cnt_plano_conta_id, NULL) ELSE cnt_razao_debito(YEAR(B.documento_origem_data),MONTH(B.documento_origem_data),A.cnt_plano_conta_id, A.cnt_plano_terceiro_id) END"),
      'credito' => new \yii\db\Expression("CASE WHEN  a.cnt_plano_terceiro_id IS NULL THEN cnt_razao_credito(YEAR(B.documento_origem_data),MONTH(B.documento_origem_data),A.cnt_plano_conta_id, NULL) ELSE cnt_razao_credito(YEAR(B.documento_origem_data),MONTH(B.documento_origem_data),A.cnt_plano_conta_id, A.cnt_plano_terceiro_id) END"),
      'debito_acumulado' => new \yii\db\Expression("CASE WHEN  a.cnt_plano_terceiro_id IS NULL THEN cnt_razao_debito_acumulado(YEAR(B.documento_origem_data),MONTH(B.documento_origem_data),A.cnt_plano_conta_id, NULL) ELSE cnt_razao_debito_acumulado(YEAR(B.documento_origem_data),MONTH(B.documento_origem_data),A.cnt_plano_conta_id, A.cnt_plano_terceiro_id) END"),
      'credito_acumulado' => new \yii\db\Expression("CASE WHEN  a.cnt_plano_terceiro_id IS NULL THEN cnt_razao_credito_acumulado(YEAR(B.documento_origem_data),MONTH(B.documento_origem_data),A.cnt_plano_conta_id, NULL) ELSE cnt_razao_credito_acumulado(YEAR(B.documento_origem_data),MONTH(B.documento_origem_data),A.cnt_plano_conta_id, A.cnt_plano_terceiro_id) END")
    ])
      ->from('cnt_razao_item A')
      ->leftJoin('cnt_razao B', 'B.id = A.cnt_razao_id')
      ->leftJoin('cnt_plano_conta C', 'C.id = A.cnt_plano_conta_id')
      ->where(['B.status' => 1])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['MONTH(B.documento_origem_data)' => $bas_mes_id])
      ->groupBy('A.cnt_plano_conta_id,A.cnt_plano_terceiro_id')
      ->orderBy('A.cnt_plano_conta_id');
    print_r($query->createCommand()->getRawSql());
    die();
    return $query->createCommand()->queryAll();
  }


  /**
   * @return \yii\db\ActiveQuery
   */
  public function getBalancete($bas_ano_id, $bas_mes_id)
  {
    $query = Yii::$app->db->createCommand(" SELECT PCX.id
       , PCX.descricao 
       , sum(xx.debito) as debito
       , sum(xx.credito) as credito
       , sum(xx.debito_acumulado) as debito_acumulado       
       , sum(xx.credito_acumulado) as credito_acumulado
       , sum(xx.saldo_debito) as saldo_debito
       , sum(xx.saldo_credito) as saldo_credito
        
    FROM (SELECT x.cnt_plano_conta_id
       ,x.path
             ,x.cnt_plano_terceiro_id
             ,sum( CASE WHEN x.bas_mes_id = :bas_mes_id THEN x.debito    ELSE 0.00  END) AS debito
              ,sum( CASE WHEN x.bas_mes_id = :bas_mes_id THEN x.credito   ELSE 0.00  END) AS credito
               , sum(x.debito) AS debito_acumulado
               , sum(x.credito)   AS credito_acumulado
               ,(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END) AS saldo_debito
             ,(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END) AS saldo_credito
          FROM
             (
      SELECT A.cnt_plano_conta_id
           ,A.cnt_plano_terceiro_id
                   ,C.path
                   ,YEAR(B.documento_origem_data) as bas_ano_id
                   ,MONTH(B.documento_origem_data) as bas_mes_id
                 ,(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )AS debito 
           ,(CASE WHEN A.cnt_natureza_id = 'C' THEN A.valor  ELSE 0.00  END )AS credito 
       FROM cnt_plano_conta C  
             LEFT JOIN cnt_razao_item A ON (C.id = A.cnt_plano_conta_id)
       LEFT JOIN cnt_razao B ON (A.cnt_razao_id=B.id)      
      WHERE B.status =1 
          AND YEAR(B.documento_origem_data)=:bas_ano_id 
          AND MONTH(B.documento_origem_data)<=:bas_mes_id
      ) AS x
      GROUP BY x.cnt_plano_conta_id, x.cnt_plano_terceiro_id
) AS xx
    LEFT JOIN cnt_plano_conta PCX 
      ON xx.path LIKE CONCAT(PCX.path,'%')
GROUP BY PCX.id , PCX.descricao 
ORDER BY PCX.path ASC
       ; 
");
    $query->bindValues([':bas_ano_id' => $bas_ano_id, ':bas_mes_id' => $bas_mes_id]);
    // print_r($query->getRawSql());die();
    return $query->queryAll();
  }




  /**
   * @return \yii\db\ActiveQuery
   */
  public function queryBalanceteTerceiro($cnt_plano_conta_id, $bas_ano_id, $bas_mes_id)
  {
    $query = Yii::$app->db->createCommand("SELECT x.cnt_plano_conta_id
             ,x.cnt_plano_terceiro_id
             ,x.person
             , sum(CASE WHEN x.bas_mes_id = :bas_mes_id THEN x.debito    ELSE 0.00  END) AS debito
       ,sum( CASE WHEN x.bas_mes_id = :bas_mes_id THEN x.credito   ELSE 0.00  END) AS credito
             , sum(x.debito) AS debito_acumulado
             , sum(x.credito)   AS credito_acumulado
       ,(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END) AS saldo_d
             ,(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END) AS saldo_c
               
          FROM
             (
      SELECT A.cnt_plano_conta_id
           ,A.cnt_plano_terceiro_id
                   ,D.nome as person
                   ,C.path
                   ,YEAR(B.documento_origem_data) as bas_ano_id
                   ,MONTH(B.documento_origem_data) as bas_mes_id
                 ,(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )AS debito 
           ,(CASE WHEN A.cnt_natureza_id = 'C' THEN A.valor  ELSE 0.00  END )AS credito 
       FROM cnt_plano_conta C  
             LEFT JOIN cnt_razao_item A ON (C.id = A.cnt_plano_conta_id)
       LEFT JOIN cnt_razao B ON (A.cnt_razao_id=B.id)   
             LEFT JOIN dsp_person D ON (D.id = A.cnt_plano_terceiro_id)
      WHERE B.status =1 
          AND YEAR(B.documento_origem_data)=:bas_ano_id 
          AND MONTH(B.documento_origem_data)<=:bas_mes_id
                  AND C.tem_plano_externo = 1
      ) AS x
      where x.cnt_plano_conta_id = :cnt_plano_conta_id
      GROUP BY x.cnt_plano_conta_id, x.cnt_plano_terceiro_id
            ORDER BY x.person");
    $query->bindValues([':cnt_plano_conta_id' => $cnt_plano_conta_id, ':bas_ano_id' => $bas_ano_id, ':bas_mes_id' => $bas_mes_id]);
    // print_r($query->getRawSql());die();
    return $query->queryAll();
  }












  /**
   * @return \yii\db\ActiveQuery
   */
  public function getBalanceteFluxoCaixa($bas_ano_id, $bas_mes_id)
  {
    $query = Yii::$app->db->createCommand(" SELECT PCX.id
       , PCX.descricao 
       , sum(xx.debito) as debito
       , sum(xx.credito) as credito
       , sum(xx.debito_acumulado) as debito_acumulado       
       , sum(xx.credito_acumulado) as credito_acumulado
       , sum(xx.saldo_debito) as saldo_debito
       , sum(xx.saldo_credito) as saldo_credito
        
    FROM (SELECT x.cnt_plano_conta_id
       ,x.path
             ,x.cnt_plano_terceiro_id
             ,sum( CASE WHEN x.bas_mes_id = :bas_mes_id THEN x.debito    ELSE 0.00  END) AS debito
              ,sum( CASE WHEN x.bas_mes_id = :bas_mes_id THEN x.credito   ELSE 0.00  END) AS credito
               , sum(x.debito) AS debito_acumulado
               , sum(x.credito)   AS credito_acumulado
               ,(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END) AS saldo_debito
             ,(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END) AS saldo_credito
          FROM
             (
      SELECT A.cnt_plano_conta_id
           ,A.cnt_plano_terceiro_id
                   ,C.path
                   ,YEAR(B.documento_origem_data) as bas_ano_id
                   ,MONTH(B.documento_origem_data) as bas_mes_id
                 ,(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )AS debito 
           ,(CASE WHEN A.cnt_natureza_id = 'C' THEN A.valor  ELSE 0.00  END )AS credito 
       FROM cnt_plano_conta C  
             LEFT JOIN cnt_razao_item A ON (C.id = A.cnt_plano_conta_id)
       LEFT JOIN cnt_razao B ON (A.cnt_razao_id=B.id)      
      WHERE B.status =1 
          AND YEAR(B.documento_origem_data)=:bas_ano_id 
          AND MONTH(B.documento_origem_data)<=:bas_mes_id
          AND C.tem_plano_fluxo_caixa =1
      ) AS x
      GROUP BY x.cnt_plano_conta_id, x.cnt_plano_terceiro_id
) AS xx
    LEFT JOIN cnt_plano_conta PCX 
      ON xx.path LIKE CONCAT(PCX.path,'%')
GROUP BY PCX.id , PCX.descricao 
ORDER BY PCX.path ASC
       ; 
");
    $query->bindValues([':bas_ano_id' => $bas_ano_id, ':bas_mes_id' => $bas_mes_id]);
    // print_r($query->getRawSql());die();
    return $query->queryAll();
  }





  /**
   * @return \yii\db\ActiveQuery
   */
  public function getLancamentoError()
  {
    $query = (new \yii\db\Query())
      ->select([
        'B.id', 'B.numero', 'B.cnt_diario_id', 'B.bas_ano_id', 'B.bas_mes_id', 'debito' => "SUM(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "SUM(CASE WHEN A.cnt_natureza_id = 'C' THEN A.valor  ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_conta'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_conta_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->where(['B.status' => 1])
      ->groupBy('B.id')
      // ->andWhere(['YEAR(B.documento_origem_data)'=>$bas_ano_id])
      // ->andWhere(['<=','MONTH(B.documento_origem_data)',$bas_mes_id])
      // ->having('debito!=credito OR(debito=0 AND credito=0)')
      ->having('(debito != credito)')
      ->all();



    return $query;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getLancamentoErrorCount()
  {
    $query = (new \yii\db\Query())
      ->select([
        'B.id', 'B.numero', 'B.cnt_diario_id', 'B.bas_ano_id', 'B.bas_mes_id', 'debito' => "SUM(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "SUM(CASE WHEN A.cnt_natureza_id = 'C' THEN A.valor  ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_conta'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_conta_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->where(['B.status' => 1])
      ->groupBy('B.id')
      // ->andWhere(['YEAR(B.documento_origem_data)'=>$bas_ano_id])
      // ->andWhere(['<=','MONTH(B.documento_origem_data)',$bas_mes_id])
      // ->having('debito!=credito OR (debito=0 AND credito=0)')
      ->having('debito!=credito')
      ->count();



    return $query;
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getBalanceteConta($bas_ano_id, $bas_mes_id, $cnt_plano_conta_id)
  {
    $planoConta = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();

    $raz_detalhado = (new \yii\db\Query())
      ->select([
        'A.cnt_plano_conta_id', 'A.cnt_plano_terceiro_id', 'C.path', 'bas_mes_id' => 'YEAR(B.documento_origem_data)', 'bas_mes_id' => 'MONTH(B.documento_origem_data)', 'debito' => "(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_conta'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_conta_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->where(['B.status' => 1])
      ->andWhere(['YEAR(B.documento_origem_data)' => $bas_ano_id])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $bas_mes_id]);

    $razao_agrupado = (new \yii\db\Query())
      ->select([
        'x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id', 'x.path', 'debito' => "sum(CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.debito    ELSE 0.00  END)", 'credito' => "sum( CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.credito    ELSE 0.00  END)", 'debito_acumulado' => 'sum(x.debito)', 'credito_acumulado' => 'sum(x.credito)', 'saldo_debito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)', 'saldo_credito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
      ])
      ->from(['x' => $raz_detalhado])
      ->groupBy(['x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id']);

    $balancete = (new \yii\db\Query())
      ->select([
        'P.id', 'P.descricao', 'debito' => 'sum(xx.debito)', 'credito' => 'sum(xx.credito)', 'debito_acumulado' => 'sum(xx.debito_acumulado)', 'credito_acumulado' => 'sum(xx.credito_acumulado)', 'saldo_debito' => 'sum(xx.saldo_debito)', 'saldo_credito' => 'sum(xx.saldo_credito)'
      ])
      ->from(['xx' => $razao_agrupado])
      ->leftJoin(['P' => 'cnt_plano_conta'], ['LIKE', 'xx.path', new \yii\db\Expression("CONCAT(P.path,'%')"), FALSE])
      ->groupBy(['P.id', 'P.descricao'])
      ->where(['P.id' => $cnt_plano_conta_id])
      ->orderBy('P.path')
      ->one();

    return $balancete;
  }



  /**
   * @return \yii\db\ActiveQuery
   */
  public function getBalanceteContaAll($bas_ano_id, $bas_mes_id, $cnt_plano_conta_id)
  {
    $planoConta = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();
    if (!$planoConta) {
      $planoConta['path'] = '';
    }

    $raz_detalhado = (new \yii\db\Query())
      ->select([
        'A.cnt_plano_conta_id', 'A.cnt_plano_terceiro_id', 'C.path', 'B.bas_ano_id', 'B.bas_mes_id', 'debito' => "(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_conta'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_conta_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->where(['B.status' => 1])
      ->andWhere(['B.bas_ano_id' => $bas_ano_id])
      ->andWhere(['<=', 'B.bas_mes_id', $bas_mes_id]);

    $razao_agrupado = (new \yii\db\Query())
      ->select([
        'x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id', 'x.path', 'debito' => "sum(CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.debito    ELSE 0.00  END)", 'credito' => "sum( CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.credito    ELSE 0.00  END)", 'debito_acumulado' => 'sum(x.debito)', 'credito_acumulado' => 'sum(x.credito)', 'saldo_debito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)', 'saldo_credito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
      ])
      ->from(['x' => $raz_detalhado])
      ->groupBy(['x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id']);

    $balancete = (new \yii\db\Query())
      ->select([
        'P.id', 'P.descricao', 'debito' => 'sum(xx.debito)', 'credito' => 'sum(xx.credito)', 'debito_acumulado' => 'sum(xx.debito_acumulado)', 'credito_acumulado' => 'sum(xx.credito_acumulado)', 'saldo_debito' => 'sum(xx.saldo_debito)', 'saldo_credito' => 'sum(xx.saldo_credito)'
      ])
      ->from(['xx' => $razao_agrupado])
      ->leftJoin(['P' => 'cnt_plano_conta'], ['LIKE', 'xx.path', new \yii\db\Expression("CONCAT(P.path,'%')"), FALSE])
      ->groupBy(['P.id', 'P.descricao'])
      // ->where(['P.id'=>$cnt_plano_conta_id])                    
      ->filterwhere(['LIKE', 'P.path', $planoConta['path'] . '%', FALSE])
      ->orderBy('P.path')
      ->all();

    return $balancete;
  }




  /**
   * @return \yii\db\ActiveQuery
   */
  public function getBalanceteAberturaConta($bas_ano_id)
  {
    $raz_detalhado = (new \yii\db\Query())
      ->select([
        'A.cnt_plano_conta_id', 'A.cnt_plano_terceiro_id', 'C.path', 'bas_mes_id' => 'YEAR(B.documento_origem_data)', 'bas_mes_id' => 'MONTH(B.documento_origem_data)', 'debito' => "(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_conta'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_conta_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->where(['B.status' => 1])
      ->andWhere(['B.bas_ano_id' => $bas_ano_id]);

    $razao_agrupado = (new \yii\db\Query())
      ->select([
        'x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id', 'x.path', 'saldo_debito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)', 'saldo_credito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
      ])
      ->from(['x' => $raz_detalhado])
      ->groupBy(['x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id'])
      ->having([
        'or',
        ['>', 'saldo_debito', 0],
        ['>', 'saldo_credito', 0]
      ])
      ->all();

    return $razao_agrupado;
  }





  /**
   * @return \yii\db\ActiveQuery
   */
  public function getBalanceteFechoConta($bas_ano_id, $bas_mes_id, $cnt_plano_conta_id)
  {
    $planoConta = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();

    $raz_detalhado = (new \yii\db\Query())
      ->select([
        'A.cnt_plano_conta_id', 'A.cnt_plano_terceiro_id', 'C.path', 'B.bas_ano_id', 'B.bas_mes_id', 'debito' => "(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_conta'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_conta_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->where(['B.status' => 1])
      ->andWhere(['B.bas_ano_id' => $bas_ano_id])
      ->andWhere(['<=', 'B.bas_mes_id', $bas_mes_id]);

    $razao_agrupado = (new \yii\db\Query())
      ->select([
        'x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id', 'x.path', 'debito' => "sum(CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.debito    ELSE 0.00  END)", 'credito' => "sum( CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.credito    ELSE 0.00  END)", 'debito_acumulado' => 'sum(x.debito)', 'credito_acumulado' => 'sum(x.credito)', 'saldo_debito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)', 'saldo_credito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
      ])
      ->from(['x' => $raz_detalhado])
      ->groupBy(['x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id']);

    $balancete = (new \yii\db\Query())
      ->select([
        'P.id', 'P.descricao', 'xx.cnt_plano_terceiro_id', 'debito' => 'sum(xx.debito)', 'credito' => 'sum(xx.credito)', 'debito_acumulado' => 'sum(xx.debito_acumulado)', 'credito_acumulado' => 'sum(xx.credito_acumulado)', 'saldo_debito' => 'sum(xx.saldo_debito)', 'saldo_credito' => 'sum(xx.saldo_credito)'
      ])
      ->from(['xx' => $razao_agrupado])
      ->leftJoin(['P' => 'cnt_plano_conta'], ['LIKE', 'xx.path', new \yii\db\Expression("CONCAT(P.path,'%')"), FALSE])
      ->groupBy(['P.id'])
      ->where(['P.path' => $planoConta['path']])
      ->orderBy('P.path')
      ->all();

    return $balancete;
  }




  /**
   * @return \yii\db\ActiveQuery
   */
  public function getBalanceteFechoContaAll($bas_ano_id, $bas_mes_id, $cnt_plano_conta_id)
  {
    $planoConta = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();

    $raz_detalhado = (new \yii\db\Query())
      ->select([
        'A.cnt_plano_conta_id', 'A.cnt_plano_terceiro_id', 'C.path', 'B.bas_ano_id', 'B.bas_mes_id', 'debito' => "(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_conta'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_conta_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->where(['B.status' => 1])
      ->andWhere(['B.bas_ano_id' => $bas_ano_id])
      ->andWhere(['<=', 'B.bas_mes_id', $bas_mes_id]);

    $razao_agrupado = (new \yii\db\Query())
      ->select([
        'x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id', 'x.path', 'debito' => "sum(CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.debito    ELSE 0.00  END)", 'credito' => "sum( CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.credito    ELSE 0.00  END)", 'debito_acumulado' => 'sum(x.debito)', 'credito_acumulado' => 'sum(x.credito)', 'saldo_debito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)', 'saldo_credito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
      ])
      ->from(['x' => $raz_detalhado])
      ->groupBy(['x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id']);

    $balancete = (new \yii\db\Query())
      ->select([
        'P.id', 'P.descricao', 'xx.cnt_plano_terceiro_id', 'debito' => 'sum(xx.debito)', 'credito' => 'sum(xx.credito)', 'debito_acumulado' => 'sum(xx.debito_acumulado)', 'credito_acumulado' => 'sum(xx.credito_acumulado)', 'saldo_debito' => 'sum(xx.saldo_debito)', 'saldo_credito' => 'sum(xx.saldo_credito)'
      ])
      ->from(['xx' => $razao_agrupado])
      ->leftJoin(['P' => 'cnt_plano_conta'], ['LIKE', 'xx.path', new \yii\db\Expression("CONCAT(P.path,'%')"), FALSE])
      ->groupBy(['P.id', 'xx.cnt_plano_terceiro_id'])
      ->where(['like', 'P.path', $planoConta['path'] . '%', FALSE])
      ->andWhere(['P.cnt_plano_conta_tipo_id' => 2])
      ->orderBy('P.path')
      ->having([
        'or',
        ['>', 'saldo_debito', 0],
        ['>', 'saldo_credito', 0]
      ])
      ->all();

    return $balancete;
  }








  public  function BalancetePerson($data)
  {

    $bas_mes_id = $data['bas_mes_id'];

    $data_one = (new \yii\db\Query())
      ->select([
        'A.cnt_plano_conta_id', 'A.cnt_plano_terceiro_id', 'person' => 'D.nome', 'C.path', 'bas_mes_id' => 'YEAR(B.documento_origem_data)', 'bas_mes_id' => 'MONTH(B.documento_origem_data)', 'debito' => "(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_conta'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_conta_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->leftJoin(['D' => 'dsp_person'], 'D.id = A.cnt_plano_terceiro_id')
      ->where(['B.status' => 1])
      ->andWhere(['C.tem_plano_externo' => 1])
      ->andWhere(['A.cnt_plano_conta_id' => $data['cnt_plano_conta_id']])
      ->andWhere(['YEAR(B.documento_origem_data)' => $data['ano']])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $data['bas_mes_id']])
      ->filterWhere(['A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id']]);

    $data_two = (new \yii\db\Query())
      ->select([
        'x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id', 'x.person', 'x.path', 'debito' => "sum(CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.debito    ELSE 0.00  END)", 'credito' => "sum( CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.credito    ELSE 0.00  END)", 'debito_acumulado' => 'sum(x.debito)', 'credito_acumulado' => 'sum(x.credito)', 'saldo_d' => '(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)', 'saldo_c' => '(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
      ])
      ->from(['x' => $data_one])
      ->groupBy(['x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id'])
      ->orderBy('x.person')
      // ->filterWhere(['LIKE','x.path',$this->path.'%', FALSE])
      ->all();

    return $data_two;
  }













  public  function getBalanceteFluxoCaixaAll($ano, $bas_mes_id, $cnt_plano_fluxo_caixa_id, $cnt_plano_terceiro_id = null)
  {
    $planoCaixa =  PlanoFluxoCaixa::find()->where(['id' => $cnt_plano_fluxo_caixa_id])->asArray()->one();

    $raz_detalhado = (new \yii\db\Query())
      ->select([
        'A.cnt_plano_fluxo_caixa_id', 'A.cnt_plano_terceiro_id', 'C.path', 'bas_mes_id' => 'YEAR(B.documento_origem_data)', 'bas_mes_id' => 'MONTH(B.documento_origem_data)', 'debito' => "(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )", 'credito' => "(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"
      ])
      ->from(['C' => 'cnt_plano_fluxo_caixa'])
      ->leftJoin(['A' => 'cnt_razao_item'], 'C.id = A.cnt_plano_fluxo_caixa_id')
      ->leftJoin(['B' => 'cnt_razao'], 'A.cnt_razao_id=B.id')
      ->where(['B.status' => 1])
      ->andWhere(['YEAR(B.documento_origem_data)' => $ano])
      ->andWhere(['<=', 'MONTH(B.documento_origem_data)', $bas_mes_id])
      ->filterWhere(['A.cnt_plano_terceiro_id' => $cnt_plano_terceiro_id]);

    $razao_agrupado = (new \yii\db\Query())
      ->select([
        'x.cnt_plano_fluxo_caixa_id', 'x.cnt_plano_terceiro_id', 'x.path', 'debito' => "sum(CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.debito    ELSE 0.00  END)", 'credito' => "sum( CASE WHEN x.bas_mes_id = '$bas_mes_id' THEN x.credito    ELSE 0.00  END)", 'debito_acumulado' => 'sum(x.debito)', 'credito_acumulado' => 'sum(x.credito)', 'saldo_debito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)', 'saldo_credito' => '(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
      ])
      ->from(['x' => $raz_detalhado])
      ->groupBy(['x.cnt_plano_fluxo_caixa_id', 'x.cnt_plano_terceiro_id']);

    $balancete = (new \yii\db\Query())
      ->select([
        'P.id', 'P.descricao', 'debito' => 'sum(xx.debito)', 'credito' => 'sum(xx.credito)', 'debito_acumulado' => 'sum(xx.debito_acumulado)', 'credito_acumulado' => 'sum(xx.credito_acumulado)', 'saldo_debito' => 'sum(xx.saldo_debito)', 'saldo_credito' => 'sum(xx.saldo_credito)'
      ])
      ->from(['xx' => $razao_agrupado])
      ->leftJoin(['P' => 'cnt_plano_fluxo_caixa'], ['LIKE', 'xx.path', new \yii\db\Expression("CONCAT(P.path,'%')"), FALSE])
      ->groupBy(['P.id', 'P.descricao'])
      ->filterWhere(['LIKE', 'P.path', $planoCaixa['path'] . '%', FALSE])
      ->orderBy('P.path')
      ->all();

    return $balancete;
  }



  /**
   * Lists all User models.
   * @return mixed
   */
  public function cntRecebimentoTesourariaItem($id)
  {
    return (new \yii\db\Query())
      ->select([
        'A.id',
        'numero' => "CONCAT(B.numero, '/', B.bas_ano_id)",
        'descricao' => 'C.descricao',
        'cnt_plano_terceiro_id' => 'A.dsp_person_id',
        'C.cnt_plano_conta_id',
        'C.cnt_plano_iva_id',
        'C.cnt_plano_fluxo_caixa_id',
        'valor' => 'A.valor'
      ])
      ->from('fin_recebimento_item A')
      ->leftJoin('fin_recebimento B', 'B.id = A.fin_recebimento_id')
      ->leftJoin('dsp_item C', 'C.id = A.dsp_item_id')
      ->where(['A.fin_recebimento_id' => $id])
      ->groupBy(['A.id'])
      ->all();
  }
}
