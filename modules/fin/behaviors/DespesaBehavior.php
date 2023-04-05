<?php

namespace app\modules\fin\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;

class DespesaBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_BEFORE_INSERT => 'create',
           ActiveRecord::EVENT_BEFORE_UPDATE => 'update',
       ];
   }

   public function create(Event $event)
   {        
      if(empty($this->owner->numero)){
          $this->owner->numero =  (empty($this->owner->processo->numero)?'':'PR'.$this->owner->processo->numero).(empty($this->owner->notaCredito->numero)?'':'|AC'.$this->owner->notaCredito->numero).(empty($this->owner->dsp_fatura_provisoria_id)?'':'|FP'.$this->owner->faturaProvisoria->numero).'CF'.$this->owner->dsp_person_id;
      }
      if(!empty($this->owner->dsp_processo_id)){
          \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($this->owner->dsp_processo_id);
      }
  }

  public function update(Event $event)
   {
    if(empty($this->owner->numero)){
        $this->owner->numero = (empty($this->owner->processo->numero)?'':'PR'.$this->owner->processo->numero).(empty($this->owner->notaCredito->numero)?'':'|AC'.$this->owner->notaCredito->numero).(empty($this->owner->dsp_fatura_provisoria_id)?'':'|FP'.$this->owner->faturaProvisoria->numero).'|CF'.$this->owner->dsp_person_id;
    }
    if(!empty($this->owner->dsp_processo_id)){
        \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($this->owner->dsp_processo_id);
    }

      
  }


}