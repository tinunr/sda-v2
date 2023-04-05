<?php

namespace app\modules\fin\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\models\Documento;
use app\models\DocumentoNumero;
use app\modules\fin\models\Receita;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\services\FaturaService;

class FaturaDefinitivaBehavior extends Behavior
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
        if (($n = FaturaDefinitiva::find()->where(['bas_ano_id' => $this->owner->bas_ano_id])->max('numero')) > 0) {
            $number = $n + 1;
        }
        $this->owner->numero = $number;
    }

    public function afterInsert(Event $event)
    {
        $documentoNumero = DocumentoNumero::findByDocumentId(Documento::FATURA_PROVISORIA_ID);
        $documentoNumero->saveNexNumber();
        if ($this->owner->fin_fatura_definitiva_serie == FaturaDefinitiva::FATURA_DEFINITIVA_SERIE_B) {

            $receita = new Receita();
            $receita->fin_fataura_definitiva_id =  $this->owner->id;
            $receita->fin_receita_tipo_id =  Receita::FATURA_DEFINITIVA;
            $receita->valor = $this->owner->valor;
            $receita->valor_recebido = 0;
            $receita->saldo = $this->owner->valor;
            $receita->descricao = 'Receita gerada a partir de fatura definitiva nº ' . $this->owner->numero . '/' . $this->owner->bas_ano_id;
            $receita->dsp_person_id =  $this->owner->dsp_person_id;
            $receita->bas_ano_id = $this->owner->bas_ano_id;
            $receita->status =  1;
            $receita->data =  $this->owner->data;
            $receita->save();
        }

        \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($this->owner->dsp_processo_id);
    }





    public function afterUpdate(Event $event)
    {
        if ($this->owner->fin_fatura_definitiva_serie == FaturaDefinitiva::FATURA_DEFINITIVA_SERIE_B) {
            if (($receita = Receita::findOne(['dsp_fataura_provisoria_id' => $this->owner->id, 'fin_receita_tipo_id' => Receita::FATURA_DEFINITIVA])) == null) {
                $receita = new Receita();
            } else {
                $receita->fin_receita_tipo_id =  Receita::FATURA_DEFINITIVA;
                $receita->fin_fataura_definitiva_id =  $this->owner->id;
                $receita->valor = $this->owner->valor;
                $receita->valor_recebido = 0;
                $receita->saldo = $this->owner->valor;
                $receita->descricao = 'Receita gerada a partir de fatura definitiva nº ' . $this->owner->numero . '/' . $this->owner->bas_ano_id;
                $receita->dsp_person_id =  $this->owner->dsp_person_id;
                $receita->bas_ano_id = $this->owner->bas_ano_id;
                $receita->status =  1;
                $receita->data =  $this->owner->data;
                $receita->save();
            }
            \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($this->owner->dsp_processo_id);
        }
        /**
         * criar fatura quando a FD foi validado
         */
        if ($this->owner->send ==  FaturaDefinitiva::ENVIADO) {
            FaturaService::createFatura($this->owner->id);
            FaturaService::createDebitoCliente($this->owner->id);
        }
    }
}
