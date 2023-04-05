<?php

namespace app\modules\fin\services;

use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\RecebimentoItem;
use app\modules\fin\models\Receita;
use app\modules\fin\models\OfAccounts;
use app\modules\fin\models\OfAccountsItem;

/**
 * 
 */
class FaturaProvisoriaService
{

    /**
     * Vereficar se a despesa tem fatura definitiva
     * @var integer
     */
    public static function atualizarValorRecebido($fp_id)
    {
        $valorRecebido = 0;
        $fp = FaturaProvisoria::find()->where(['id' => $fp_id])->one();
        $receita = Receita::find()->where(['dsp_fataura_provisoria_id' => $fp_id])->one();
        $recebimentoItem = RecebimentoItem::find()
            ->joinWith(['recebimento'])
            ->where(['fin_recebimento_item.fin_receita_id' => $receita->id])
            ->andWhere(['fin_recebimento.status' => 1])
            ->all();

        foreach ($recebimentoItem as $recebimentoItem) {
            $valorRecebido = $valorRecebido + $recebimentoItem->valor_recebido;
        }

        $ofAccounts = OfAccounts::find()
            ->where(['fin_receita_id' => $receita->id])
            ->andWhere(['status' => 1])
            ->all();

        foreach ($ofAccounts as $ofAccount) {
            $valorRecebido = $valorRecebido + $ofAccount->valor;
        }

        $ofAccountsItem = OfAccountsItem::find()
            ->joinWith(['ofAccounts'])
            ->where(['fin_of_accounts_item.fin_receita_id' => $receita->id])
            ->andWhere(['fin_of_accounts.status' => 1])
            ->all();
        foreach ($ofAccountsItem as $ofAccountsItem) {
            $valorRecebido = $valorRecebido + $ofAccountsItem->valor;
        }
        $receita->valor = $fp->valor;
        $receita->saldo = $fp->valor - $valorRecebido;
        $receita->valor_recebido =  $valorRecebido;
        $receita->save();
    }
}
