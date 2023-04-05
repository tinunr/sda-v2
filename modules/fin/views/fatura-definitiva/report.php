<?php

use app\modules\fin\models\FaturaProvisoriaItem;
use app\modules\dsp\models\Item;

$formatter = Yii::$app->formatter;

$total = 0;
$total_recebido = 0;
$total_saldo = 0;
$total_honorario = 0;


?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nº FP / FD Esp.</th>
            <th>Nº PR</th>
            <th>NORD</th>
            <th>Honorário</th>
            <th>Valor Total</th>
            <th>Valor Recebido</th>
            <th>Saldo</th>
            <th>Data</th>
            <th>Cliente</th>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($dataProvider->getModels() as $key => $model) : ?>
        <?php
            $total = $total + $model->valor;
            $total_recebido = $total_recebido + $model->receita->valor_recebido;
            $total_saldo = $total_saldo + $model->receita->saldo;
            if (!empty(FaturaProvisoriaItem::find()->where(['dsp_fatura_provisoria_id' => $model->id, 'dsp_item_id' => Item::HONORARIO])->one()->valor)) {
                $honorario = FaturaProvisoriaItem::find()->where(['dsp_fatura_provisoria_id' => $model->id, 'dsp_item_id' => Item::HONORARIO])->one()->valor;
            } else {
                $honorario = 0;
            }
            $total_honorario = $total_honorario + $honorario;
            ?>
        <tr>
            <td><?= $model->numero . '/' . $model->bas_ano_id ?></td>
            <td><?= $model->processo->numero . '/' . $model->processo->bas_ano_id ?></td>
            <td><?= empty($model->processo->nord->id) ? '' : $model->processo->nord->id . '/' . $model->processo->nord->bas_ano_id ?>
            </td>
            <td class="currency"><?= $formatter->asCurrency($honorario) ?></td>
            <td class="currency"><?= $formatter->asCurrency($model->valor) ?></td>
            <td class="currency"><?= $formatter->asCurrency($model->receita->valor_recebido) ?></td>
            <td class="currency"><?= $formatter->asCurrency($model->receita->saldo) ?></td>
            <td><?= $formatter->asDate($model->data) ?></td>
            <td><?= $model->person->nome ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th>TOTAL</th>
            <th></th>
            <th></th>
            <th class="currency"><?= $formatter->asCurrency($total_honorario) ?></th>
            <th class="currency"><?= $formatter->asCurrency($total) ?></th>
            <th class="currency"><?= $formatter->asCurrency($total_recebido) ?></th>
            <th class="currency"><?= $formatter->asCurrency($total_saldo) ?></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>

</table>
