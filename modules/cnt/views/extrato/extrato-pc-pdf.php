<div>
<?= \app\modules\cnt\widget\ExtratoContaWidget::widget([
		'mes'=>02,
		'ano' => 2019,
		'cnt_plano_conta_id'=>$data['RazaoItemSearch']['cnt_plano_conta_id'],
		'data'=>$data['RazaoItemSearch'],
		'titleRepor'=>$titleReport,
	]) ?>

</div>