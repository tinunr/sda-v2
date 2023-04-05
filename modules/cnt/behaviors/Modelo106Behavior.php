<?php

namespace app\modules\cnt\behaviors;

use yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\models\Ano;
use app\models\Mes;

class Modelo106Behavior extends Behavior
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
        $this->owner->ano_descricao =date("Y", strtotime($this->owner->data));
        $this->owner->mes_descricao = Mes::findOne($this->owner->mes)->descricao;
  }

   public function before_update(Event $event)
   {
        $this->owner->ano_descricao =date("Y", strtotime($this->owner->data));
        $this->owner->mes_descricao = Mes::findOne($this->owner->mes)->descricao;
  }


}