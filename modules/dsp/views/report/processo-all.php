<?php
$formatter = Yii::$app->formatter;

use app\modules\dsp\models\ProcessoStatus;

$total = 0;
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>N? PR</th>
            <th>NORD</th>
            <th>Estado</th>
            <th>Cliente</th>
            <th>Mercadoria</th>
            <th>OBS</th>
            <th>PL</th>
            <th>Data Regul. PL</th>
            <th>Data Liq.</th>
            <th>Dias</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataProvider->getModels() as $key => $value) : ?>
        <?php
      $total = $total + $value->valor;
      ?>
        <tr>
            <td><?= $value->numero . '/' . $value->bas_ano_id ?></td>
            <td><?= empty($value->nord->id) ? '' : $value->nord->id ?></td>
            <td><?= $value->processoStatus->descricao ?></td>
            <td><?= $value->person->nome ?></td>
            <td><?= $value->descricao ?></td>
            <td><?= ($value->status == ProcessoStatus::PENDENTE) ? \app\modules\dsp\services\DspService::processoObsPendenstes($value->id) : '' ?>
            </td>
            <td><?= $value->n_levantamento ?></td>
            <td>
                <?php if (!empty($value->pedidoLevantamento->data_proragacao)) : ?>
                <?= $formatter->asDate($value->pedidoLevantamento->data_proragacao) ?>
                <?php elseif (!empty($value->pedidoLevantamento->data_regularizacao)) : ?>
                <?= $formatter->asDate($value->pedidoLevantamento->data_regularizacao) ?>
                <?php endif; ?>
            </td>
            <td><?= !empty($value->nord->despacho->data_liquidacao) ? $value->nord->despacho->data_liquidacao : '' ?>
            </td>
            <td> <?= $value->diasEscritorio() ?>

            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
