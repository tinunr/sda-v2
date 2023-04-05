<?php 
$total = 0;

?>
<table class="table table-striped  table-bordered">
    <tr>
        <th>#</th>
        <th>Meio Financeiro</th>
        <th>Conta</th>
        <th>Valor</th>
    </tr>
    <?php $i=1; foreach ($dataProvider->getModels() as $key => $model):?>
    <?php $total = $total + $model->saldo;?>
        <tr>
            <td><?=$i?></td>
            <td><?=$model->banco->sigla?></td>
            <td><?=$model->numero?></td>
            <td><?=Yii::$app->formatter->asCurrency($model->saldo)?></td>
        </tr>
    <?php $i++; endforeach;?>
     <tr>
            <th colspan="3">TOTAL</th>
            <th><?=Yii::$app->formatter->asCurrency($total)?></th>
        </tr>
</table>
  