<p class="text-center">ANO: <?=$model->ano?> MÊS: <?=$model->mes?></p>
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
<?php foreach($data as $model):?>

  <tr>
    <td><?=$model['origem']?></td>
    <td><?=$model['nif']?></td>
    <td><?=$model['designacao']?></td>
    <td>01</td>
    <td><?=$model['tp_doc']?></td>
    <td><?=$model['num_doc']?></td>
    <td><?=$model['data']?></td>
    <td><?=($model['vl_fatura'])?></td>
    <td><?=($model['vl_base_incid'])?></td>
    <td><?=$model['tx_iva']?></td>
    <td><?=($model['iva_liq'])?></td>
    <td></td>
    <td><?=$model['linha_dest_mod']?></td>
  </tr>
<?php endforeach;?>
</tbody>
<tfoot></tfoot>
</table>