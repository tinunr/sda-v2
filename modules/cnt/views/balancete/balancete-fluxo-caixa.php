<?= \app\modules\cnt\widget\BalanceteFluxoCaixa::widget([
        'bas_ano_id' =>$data['RazaoItemSearch']['bas_ano_id'],
        'bas_ano'=>$bas_ano,
        'bas_mes_id' =>$data['RazaoItemSearch']['bas_mes_id'],
        'cnt_plano_fluxo_caixa_id'=>$data['RazaoItemSearch']['cnt_plano_fluxo_caixa_id'],
        'cnt_plano_terceiro_id'=>$data['RazaoItemSearch']['cnt_plano_terceiro_id'],
    ]);