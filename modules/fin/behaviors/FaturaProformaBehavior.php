<?php

namespace app\modules\fin\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;

class FaturaProformaBehavior extends Behavior
{

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'create',
            ActiveRecord::EVENT_AFTER_UPDATE => 'update',
        ];
    }

    public function create(Event $event)
    {
        $number = 1;
        $this->owner->bas_ano_id = substr(date('Y', strtotime($this->owner->data)), -2);
        if (($n = \app\modules\fin\models\FaturaProforma::find()->where(['bas_ano_id' => $this->owner->bas_ano_id])->max('numero')) >= 1) {
            $number = $n + 1;
        }
        $this->owner->numero = $number;
    }





    public function update(Event $event)
    {
    }
}
