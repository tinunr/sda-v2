<?php

namespace app\modules\fin\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Receita;
use app\models\DocumentoNumero;
use app\models\Documento;
use app\modules\fin\services\FaturaProvisoriaService;

class FaturaProvisoriaBehavior extends Behavior
{

  public function events()
  {
    return [
      ActiveRecord::EVENT_BEFORE_INSERT => 'beforeCreate',
      ActiveRecord::EVENT_AFTER_INSERT => 'afteCreate',
      ActiveRecord::EVENT_AFTER_UPDATE => 'update',
    ];
  }

  public function beforeCreate(Event $event)
  {
    $number = 1;
    $this->owner->bas_ano_id = substr(date('Y', strtotime($this->owner->data)), -2);
    if (($n = \app\modules\fin\models\FaturaProvisoria::find()->where(['bas_ano_id' => $this->owner->bas_ano_id])->max('numero')) >= 1) {
      $number = $n + 1;
    }
    $this->owner->numero = $number;
  }


  public function afteCreate(Event $event)
  {
    $documentoNumero = DocumentoNumero::findByDocumentId(Documento::FATURA_PROVISORIA_ID);
    $documentoNumero->saveNexNumber();

    $receita = new Receita();
    $receita->fin_receita_tipo_id =  1;
    $receita->dsp_fataura_provisoria_id =  $this->owner->id;
    $receita->valor = $this->owner->valor;
    $receita->valor_recebido = 0;
    $receita->saldo = $this->owner->valor;
    $receita->descricao = 'Receita gerada a partir de fatura provisória nº ' . $this->owner->numero . '/' . $this->owner->bas_ano_id;
    $receita->dsp_person_id =  $this->owner->dsp_person_id;
    $receita->bas_ano_id = $this->owner->bas_ano_id;
    $receita->status =  1;
    $receita->data =  $this->owner->data;
    $receita->save();

    \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($this->owner->dsp_processo_id);
  }





  public function update(Event $event)
  {
    FaturaProvisoriaService::atualizarValorRecebido($this->owner->id);
    
    \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($this->owner->dsp_processo_id);
  }
}
