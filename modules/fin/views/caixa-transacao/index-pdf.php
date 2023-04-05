<?php 
$formatter = Yii::$app->formatter;

$valor_entrada = 0;
$valor_saida = 0;
$saldo = 0;
?>

<table class="table table-striped">
  <thead>
    <tr>
      <th>CONTA</th>
      <th>DATA</th>
      <th>ENTRADA</th>
      <th>SAIDA</th>
      <th>SALDO</th>
      <th>Nº DOC.</th>
      <th>DATA DOC.</th>
      <th>DESCRICAO.</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($dataProvider->getModels() as $key => $model): ?>
    <?php 
    $valor_entrada = $valor_entrada +$model->valor_entrada;
    $valor_saida = $valor_saida + $model->valor_saida;
    $saldo = $saldo + $model->saldo;

    ?>
    <tr>
      <td><?= $model->caixa->bancoConta->banco->sigla.' - '.$model->caixa->bancoConta->numero?></td>
      <td><?=$formatter->asDate($model->data)?></td>
      <td><?=$formatter->asCurrency($model->valor_entrada)?></td>
      <td><?=$formatter->asCurrency($model->valor_saida)?></td>
      <td><?=$formatter->asCurrency($model->saldo)?></td>
      <td><?=$model->numero_documento?></td>
      <td><?=$formatter->asDate($model->data_documento)?></td>
      <td><?=$model->descricao?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
  <tr>
      <th>TOTAL</th>
      <th></th>
      <th><?=$formatter->asCurrency($valor_entrada)?></th>
      <th><?=$formatter->asCurrency($valor_saida)?></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
   
 </table>
  