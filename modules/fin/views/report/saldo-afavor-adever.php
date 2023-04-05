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
      <th>Valor Recebido</th>
      <th>Valor Pago</th>
      <th>Reembolso</th>
      <th>Aviso credito</th>
      <th>Nota credito</th>
      <th>Valor a favor</th>
      <th>Valor a dever</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $total_pago = 0;
    $total_recebido = 0;
    $total_despesa = 0;
    $total_saldo_af = 0;
    $total_saldo_ad = 0;
    $reembolco_afavor_cliente = 0;
    $aviso_credito = 0;
    $nota_credito = 0;
    ?>
    <?php foreach ($dataProvider->getModels() as $key => $value):?>
      
    <?php 
    $total_recebido = $total_recebido + $value['valor_recebido'];
    $total_despesa = $total_despesa + $value['valor_despesa'];
    $total_pago = $total_pago + $value['valor_pago'];
    $reembolco_afavor_cliente = $reembolco_afavor_cliente + $value['reembolco_afavor_cliente'];
    $aviso_credito = $aviso_credito + $value['aviso_credito'];
    $nota_credito = $nota_credito + $value['nota_credito'];
    
    ?>
    <tr>
      <td><?=$value['numero']?></td>
      <td><?=$value['fatura_provisorias']?></td>
      <td><?=$value['nord']?></td>
      <td><?=$value['person']?></td>
      <td><?=$formatter->asCurrency($value['valor_recebido'])?></td>
      <td><?=$formatter->asCurrency($value['valor_pago'])?></td>
      <td><?=$formatter->asCurrency($value['reembolco_afavor_cliente'])?></td>
      <td><?=$formatter->asCurrency($value['aviso_credito'])?></td>
      <td><?=$formatter->asCurrency($value['nota_credito'])?></td>
      <?php //$saldo = ($valorRealRecebido + ($value['reembolco_afavor_cliente'] )-$value['valor_pago']);
      if($value['saldo'] >0){
      $total_saldo_af = $total_saldo_af + $value['saldo']; 
      }else{
      $total_saldo_ad = $total_saldo_ad + abs($value['saldo']); 
      }?>
      <td>
       <?= $value['saldo'] >0?$formatter->asCurrency($value['saldo']):0?>
      </td>
      <td>
       <?= $value['saldo'] <0?$formatter->asCurrency(abs($value['saldo'])):0?>
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
      <th><?=$formatter->asCurrency($total_pago) ?></th>
      <th><?=$formatter->asCurrency($reembolco_afavor_cliente) ?></th>
      <th><?=$formatter->asCurrency($aviso_credito) ?></th>
      <th><?=$formatter->asCurrency($nota_credito) ?></th>
      <th><?=$formatter->asCurrency($total_saldo_af) ?></th>
      <th><?=$formatter->asCurrency($total_saldo_ad) ?></th>
    </tr>
  </tfoot>
   
 </table>

