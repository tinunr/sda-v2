<?php

namespace app\modules\dsp\services;

use yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\Setor;
use app\modules\dsp\models\ProcessoDespachoDocumento;
use app\modules\dsp\models\ProcessoObs;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\ProcessoStatusFinanceiro;
use app\modules\dsp\models\ProcessoStatusOperacional;
use app\modules\fin\models\FaturaProvisoria;


/**
 * 
 */
class ProcessoService extends Component
{
    /**
     * Cria ou atualiza histórico de estado oeracionais.
     * @return mixed
     */
    public static function setStatusOperacional($dsp_processo_id, $dsp_processo_status_id)
    {
        $processo = Processo::findOne($dsp_processo_id);
        $model = ProcessoStatusOperacional::find()
            ->where(['dsp_processo_id' => $dsp_processo_id])
            ->orderBy('id DESC')
            ->limit(1)
            ->one();
        // print_r($model);die();
        if (!empty($model)) {
            // print_r($model);die();
            if ($model->dsp_processo_status_id != $dsp_processo_status_id) {
                $new_model = new ProcessoStatusOperacional();
                $new_model->dsp_processo_id = $dsp_processo_id;
                $new_model->dsp_processo_status_id = $dsp_processo_status_id;
                $new_model->user_id = $processo->user_id;
                $new_model->dsp_setor_id = $processo->dsp_setor_id;
                $new_model->save();
            }
        } else {
            $new_model = new ProcessoStatusOperacional();
            $new_model->dsp_processo_id = $dsp_processo_id;
            $new_model->dsp_processo_status_id = $dsp_processo_status_id;
            $new_model->user_id = $processo->user_id;
            $new_model->dsp_setor_id = $processo->dsp_setor_id;
            if (!$new_model->save()) {
                print_r($new_model->errors);
                die();
            }
        }
    }

