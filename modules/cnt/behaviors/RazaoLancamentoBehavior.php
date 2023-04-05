<?php

namespace app\modules\cnt\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;

class RazaoLancamentoBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_AFTER_INSERT => 'after_insert',
           ActiveRecord::EVENT_AFTER_UPDATE => 'after_update',
       ];
   }

  public function after_insert(Event $event){        
      Yii::$app->CntQuery->vereficarDebitoCredito($this->owner->cnt_razao_id); 
  }

  public function after_update(Event $event){
      Yii::$app->CntQuery->vereficarDebitoCredito($this->owner->cnt_razao_id);       
  }
  public function after_delete(Event $event){
      Yii::$app->CntQuery->vereficarDebitoCredito($this->owner->cnt_razao_id);       
  }


}