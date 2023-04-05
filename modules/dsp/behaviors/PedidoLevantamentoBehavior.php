<?php

namespace app\modules\dsp\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;

class PedidoLevantamentoBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_BEFORE_INSERT => 'run',
           ActiveRecord::EVENT_BEFORE_UPDATE => 'run',
       ];
   }

   public function run(Event $event)
   {     
          $this->owner->status = 1;
          if ($this->owner->data_regularizacao) {
          $this->owner->status = 2;
          }

          if(($model = \app\modules\dsp\models\Processo::findOne(['n_levantamento'=>$this->owner->id]))!=null){
                if (!empty($model->nord->despacho->id)) {
                    $this->owner->status = 3;
                }
                if (!empty($model->nord->despacho->numero_liquidade)) {
                    $this->owner->status = 4;
                }
                if (!empty($model->nord->despacho->n_receita)) {
                     $this->owner->status = 5;
                }
        }
  }


}