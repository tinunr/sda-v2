<?php

namespace app\modules\fin\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Despesa;

class ReceitaBehavior extends Behavior
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
    if(!empty($this->owner->faturaProvisoria->dsp_processo_id)){
        \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($this->owner->faturaProvisoria->dsp_processo_id);
    }
      
      
  }

  public function update(Event $event)
   {
    if(!empty($this->owner->faturaProvisoria->dsp_processo_id)){
        \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($this->owner->faturaProvisoria->dsp_processo_id);
    }
       
        
      
  }


}