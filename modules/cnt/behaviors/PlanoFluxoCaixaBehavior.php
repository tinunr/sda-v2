<?php

namespace app\modules\cnt\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\cnt\models\PlanoFluxoCaixa;

class PlanoFluxoCaixaBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_BEFORE_INSERT => 'before_insert',
           ActiveRecord::EVENT_BEFORE_UPDATE => 'before_update',
       ];
   }

   public function before_insert(Event $event)
   {        
      $this->owner->codigo = (string) $this->owner->id;
      if (!empty($this->owner->cnt_plano_fluxo_caixa_id)) {
        $pai = PlanoFluxoCaixa::find()->where(['id'=>$this->owner->cnt_plano_fluxo_caixa_id])->asArray()->one();
        $this->owner->path = $pai['path'].$this->owner->id.'/';
      }else{
        $this->owner->path = $this->owner->id.'/';
      }
      
      
  }

  public function before_update(Event $event)
   {
      $this->owner->codigo = (string) $this->owner->id;
      if (!empty($this->owner->cnt_plano_fluxo_caixa_id)) {
        $pai = PlanoFluxoCaixa::find()->where(['id'=>$this->owner->cnt_plano_fluxo_caixa_id])->asArray()->one();
        $this->owner->path = $pai['path'].$this->owner->id.'/';
      }else{
        $this->owner->path =  $this->owner->id.'/';
      }
      
  }


}