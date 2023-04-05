<?php
use app\modules\cnt\models\RazaoItemSearch;
use app\modules\cnt\models\PlanoConta;
$formatter = Yii::$app->formatter;
$total_debito = 0;
$total_credito = 0;
$total_saldo = 0;

// print_r($data);die();
?>

<div class="row">
	<p class="text-center">Mês: <?=$titleReport?></p>
	<p class="text-center">Moeda: Nacional</p>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Mês</th>
				<th>Dia</th>
				<th>Diário</th>
				<th>Docum.</th>
				<th>Anal/Ter </th>
				<th>Descritivo </th>
				<th>Debito </th>
				<th>Credito</th>
				<th>Saldo</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($dataProvider->getModels() as $key => $model): ?>
				<?php 
				
					$adao = Yii::$app->CntQuery->getAdao($model['codigo']); sort($adao);
				?>
			<?php foreach ($adao as $key => $value):  $planoConta = PlanoConta::findOne($value);?>
		
				<tr>
					<th colspan="2"><?=$value?></th>
					<th colspan="7"><?=!empty($planoConta->descricao)?$planoConta->descricao:null?></th>
				</tr>
			<?php endforeach;?>
			
			<tr>
				<th colspan="2"><?=$model['cnt_plano_terceiro_id']?></th>
				<th colspan="7"><?=$model['nome']?></th>
			</tr>

			
			
			<?php 
			$data['RazaoItemSearch']['cnt_plano_conta_id']=$model['codigo'];
			$searchModel = new RazaoItemSearch($data['RazaoItemSearch']);
         	$dataProvider = $searchModel->extrato(Yii::$app->request->queryParams);?>

			<?php foreach ($dataProvider->getModels()  as $key => $value) :?>
				<?php 
					$total_debito = $total_debito + $value['debito'];
					$total_credito = $total_credito + $value['credito'];
					$total_saldo = $total_saldo + ($value['debito']-$value['credito']);
				?>
			<tr>
				<td><?=$formatter->asDate($value['data'],'MM-Y')?></td>
				<td><?=$formatter->asDate($value['data'],'dd')?></td>
				<td><?=str_pad($value['cnt_diario_id'],2,'0',STR_PAD_LEFT)?></td>
				<td><?=$value['num_doc']?></td>
				<td><?=($value['terceiro']>0)?str_pad($value['terceiro'],6,'0',STR_PAD_LEFT):null?></td>
				<td><?=$value['descricao']?></td>
				<td><?=!$value['debito']?null:$formatter->asCurrency($value['debito'])?></td>
				<td><?=!$value['credito']?null:$formatter->asCurrency($value['credito'])?></td>
				<td><?=$formatter->asCurrency($total_saldo)?></td>
			</tr>
		<?php endforeach;?>
		<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>Totais Acumulados</th>
				<th><?=$formatter->asCurrency($total_debito)?></th>
				<th><?=$formatter->asCurrency($total_credito)?></th>
				<th></th>
			</tr>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>Saldo</th>
				<th></th>
				<th><?=$formatter->asCurrency($total_saldo)?></th>
				<th>BD</th>
			</tr>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>Total Control</th>
				<th><?=$formatter->asCurrency($total_debito)?></th>
				<th><?=$formatter->asCurrency($total_credito)?></th>
				<th></th>
			</tr>
		<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>Totais Acumulados</th>
				<th><?=$formatter->asCurrency($total_debito)?></th>
				<th><?=$formatter->asCurrency($total_saldo)?></th>
				<th></th>
			</tr>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>Saldo</th>
				<th><?=$total_saldo<0?$formatter->asCurrency(abs($total_saldo)):null?></th>
				<th><?=$total_saldo>0?$formatter->asCurrency(abs($total_saldo)):null?></th>
				<th><?=$total_saldo>0?'DB':'CR'?></th>
			</tr>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>Total Control</th>
				<th><?=$total_saldo<0?$formatter->asCurrency($total_debito+abs($total_saldo)):$formatter->asCurrency($total_debito)?></th>
				<th><?=$total_saldo>0?$formatter->asCurrency($total_credito+abs($total_saldo)):$formatter->asCurrency($total_credito)?></th>
				<th></th>
			</tr>
		</tfoot>
	</table>
</div>