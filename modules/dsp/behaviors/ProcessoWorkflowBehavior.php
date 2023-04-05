<?php

namespace app\modules\dsp\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\ProcessoWorkflow;

class ProcessoWorkflowBehavior extends Behavior
{

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run_insert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'run_update',
        ];
    }

    public function run_insert(Event $event)
    {
        if ($this->owner->status == 2 || $this->owner->status == 5 || $this->owner->status == 6) {
            $processo = Processo::findOne($this->owner->dsp_processo_id);
            $processo->user_id = $this->owner->user_id;
            $processo->dsp_setor_id = $this->owner->dsp_setor_id;
            $processo->save();
        }

        if (!empty($this->owner->in_workflow_id)) {
            if ($this->owner->status == 5 || $this->owner->status == 6) {
                $old  = processoWorkflow::findOne($this->owner->in_workflow_id);
                $old->status = 5;
                $old->save();
            }
        }
    }

    public function run_update(Event $event)
    {
        if ($this->owner->status == 2 || $this->owner->status == 5 || $this->owner->status == 6) {
            $processo = Processo::findOne($this->owner->dsp_processo_id);
            $processo->user_id = $this->owner->user_id;
            $processo->dsp_setor_id = $this->owner->dsp_setor_id;
            $processo->save();
        }


        if (!empty($this->owner->in_workflow_id)) {
            if ($this->owner->status == 2) {
                if (($old  = processoWorkflow::findOne($this->owner->in_workflow_id)) != NULL) {
                    $old->status = 3;
                    $old->save();
                }
            }
        }
    }
}
