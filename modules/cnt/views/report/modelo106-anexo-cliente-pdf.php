<p class="text-center">ANO: <?=$bas_ano_id?> MÊS: <?=$bas_mes_id?></p>
<p class="text-center"><?=$titleReport?></p>



<table class="table table-striped">
  <thead>
<tr>
  <th>Origem</th>
  <th>NIF da Entidade</th>
  <th>Designação da Entidade</th>
  <th>Série</th>
  <th>Tipo Doc.</th>
  <th>Num. Doc.</th>
  <th>Data</th>
  <th>Valor Fatura</th>
  <th>Valor Base de Incidencia</th>
  <th>Taxa IVA</th>
  <th>IVA Liquidao</th>
  <th>Não Liquidao Imposto</th>
  <th>Valor destino</th>
</tr>
</thead>
<tbody>
<?php foreach($dataProvider->getModels() as $model):
  $origem = Yii::$app->CntQuery->origemData($model['cnt_razao_id'])?>

  <tr>
    <td><?=$origem->origem?></td>
    <td><?=$origem->nif?></td>
    <td><?=$origem->designacao?></td>
    <td>01</td>
    <td><?=$origem->tp_doc?></td>
    <td><?=$origem->num_doc?></td>
    <td><?=$origem->data?></td>
    <td><?=ceil($model['vl_fatura'])?></td>
    <td><?=ceil($model['vl_base_incid'])?></td>
    <td><?=$model['tx_iva']?></td>
    <td><?=ceil($model['iva_liq'])?></td>
    <td></td>
    <td><?=$model['linha_dest_mod']?></td>
  </tr>
<?php endforeach;?>
</tbody>
<tfoot></tfoot>
</table>