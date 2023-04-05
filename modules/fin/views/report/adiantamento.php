<?php 
use yii\helpers\Html;
$formatter = Yii::$app->formatter;
$valor_recebido = 0;
$valor_despesa = 0;
$valor_pago = 0;
$saldo = 0;
?>
<section>
<div>
    <table class="table table-hover table-border table-hover">

   <tr>
    <th>#</th>
    <th>Adiantamento</th>
    <th>Descrição</th>
    <th>Data</th>
    <th>1. Valor Recebido</th>
    <th>2. Valor Despesa</th>
    <th>3. Valor Pago</th>
    <th>(1-3). Saldo</th> 
    </tr>
    <?php   foreach($dataProvider->getModels() as $key=> $model):?>

        <?php
        $valor_recebido = $valor_recebido + $model['valor_recebido'];
        $valor_despesa = $valor_despesa + $model['valor_despesa'];
        $valor_pago = $valor_pago + $model['valor_pago'];
        $saldo = $saldo + $model['valor_recebido']-$model['valor_pago'] ;
        ?>
        <tr>
            <td><?=$key+1?></td>
            <td><?=Html::a($model['numero'],['/fin/recebimento/view','id'=>$model['id']],['class'=>'btn-link','target'=>'_blank'])?></td>
            <td style="width: 20%;"><?= $model['descricao']?></td>
            <td><?=$formatter->asDate($model['data'])?></td>
            <td><?=$formatter->asCurrency($model['valor_recebido'])?></td>
            <td style="<?=$model['valor_recebido']!= $model['valor_despesa']?'color:red;':''?>"><?=$formatter->asCurrency($model['valor_despesa'])?></td>
            <td><?=$formatter->asCurrency($model['valor_pago'])?></td>
            <td><?=$formatter->asCurrency($model['valor_recebido']-$model['valor_pago'] )?></td>
        </tr>
        <?php endforeach;?> 
         <tr>
             <th  ></th>
             <th  ></th>
             <th  ></th>
             <th  ></th>
             <th><?=$formatter->asCurrency($valor_recebido)?></th> 
             <th><?=$formatter->asCurrency($valor_despesa)?></th> 
             <th><?=$formatter->asCurrency($valor_pago)?></th> 
             <th><?=$formatter->asCurrency($saldo)?></th>   
         </tr>
    </table>
</div>
</section>