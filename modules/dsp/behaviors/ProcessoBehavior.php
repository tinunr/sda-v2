<?php

namespace app\modules\dsp\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\ProcessoWorkflow;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\models\Parameter;
use yii\db\Expression;
use app\modules\dsp\services\ProcessoService;
use app\components\helpers\UploadFileHelper;
use app\modules\dsp\models\ProcessoWorkflowStatus;

class ProcessoBehavior extends Behavior
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
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::PROCESSO_ID);
        $documentoNumero->saveNexNumber();
        UploadFileHelper::setUrlFile($this->owner->id);
        $workflow = new ProcessoWorkflow();
        $workflow->dsp_processo_id = $this->owner->id;
        $workflow->status = Parameter::getValue('PROCESSO', 'WORKFLOW_STATUS_DEFAULT');
        $workflow->user_id = Parameter::getValue('PROCESSO', 'WORKFLOW_USER_DEFAULT');
        $workflow->dsp_setor_id = Parameter::getValue('PROCESSO', 'WORKFLOW_SETOR_DEFAULT');
        $workflow->in_data_hora = new Expression('NOW()');
        $workflow->recebeu_data_hora = new Expression('NOW()');
        $workflow->data_inicio = new Expression('NOW()');
        if ($workflow->save()) {
            if (!empty($this->owner->user_id)) {
                $workflowS = new ProcessoWorkflow();
                $workflowS->dsp_processo_id = $this->owner->id;
                $workflowS->status = ProcessoWorkflowStatus::RECEBIDO;
                $workflowS->user_id = $this->owner->user_id;
                $workflowS->dsp_setor_id = $this->owner->dsp_setor_id;
                $workflowS->in_workflow_id = $workflow->id;
                $workflowS->data_inicio = new Expression('NOW()');
                if ($workflowS->save()) {
                    $workflow->out_workflow_id = $workflowS->id;
                    $workflowS->data_fim = new Expression('NOW()');
                    $workflow->status = ProcessoWorkflowStatus::ENVIADO;
                    $workflow->save();
                }
            }
        }

        if ($this->owner->status != ProcessoStatus::CONCLUIDO && $this->owner->status != ProcessoStatus::PARCEALMENTE_CONCLUIDO) {
                $this->owner->status = ProcessoStatus::EM_EXECUCAO;
                $obs = \app\modules\dsp\models\ProcessoObs::find()->where(['dsp_processo_id' => $this->owner->id, 'status' => 0])->count();
                if ($obs > 0) {
                    $this->owner->status = ProcessoStatus::PENDENTE;
                }
                if (!empty($this->owner->nord->despacho->id) || !empty($this->owner->n_registro_tn)) {
                    $this->owner->status = ProcessoStatus::REGISTRADO;
                }
                if (!empty($this->owner->nord->despacho->numero_liquidade)) {
                    $this->owner->status = ProcessoStatus::LIQUIDADO;
                }
                if (!empty($this->owner->nord->despacho->n_receita)) {
                    $this->owner->status = ProcessoStatus::RECEITADO;
                }
                if (!empty($this->owner->nord->despacho->anulado)) {
                    $this->owner->status = ProcessoStatus::STATUS_ANULADO;
                }
                if (!empty($this->owner->n_registro_tn)) {
                    $this->owner->status = ProcessoStatus::CONCLUIDO;
                }
        }
        ProcessoService::setStatusOperacional($this->owner->id, $this->owner->status);
        ProcessoService::setStatusFinanceiro($this->owner->id);
    }




    public function run_update(Event $event)
    {
        if ($this->owner->status != ProcessoStatus::CONCLUIDO && $this->owner->status != ProcessoStatus::PARCEALMENTE_CONCLUIDO) {
                $this->owner->status = ProcessoStatus::EM_EXECUCAO;
                $obs = \app\modules\dsp\models\ProcessoObs::find()->where(['dsp_processo_id' => $this->owner->id, 'status' => 0])->count();
                if ($obs > 0) {
                    $this->owner->status = ProcessoStatus::PENDENTE;
                }
                if (!empty($this->owner->nord->despacho->id) || !empty($this->owner->n_registro_tn)) {
                    $this->owner->status = ProcessoStatus::REGISTRADO;
                }
                if (!empty($this->owner->nord->despacho->numero_liquidade)) {
                    $this->owner->status = ProcessoStatus::LIQUIDADO;
                }
                if (!empty($this->owner->nord->despacho->n_receita)) {
                    $this->owner->status = ProcessoStatus::RECEITADO;
                }
                if (!empty($this->owner->nord->despacho->anulado)) {
                    $this->owner->status = ProcessoStatus::STATUS_ANULADO;
                }
                if (!empty($this->owner->n_registro_tn)) {
                    $this->owner->status = ProcessoStatus::CONCLUIDO;
                }
            }
        ProcessoService::setStatusOperacional($this->owner->id, $this->owner->status);
    }
}
