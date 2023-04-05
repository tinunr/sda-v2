<?php 
use app\models\Mes;
$this->title = 'Contabilidade Dashboard';
$formatter = Yii::$app->formatter;
// print_r($dataTaxaRAI);die();
// print_r(Yii::$app->CntQuery->getBalanceteConta(2019,05,7));die();
?>

<section>
<div class="row">

<div class="col-md-6">
<div class="titulo-principal"> <h5 class="titulo">Previsão de Imposto / Taxa RAI</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Ano</th>
            <th>Mês</th>
            <th>Conta 6</th>
            <th>Conta 7</th>
            <th>Tx Imposto</th>
            <th>RAI</th>
            <th>Imposto</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($dataTaxaRAI as $key =>$value) :?>
        <tr>
            <td><?=$key?></td>
            <td><?=$value['ano']?></td>
            <td><?=Mes::findOne($value['mes'])->descricao?></td>
            <td><?=$formatter->asCurrency($value['conta6'])?></td>
            <td><?=$formatter->asCurrency($value['conta7'])?></td>
            <td><?=$value['juru']?></td>
            <td><?=$formatter->asCurrency($value['rai'])?></td>
            <td><?=$formatter->asCurrency($value['imposto'])?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

</div>
</section>
