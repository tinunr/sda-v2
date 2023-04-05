
<?= \app\modules\cnt\components\BalanceteFluxoCaixaWidget::widget([
        'titleReport'=>$titleReport,
        'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
        'bas_ano_id' =>$data['RazaoItemSearch']['bas_ano_id'],
        'cnt_plano_fluxo_caixa_id'=>$data['RazaoItemSearch']['cnt_plano_fluxo_caixa_id'],
        'data'=>$data['RazaoItemSearch'],
    ]) ?>