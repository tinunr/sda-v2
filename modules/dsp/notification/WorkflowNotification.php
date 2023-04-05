<?php

namespace app\modules\dsp\notification;

use Yii;
use app\components\services\NotificationService;

class WorkflowNotification extends NotificationService
{
    const WORKFLOW_CREATE = 'WORKFLOW_CREATE';
    const WORKFLOW_UPDATE = 'WORKFLOW_UPDATE';

    public $userId;
    public $dsp_processo_id;

    public function getService()
    {
        return 'DSP - Processo';
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        switch ($this->key) {
            case self::WORKFLOW_CREATE:
                return Yii::t('app', 'Novo processo Inicido Novo processo Inicido Novo processo Inicido Novo processo Inicido Novo processo Inicido Novo processo Inicido Novo processo Inicido');
            case self::WORKFLOW_UPDATE:
                return Yii::t('app', 'Instructions to reset the password');
        }
    }

    /**
     * @inheritdoc
     */
    public function getRoute()
    {
        return ['/dsp/processo-workflow/index'];
    }
}
