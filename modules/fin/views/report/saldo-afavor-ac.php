<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
$formatter = Yii::$app->formatter;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DesciplinaPerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Processos';
$total_valor=0;
$total_pago=0;
$total_saldo=0;
?>

 <table class="table table-striped">
  <thead>
    <tr>
      <th>Nº AC</th>
      <th>Nº PR</th>
      <th>Cliente</th>
      <th class="text-right">Valor</th>
      <th class="text-right">Valor Usado</th>
      <th class="text-right">Saldo</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($dataProvider->getModels() as $key => $value): ?>
        <?php 
    $total_valor= $total_valor + $value['valor'];
    $total_pago = $total_pago + $value['total_pago'];
    $saldo = ($value['valor']-$value['total_pago']);
    $total_saldo = $total_saldo + $saldo;
    
    ?>
    <tr>
      <td><?=$value['numero']?></td>
      <td><?=$value['numero_pr']?></td>
      <td><?=$value['personName']?></td>
      <td class="text-right"><?=$formatter->asCurrency($value['valor'])?></td>
      <td class="text-right"><?=$formatter->asCurrency($value['total_pago'])?></td>
      <td class="text-right"><?=$formatter->asCurrency($saldo)?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
  <tr>
      <th colspan="3">TOTAL</th>
      <th class="text-right"><?=$formatter->asCurrency($total_valor) ?></th>
      <th class="text-right"><?=$formatter->asCurrency($total_pago) ?></th>
      <th class="text-right"><?=$formatter->asCurrency($total_saldo) ?></th>
    </tr>
   
 </table>

