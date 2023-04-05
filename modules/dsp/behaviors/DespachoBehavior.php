<?php

namespace app\modules\dsp\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;

class DespachoBehavior extends Behavior
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
        if (($nord = \app\modules\dsp\models\Nord::findOne(['numero' => $this->owner->nord,'bas_ano_id'=>$this->owner->bas_ano_id,'dsp_desembaraco_id'=>$this->owner->dsp_desembaraco_id])) !== null) 
        { 
     
          if(($model = \app\modules\dsp\models\Processo::findOne(['id'=>$nord->dsp_processo_id]))!=null)
          {
          $model->status = 4;
          $obs = \app\modules\dsp\models\ProcessoObs::find()->where(['dsp_processo_id'=>$model->id,'status'=>0])->count();
          
                if (!empty($this->owner->numero_liquidade)) {
                    $model->status = 5; 
                }
                if (!empty($this->owner->n_receita)) {
                    $model->status = 6;
                } 
                if (!empty($this->owner->anulado)) {
                    $model->status = 11;
                }
          
          $model->save();
        }
   }
  }


}