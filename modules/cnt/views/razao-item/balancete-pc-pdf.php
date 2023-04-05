<?php 
use app\models\Ano;
?>
<?= \app\modules\cnt\components\BalanceteContaWidget::widget([
        'titleReport'=>$titleReport,
        'bas_mes_id'=>$data['RazaoItemSearch']['bas_mes_id'],
        'bas_ano_id' =>$data['RazaoItemSearch']['bas_ano_id'],
        'cnt_plano_conta_id'=>$data['RazaoItemSearch']['cnt_plano_conta_id'],
        'data'=>$data['RazaoItemSearch'],
    ]) ?>