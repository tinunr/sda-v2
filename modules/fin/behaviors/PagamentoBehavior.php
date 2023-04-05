<?php

namespace app\modules\fin\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\CaixaOperacao;
use app\modules\fin\models\BancoTransacao;
use app\modules\fin\models\BancoTransacaoTipo;

class PagamentoBehavior extends Behavior
{

   public function events()
   {
       return [
           // ActiveRecord::EVENT_AFTER_INSERT => 'create',
           // ActiveRecord::EVENT_BEFORE_UPDATE => 'update',
       ];
   }

   public function create(Event $event)
   {

    //   $caixaTransacao = new CaixaTransacao();
    //   $caixaTransacao->fin_pagamento_id =  $this->owner->id;
    //   $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
    //   $caixaTransacao->descricao = 'Pagamento nº '.$this->owner->numero.'/'.$this->owner->bas_ano_id;
    //   $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($this->owner->fin_banco_conta_id);
    //   $caixaTransacao->valor_saida = $this->owner->valor;
    //   $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($this->owner->fin_banco_conta_id);
    //   $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::PAGAMENTO;
    //   $caixaTransacao->fin_documento_pagamento_id = $this->owner->fin_documento_pagamento_id;
    //   $caixaTransacao->numero_documento = $this->owner->numero_documento;
    //   $caixaTransacao->data_documento = $this->owner->data_documento;
    //   $caixaTransacao->save();

  }





  public function update(Event $event)
   {
    //   if(($caixaTransacao = CaixaTransacao::findOne(['fin_pagamento_id'=>$this->owner->id]))!=null){
    //   $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
    //   $caixaTransacao->descricao = 'Pagamento nº '.$this->owner->numero;
    //   $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($this->owner->fin_banco_conta_id);
    //   $caixaTransacao->valor_saida = $this->owner->valor;
    //   $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($this->owner->fin_banco_conta_id);
    //   $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::PAGAMENTO;
    //   $caixaTransacao->fin_documento_pagamento_id = $this->owner->fin_documento_pagamento_id;
    //   $caixaTransacao->numero_documento = $this->owner->numero_documento;
    //   $caixaTransacao->data_documento = $this->owner->data_documento;
    //     $caixaTransacao->save();
    // }else{
    //   $caixaTransacao = new CaixaTransacao();
    //   $caixaTransacao->fin_pagamento_id =  $this->owner->id;
    //   $caixaTransacao->status =  CaixaTransacao::STATUS_UNCKECKED;
    //   $caixaTransacao->descricao = 'Pagamento nº '.$this->owner->numero;
    //   $caixaTransacao->fin_caixa_id = Yii::$app->FinCaixa->caixaId($this->owner->fin_banco_conta_id);
    //   $caixaTransacao->valor_saida = $this->owner->valor;
    //   $caixaTransacao->saldo = Yii::$app->FinCaixa->saldoConta($this->owner->fin_banco_conta_id);
    //   $caixaTransacao->fin_caixa_operacao_id =  CaixaOperacao::PAGAMENTO;
    //   $caixaTransacao->fin_documento_pagamento_id = $this->owner->fin_documento_pagamento_id;
    //   $caixaTransacao->numero_documento = $this->owner->numero_documento;
    //   $caixaTransacao->data_documento = $this->owner->data_documento;
    //   $caixaTransacao->save();
    // }  
  }



}