<?= \app\modules\cnt\widget\BalanceteContaWidget::widget([
        'bas_ano_id' =>$data['RazaoItemSearch']['bas_ano_id'],
        'bas_ano' =>$bas_ano,
        'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
        'bas_mes_descricao'=>$bas_mes_descricao,
        'cnt_plano_conta_id'=>$data['RazaoItemSearch']['cnt_plano_conta_id'],
        'cnt_plano_terceiro_id'=>$data['RazaoItemSearch']['cnt_plano_terceiro_id'],
    ]) ;