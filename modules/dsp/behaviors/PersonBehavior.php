<?php

namespace app\modules\dsp\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\dsp\models\Person; 

class PersonBehavior extends Behavior
{

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run_insert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'run_update',
        ];
    }

    public function run_insert(Event $event)
    {    
        $name = self::updateName($this->owner);
        if($name){
            $this->owner->nome = $name;
        }
    }




    public function run_update(Event $event)
    {
        $name = self::updateName($this->owner);
        if($name){
            $this->owner->nome = $name;
        }
         
    }

    public static function updateName($person)
    {
        $data = \app\modules\efatura\services\MiddlewareCoreService::taxpayerSearch($person->nif);
        if($data->succeeded && !empty($data->payload[0]->Name)){
        return $data->payload[0]->Name;
        } 
        return false;
    }
}
