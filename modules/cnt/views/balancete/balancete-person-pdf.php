<?php 
use app\models\Ano;
?>
<section>
<?= \app\modules\cnt\widget\BalancetePersonWidget::widget([
        'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
        'bas_ano_id' =>$data['RazaoItemSearch']['bas_ano_id'],
        'bas_ano' =>$bas_ano,
        'cnt_plano_conta_id'=>$data['RazaoItemSearch']['cnt_plano_conta_id'],
        'cnt_plano_terceiro_id'=>$data['RazaoItemSearch']['cnt_plano_terceiro_id'],
    ]) ?>
</section>