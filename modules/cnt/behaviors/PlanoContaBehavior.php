<?php

namespace app\modules\cnt\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\cnt\models\PlanoConta;

class PlanoContaBehavior extends Behavior
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
      if (!empty($this->owner->cnt_plano_conta_id)) {
        $pai = PlanoConta::find()->where(['id'=>$this->owner->cnt_plano_conta_id])->asArray()->one();
        $this->owner->path = $pai['path'].$this->owner->id.'/';
        $this->owner->cnt_natureza_id = $pai['cnt_natureza_id']; 
      }else{
        $this->owner->path = $this->owner->id.'/';
      }
      
      
  }

  public function before_update(Event $event)
   {
      $this->owner->codigo = (string) $this->owner->id;
      if (!empty($this->owner->cnt_plano_conta_id)) {
        $pai = PlanoConta::find()->where(['id'=>$this->owner->cnt_plano_conta_id])->asArray()->one();
        $this->owner->path = $pai['path'].$this->owner->id.'/';
        $this->owner->cnt_natureza_id = $pai['cnt_natureza_id'];  
      }else{
        $this->owner->path =  $this->owner->id.'/';
      }
      
  }


}