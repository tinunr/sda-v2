<?php

namespace app\modules\fin\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\CaixaOperacao;

class CaixaBehavior extends Behavior
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

    }

  public function update(Event $event)
   {
      
    }
      
  
}