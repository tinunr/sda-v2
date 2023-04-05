<?php

namespace app\modules\fin\services;


use yii;
use yii\base\Component;


use app\modules\fin\models\PagamentoOrdem;

/**
 * 
 */
class PagamentoOrdemService extends Component
{

    /**
     * Vereficar se a despesa tem fatura definitiva
     * @var integer
     */
    public static function countStatus()
    {
        $por_validar = PagamentoOrdem::find()->where(['status' => 1, 'send' => 0])->count();
        $validado = PagamentoOrdem::find()->where(['status' => 1, 'send' => 1])->count();
        return [
            'por_validar' => $por_validar,
            'validado' => $validado,
            'total' => $por_validar + $validado,
        ];
    }


    /**
     * Vereficar se a despesa foi criado antes da FP e agora já tem FP
     * @var integer
     */
    public function foiCriadoAntesFaturaProvisoria($fin_despesa_id)
    {
        $model = Despesa::findOne($fin_despesa_id);
        if (empty($model->fin_recebimento_id) && !empty($model->dsp_processo_id) && !empty($model->dsp_fatura_provisoria_id)) {
            $fatura = FaturaProvisoria::find()->where(['id' => $model->dsp_fatura_provisoria_id])->one();
            if (!empty($fatura)) {
                if ($model->created_at < $fatura->created_at) {
                    return true;
                }
            }
        }
        return false;
    }
}
