<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\ButtonGroup;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$formatter = Yii::$app->formatter;
$this->title = 'Recebimento';
$total = 0;
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nº FP</th>
            <th>Nº REC</th>
            <th>Mercadoria</th>
            <th>Data de Fatura</th>
            <th>Valor Recebido</th>
            <th>Cliente</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataProvider->getModels() as $key => $value) : ?>
            <?php 
            $total = $total + $value['valor_recebido'];
            ?>
        <tr>
            <td><?=$value['numero_fp']?></td>
            <td><?=$value['numero']?></td>
            <td><?=$value['mercadoria']?></td>
            <td><?=$formatter->asDate($value['data'])?></td>
            <td><?=$formatter->asCurrency($value['valor_recebido'])?></td>
            <td><?=$value['cliente']?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <th>TOTAL</th>
            <th></th>
            <th></th>
            <th></th>
            <th><?=$formatter->asCurrency($total)?></th>
            <th></th>
        </tr>
    </tfoot>
</table>


