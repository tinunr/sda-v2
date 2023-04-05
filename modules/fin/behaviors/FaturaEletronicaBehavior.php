<?php

namespace app\modules\fin\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\modules\fin\models\FaturaEletronica;

class FaturaEletronicaBehavior extends Behavior
{

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }


    public function beforeInsert(Event $event)
    {
        $number = 1;
        $this->owner->bas_ano_id = substr(date('Y', strtotime($this->owner->data)), -2);
        if (($n = FaturaEletronica::find()->where(['bas_ano_id' => $this->owner->bas_ano_id])->max('numero')) > 0) {
            $number = $n + 1;
        }
        $this->owner->numero = $number;
    }

    public function afterInsert(Event $event)
    {
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::FATURA_PROVISORIA_ID);
        $documentoNumero->saveNexNumber();
        // enviar a fatura eletronica para plataforma defatura 
        \app\modules\efatura\services\MiddlewareCoreDefService:: sendDEF($this->owner->id,'FTE');
    }

    public function afterUpdate(Event $event)
    {
    }
}
