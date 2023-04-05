<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
$formatter = Yii::$app->formatter;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DesciplinaPerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Lista a DEVER';

?>

<section>
<p>
<?=Html::a('Imprimir',['valor-adever-pf','dataProvider'=>$dataProvider],['class'=>'btn btn-success','target'=>'_blank'])?>

</p>
 <table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Nº PR</th>
      <th>Nº FP(s)</th>
      <th>NORD</th>
      <th>Cliente</th> 
      <th title="Valor efectivamente  recebido das FP (dinheiro; CH, Dep, Enc.Contas) [ d ]">Valor Recebido</th>
      <th title="Valor total item da Despesa na FP [ e ] ">Valor Despesa</th>
      <th title="Devolução da Alfândega - Favor Cliente [ f ] ">Devolução da Alfândega - Favor Cliente</th>
      <th title="Aviso Crédito [ g ] ">Aviso Crédito </th>
      <th title="Nota Crédito [ h ] ">Nota Crédito </th>
      <th title="Valor Recebido para Despesas   [ se (d)> 0 e (d) > ( e);  valor = ( e)+(f)-(g)-(h); se (d)>0 e d< (  e) ; valor = (d)+(f)-(g)-(h); se (d)=0; Valor = 0) ] [ a ]">Valor Recebido para Despesas</th>
      <th title="Pagamentos efectuados [ b ] ">Pagamentos efectuados </th>
      <th title="Saldo a Dever [ (b) -(a) Se (b)>(a); Valor=(b)-(a); se (b)<=(a); valor=0) ]">Saldo</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $aviso_creadito = 0;
    $valor_recebido = 0;
    $nota_creadito = 0;
    $valor_total_item_da_despesa = 0;
    $valor_recebido_para_despesa = 0;
    $devolucao_alfandega = 0;
    $total_pago =0;
    $saldo = 0;
    ?>
    <?php foreach ($dataProvider->getModels() as $key => $value):?>
    <?php 
    $valor_recebido = $valor_recebido + $value['valor_recebido'];
    $valor_total_item_da_despesa = $valor_total_item_da_despesa + $value['valor_total_item_da_despesa'];
    $devolucao_alfandega = $devolucao_alfandega + $value['devolucao_alfandega']; 
    $aviso_creadito = $aviso_creadito + $value['aviso_creadito']; 
    $nota_creadito = $nota_creadito + $value['nota_creadito']; 
    $valor_recebido_para_despesa = $valor_recebido_para_despesa + $value['valor_recebido_para_despesa']; 
    $total_pago = $total_pago + $value['total_pago']; 
    $saldo = $saldo + $value['saldo']; 
    ?>
    <tr>
      <td><?=$value['numero']?></td>
      <td><?=$value['fatura_provisorias']?></td>
      <td><?=$value['nord']?></td>
      <td><?=$value['person']?></td>
      <td ><?=$formatter->asCurrency($value['valor_recebido'])?></td>
      <td><?=$formatter->asCurrency($value['valor_total_item_da_despesa'])?></td>
      <td> <?=$formatter->asCurrency($value['devolucao_alfandega'])?> </td>
      <td> <?=$formatter->asCurrency($value['aviso_creadito'])?> </td>
      <td> <?=$formatter->asCurrency($value['nota_creadito'])?> </td> 
      <td> <?=$formatter->asCurrency($value['valor_recebido_para_despesa'])?> </td> 
      <td> <?=$formatter->asCurrency($value['total_pago'])?> </td> 
      <td><?=$formatter->asCurrency($value['saldo'])?></td> 
    </tr>
  <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr>
      <th >TOTAL</th>
      <th></th>
      <th></th>
      <th></th>
      <th><?=$formatter->asCurrency($valor_recebido) ?></th>
      <th><?=$formatter->asCurrency($valor_total_item_da_despesa) ?></th>
      <th><?=$formatter->asCurrency($devolucao_alfandega) ?></th>
      <th><?=$formatter->asCurrency($aviso_creadito) ?></th>
      <th><?=$formatter->asCurrency($nota_creadito) ?></th>
      <th><?=$formatter->asCurrency($valor_recebido_para_despesa) ?></th>
      <th><?=$formatter->asCurrency($total_pago) ?></th>
      <th><?=$formatter->asCurrency($saldo) ?></th>
    </tr>
  </tfoot>
   
 </table>

</section>