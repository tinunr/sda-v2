<?php

namespace app\modules\cnt\behaviors;

use yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\cnt\models\DiarioNumero;
use app\modules\cnt\models\Razao;

class RazaoBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_BEFORE_INSERT => 'before_insert',
           ActiveRecord::EVENT_AFTER_INSERT => 'after_insert',
           ActiveRecord::EVENT_BEFORE_UPDATE => 'before_update',

       ];
   }

   public function before_insert(Event $event)
   { 
      if (empty($this->owner->documento_origem_data)) {
        $this->owner->documento_origem_data = $this->owner->data;
      }
      if(empty($this->owner->bas_mes_id)&&empty($this->owner->bas_ano_id)){
        $this->owner->bas_mes_id = Yii::$app->formatter->asDate($this->owner->documento_origem_data, 'MM'); 
        $this->owner->bas_ano_id = substr(date('Y', strtotime($this->owner->documento_origem_data)),-2); 
      }  
      // if(empty($this->owner->numero)){
      //   $diarioNumero = DiarioNumero::findByDiarioId($this->owner->cnt_diario_id,$this->owner->bas_ano_id, $this->owner->bas_mes_id);
      //   $this->owner->numero =  $diarioNumero->getNexNumber();
      //   // $this->owner->bas_mes_id =  $diarioNumero->mes;
      //   // $this->owner->bas_ano_id =  substr($diarioNumero->ano,-2);
      // }

        $number = 1; 
        if (($n = Razao::find()->where(['bas_ano_id' => $this->owner->bas_ano_id,'bas_mes_id'=>$this->owner->bas_mes_id ,'cnt_diario_id'=>$this->owner->cnt_diario_id])->max('numero')) > 0) {
            $number = $n + 1;
        }
        $this->owner->numero = $number;

      
  }

  public function after_insert(Event $event)
   {
      $diarioNumero = DiarioNumero::findByDiarioId($this->owner->cnt_diario_id,$this->owner->bas_ano_id, $this->owner->bas_mes_id);
      $diarioNumero->saveNexNumber();
      
  }


   public function before_update(Event $event)
   {
      if (empty($this->owner->documento_origem_data)) {
        $this->owner->documento_origem_data = $this->owner->data;
      }
      if(empty($this->owner->bas_mes_id)&&empty($this->owner->bas_ano_id)){
        $this->owner->bas_mes_id = Yii::$app->formatter->asDate($this->owner->documento_origem_data, 'MM');
        $this->owner->bas_ano_id = substr(date('Y', strtotime($this->owner->documento_origem_data)),-2); 
      }
  }


}