    /**
     * Cria ou atualiza histórico de estado financeiro.
     * @return mixed
     */
    public static function setStatusFinanceiro($dsp_processo_id)
    {
        $descricao = [];
        $dsp_processo_status_id = ProcessoStatus::FIN_SEM_FATURA_PROVISORIA;
        $processo = Processo::findOne($dsp_processo_id);
        $model = ProcessoStatusFinanceiro::find()
            ->where(['dsp_processo_id' => $dsp_processo_id])
            ->orderBy('id DESC')
            ->limit(1)
            ->one();
        if (!empty($processo->faturaProvisorio)) {
            $dsp_processo_status_id = ProcessoStatus::FIN_FATURA_PROVISORIA_EMETIDO;
            foreach ($processo->faturaProvisorio as $key => $faturaProvisoria) {
                if (!in_array($faturaProvisoria->numero . '/' . $faturaProvisoria->bas_ano_id, $descricao)) {
                    $descricao[] = $faturaProvisoria->numero . '/' . $faturaProvisoria->bas_ano_id;
                }
            }
            if (!empty($processo->faturaDefinitiva)) {
                $dsp_processo_status_id = ProcessoStatus::FIN_FATURA_DEFINITIVA;
                foreach ($processo->faturaDefinitiva as $key => $faturaDefinitiva) {
                    if (!in_array($faturaDefinitiva->numero . '/' . $faturaDefinitiva->bas_ano_id, $descricao)) {
                        $descricao[] = $faturaDefinitiva->numero . '/' . $faturaDefinitiva->bas_ano_id;
                    }
                }
            } else {
                if (!empty($processo->despesa)) {
                    foreach ($processo->despesa as $despesa) {
                        foreach ($despesa->pagamentoItem as $pagamentoItem) {
                            $dsp_processo_status_id = ProcessoStatus::FIN_PAGO_COM_FATURA_PROVISORIA;
                            if (!in_array($pagamentoItem->pagamento->numero . '/' . $pagamentoItem->pagamento->bas_ano_id, $descricao)) {
                                $descricao[] = $pagamentoItem->pagamento->numero . '/' . $pagamentoItem->pagamento->bas_ano_id;
                            }
                        }
                    }
                } elseif (!empty($processo->faturaProvisorio)) {
                    foreach ($processo->faturaProvisorio as $key => $faturaProvisoria) {
                        if (!empty($faturaProvisoria->receita->recebimentoItem)) {
                            foreach ($faturaProvisoria->receita->recebimentoItem as $key => $recebimentoItem) {
                                $dsp_processo_status_id = ProcessoStatus::FIN_RECEBIDO;
                                if (!in_array($recebimentoItem->recebimento->numero . '/' . $recebimentoItem->recebimento->bas_ano_id, $descricao)) {
                                    $descricao[] = $recebimentoItem->recebimento->numero . '/' . $recebimentoItem->recebimento->bas_ano_id;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if (!empty($processo->despesa)) {
                foreach ($processo->despesa as $despesa) {
                    foreach ($despesa->pagamentoItem as $pagamentoItem) {
                        $dsp_processo_status_id = ProcessoStatus::FIN_PAGO_SEM_FATURA_PROVISORIA;
                        if (!in_array($pagamentoItem->pagamento->numero . '/' . $pagamentoItem->pagamento->bas_ano_id, $descricao)) {
                            $descricao[] = $pagamentoItem->pagamento->numero . '/' . $pagamentoItem->pagamento->bas_ano_id;
                        }
                    }
                }
            }
        }

        $new_descricao = '';
        foreach ($descricao as $value) {
            $new_descricao = $value . ', ' . $new_descricao;
        }

        if (!empty($model)) {
            if ($model->dsp_processo_status_id == $dsp_processo_status_id) {
                $model->descricao = $new_descricao;
                $model->save();
            } else {
                $new_model = new ProcessoStatusFinanceiro();
                $new_model->dsp_processo_status_id = $dsp_processo_status_id;
                $new_model->descricao = $new_descricao;
                $new_model->dsp_processo_id = $processo->id;
                $new_model->user_id = $processo->user_id;
                $new_model->dsp_setor_id = $processo->dsp_setor_id;
                $new_model->save();
            }
        } else {
            $new_model = new ProcessoStatusFinanceiro();
            $new_model->dsp_processo_status_id = $dsp_processo_status_id;
            $new_model->descricao = $new_descricao;
            $new_model->dsp_processo_id = $processo->id;
            $new_model->user_id = $processo->user_id;
            $new_model->dsp_setor_id = $processo->dsp_setor_id;
            $new_model->save();
        }
        $processo->status_financeiro_id = $dsp_processo_status_id;
        $processo->save();
        return $dsp_processo_status_id;
    }

    public function getProcessoAnexo(int $dsp_processo_id)
    {
        $result = '';
        $documentos = ProcessoDespachoDocumento::find()
            ->where(['dsp_processo_id' => $dsp_processo_id])
            ->andWhere(['>', 'bas_ficheiro_id', 0])
            ->all();
        if (!empty($documentos)) {
            foreach ($documentos as $key => $documento) {
                if ($key == 0) {
                    $result = $result . ' ' . Html::a($documento->despachoDocumento->sigla, Url::to('@web/' . $documento->ficheiro->file_pacth), ['class' => ' btn-link', 'target' => '_blank']);
                } else {
                    $result = $result . ' | ' . Html::a($documento->despachoDocumento->sigla, Url::to('@web/' . $documento->ficheiro->file_pacth), ['class' => ' btn-link', 'target' => '_blank']);
                }
            }
        }
        return $result;
    }



    public function alterarProcessoNomeFatura(int $dsp_processo_id, int $nome_fatura)
    {
        $processo = Processo::findOne($dsp_processo_id);
        $processo->nome_fatura = $nome_fatura;
        $processo->save(false);

        $fps = FaturaProvisoria::find()->where(['dsp_processo_id' => $dsp_processo_id])->all();
        if (!empty($fps)) {
            foreach ($fps as $key => $fp) {
                $fp->dsp_person_id = $nome_fatura;
                $fp->save(false);
                $receita = $fp->receita;
                $receita->dsp_person_id = $nome_fatura;
                $receita->save(false);
            }
        }
    }


    public function temProcessoExecucao(int $user_id)
    {
        $processo = Processo::find()
            ->where(['user_id' => $user_id])
            ->andWhere(['status' => 2])
            ->andWhere(['>0', 'bas_ano_id' => 20])
            ->count();
        return ($processo > 0) ? true : false;
    }


    public static function bloquiarProcesso(int $dsp_processo_id)
    {
        $processo = Processo::find()
            ->where(['id' => $dsp_processo_id])
            ->one();
        if ($processo->dsp_setor_id == Setor::ARQUIVO_ID || $processo->dsp_setor_id == Setor::ARQUIVO_PROVISORIO_ID) {
            return false;
        }
        return true;
    }
}
