<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
$formatter = Yii::$app->formatter;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DesciplinaPerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Processos';

?>

 <table class="table table-striped">
  <thead>
    <tr>
      <th>Nº PR</th>
      <th>Nº FP(s)</th>
      <th>NORD</th>
      <th>Cliente</th>
      <!-- <th>Mercadoria</th> -->
      <th>Valor Recebido</th>
      <th>Valor Despesa</th>
      <th>Valor Pago</th>
      <th>Saldo</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $total_pago = 0;
    $total_recebido = 0;
    $total_despesa = 0;
    $total_saldo = 0;
    ?>
    <?php foreach ($dataProvider->getModels() as $key => $value):?>
    <?php 
    $total_recebido = $total_recebido + $value['valor_recebido'];
    $total_despesa = $total_despesa + $value['valor_despesa'];
    $total_pago = $total_pago + $value['total_pago'];
    
    ?>
    <tr>
      <td><?=$value['numero']?></td>
      <td><?=$value['fatura_provisorias']?></td>
      <td><?=$value['nord']?></td>
      <td><?=$value['person']?></td>
      <td><?=$formatter->asCurrency($value['valor_recebido'])?></td>
      <td><?=$formatter->asCurrency($value['valor_despesa'])?></td>
      <td><?=$formatter->asCurrency($value['total_pago'])?></td>
      <td><?php $saldo = ($value['valor_recebido']<$value['valor_despesa'])?($value['valor_recebido']-$value['total_pago']):($value['valor_despesa']-$value['total_pago']);
      $total_saldo = $total_saldo + $saldo;

      ?>
      <?=$formatter->asCurrency($saldo)?>
        
      </td>
    </tr>
  <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <th>TOTAL</th>
      <th></th>
      <th></th>
      <th></th>
      <th><?=$formatter->asCurrency($total_recebido) ?></th>
      <th><?=$formatter->asCurrency($total_despesa) ?></th>
      <th><?=$formatter->asCurrency($total_pago) ?></th>
      <th><?=$formatter->asCurrency($total_saldo) ?></th>
    </tr>
  </tfoot>
   
 </table>

