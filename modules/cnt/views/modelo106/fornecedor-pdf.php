<p class="text-center">ANO: <?=$model->ano?> MÊS: <?=$model->mes?></p>
<p class="text-center"><?=$titleReport?></p>



<table class="table table-striped">
  <thead>
<tr>
  <th>Origem</th>
  <th>NIF da Entidade</th>
  <th>Designação da Entidade</th>
  <th>Tipo Doc.</th>
  <th>Num. Doc.</th>
  <th>Data</th>
  <th>Valor Fatura</th>
  <th>Valor Base de Incidencia</th>
  <th>Taxa IVA</th>
  <th>IVA Suportado</th>
  <th>Direito ded.</th>
  <th>IVA Ded.</th>
  <th>Tipologia</th>
  <th>Valor destino</th>
</tr>
</thead>
<tbody>
<?php foreach($data as $model):?>
  <tr>
    <td><?=$model['origem']?></td>
    <td><?=$model['nif']?></td>
    <td><?=$model['designacao']?></td>
    <td><?=$model['tp_doc']?></td>
    <td><?=$model['num_doc']?></td>
    <td><?=$model['data']?></td>
    <td><?=$model['vl_fatura']?></td>
    <td><?=$model['vl_base_incid']?></td>
    <td><?=$model['tx_iva']?></td>
    <td><?=$model['iva_sup']?></td>
    <td><?=$model['direito_ded']?></td>
    <td><?=$model['iva_ded']?></td>
    <td><?=$model['tipologia']?></td>
    <td><?=$model['linha_dest_mod']?></td>
  </tr>
<?php endforeach;?>
</tbody>
<tfoot></tfoot>
</table>