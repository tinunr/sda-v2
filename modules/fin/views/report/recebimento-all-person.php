<?php

use yii\helpers\Html;
use yii\helpers\Url;
$formatter = Yii::$app->formatter;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Recebimento';
$total = 0;
?>
<?php  foreach ($person as $key => $personValue) :?>

<h3><?=$personValue['nome']?></h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Nº Recebimento</th>
            <th>Documento</th>
            <th>Valor</th>
            <th>Data</th>
            <th>Cliente</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // print_r($data);die();
            $data['ReportSearch']['dsp_person_id'] = $personValue['id'];
            $searchModel = new app\modules\fin\models\RecebimentoSearch($data['ReportSearch']);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        ?>
        <?php foreach ($dataProvider->getModels() as $key => $value) : ?>
            <?php 
            $total = $total + $value->valor;
            ?>
        <tr>
            <td><?=$value->numero.'/'.$value->bas_ano_id?></td>
            <td><?=$value->documentoPagamento->descricao?></td>
            <td><?=$value->valor?></td>
            <td><?=$value->data?></td>
            <td><?=$value->person->nome?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <th>TOTAL</th>
            <th></th>
            <th><?=$total?></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
<?php  endforeach ;?>


