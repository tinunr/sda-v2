<?php

namespace app\modules\fin\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;

class RecebimentoBehavior extends Behavior
{

  public function events()
   {
       return [
           ActiveRecord::EVENT_AFTER_INSERT => 'create',
       ];
   }

   public function create(Event $event)
   {
      $documentoNumero = \app\models\DocumentoNumero::findByDocumentId(\app\models\Documento::RECEBIMENTO_ID);
      if($this->owner->bas_ano_id == substr(date('Y'),-2) && $this->owner->numero ==$documentoNumero->getNexNumber()){
        $documentoNumero->saveNexNumber();
      }
  }



}