<?php

namespace app\modules\fin\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Transferencia;

class TransferenciaBehavior extends Behavior
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
    $number = 1;
    $this->owner->bas_ano_id = substr(date('Y', strtotime($this->owner->data)),-2);
    if(($n = Transferencia::find()->where(['bas_ano_id'=>$this->owner->bas_ano_id])->max('numero')) > 0){
        $number = $n + 1;
    }
    $this->owner->numero = $number;

  }

  public function update(Event $event)
  {
                
  }


}