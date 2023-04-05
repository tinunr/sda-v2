<?php 
use yii\helpers\Html;
use yii\helpers\Url;
$this->title="Lançamento Erado";
?>
<section>
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Numero</th>
            <th>Diário</th>
            <th>Ano</th>
            <th>Mês</th>
            <th>Debito</th>
            <th>Credito</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $key => $value):  ?>
        <tr>
            <td><?=Html::a('Abir', ['/cnt/razao/view','id'=>$value['id']], ['class' => 'btn btn-xs'])?></td>
            <td><?=$value['numero']?></td>
            <td><?=$value['cnt_diario_id']?></td>
            <td><?=$value['bas_ano_id']?></td>
            <td><?=$value['bas_mes_id']?></td>
            <td><?=$value['debito']?></td>
            <td><?=$value['credito']?></td>
        </tr>
    <?php endforeach ;?>

    </tbody>
</table>
</section>