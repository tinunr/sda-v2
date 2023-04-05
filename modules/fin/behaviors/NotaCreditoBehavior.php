<?php

namespace app\modules\fin\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaItem;
use app\models\Documento;
use app\models\DocumentoNumero;


class NotaCreditoBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_BEFORE_INSERT => 'create',
       ];
   }

   public function create(Event $event)
   {                            
    //   $documentoNumero = DocumentoNumero::findByDocumentId(Documento::AVISO_CREDITO);
    //   $documentoNumero->saveNexNumber();

        
   }

}