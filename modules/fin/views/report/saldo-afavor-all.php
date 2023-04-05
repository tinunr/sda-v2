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
      <th>Nº Adiantamenro</th>
      <th>Data</th>
      <th>Valor Recebido</th>
      <th>Honrário</th>
      <th>Total Pago</th>
      <th>Saldo a Favor</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($dataProvider->getModels() as $key => $value):?>
    <tr>
      <td><?=$value['numero']?></td>
      <td><?=$value['numero']?></td>
      <td><?=$formatter->asCurrency($value['valor_recebido'])?></td>
      <td><?=$formatter->asCurrency($value['honorario'])?></td>
      <td><?=$formatter->asCurrency($value['total_pago'])?></td>
      <td><?=$formatter->asCurrency($value['valor_recebido']-$value['honorario']-$value['total_pago'])?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
   
 </table>

