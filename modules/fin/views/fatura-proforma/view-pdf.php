<?php
$formatter = \Yii::$app->formatter;

use app\components\helpers\NumberHelper;


?>




<div class="col-md-6" style="width: 525px; float: left;"
    id="<?= $model->status ? ($model->send ? '' : 'rascunho') : 'anulado' ?>">

    <div id="invoice">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <h1>FATURA PROFORMA Nº <?= $model->numero . '/' . $model->bas_ano_id ?></h1>
            <div class="address">PROCESSO: <?= $model->processo->numero . '/' . $model->processo->bas_ano_id ?> NORD:
                <?= empty($model->processo->nord) ? '' : $model->processo->nord->id ?> </div>
            <div class="address">Data: <?= $formatter->asDate($model->data) ?></div>

        </div>
    </div>

    <div id="details" class="clearfix">
        <div id="client">
            <div class="name1"><?= $company['name'] ?></div>
            <div><?= $company['adress2'] ?></div>
            <div>NIF: <?= $company['nif'] ?></div>

        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Original</strong></div>
        <div id="invoice">
            <div class="">Exmos.(s) Sr.(s)</div>
            <h2 class="name"><?= $model->person->nome ?></h2>
            <div class="address"><?= $model->person->endereco ?></div>
            <div class="address">NIF <?= $model->person->nif ?></div>
            <div class="email"><a href="mailto:<?= $model->person->email ?>"><?= $model->person->email ?></a></div>
        </div>
    </div>

    <p id="mercadoria"><strong>Mercadoria:</strong> <?= $model->mercadoria ?></p>

    <table class="pdf">
        <thead>
            <tr>
                <th class="pdf desc" colspan="2">DESCRIÇÃO</th>
                <th class="pdf total">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0;
            foreach ($model->faturaProformaItem as $key => $modelItem) : ?>
            <tr>
                <td class="pdf desc" colspan="2">
                    <?= str_pad($modelItem->item->id, 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem->item->descricao ?>
                </td>
                <td class="pdf total"><?= $formatter->asCurrency($modelItem->valor) ?></td>
            </tr>
            <?php $i++;
            endforeach; ?>
            <?php for ($j = $i; $j < 18; $j++) : ?>
            <tr>
                <td class="pdf desc" colspan="2">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
            </tr>
            <?php endfor; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="pdf desc" colspan="2">TOTAL: <?= NumberHelper::ConvertToWords($model->valor) ?> escudos</td>
                <td class="pdf total"><?= $formatter->asCurrency($model->valor) ?></td>
            </tr>
        </tfoot>
    </table>
    <table>
        <tr>
            <td style="width: 300px; text-align: center;">
                <p>Recebi em: ....../....../......</p>
                <br><br>
                <p>..................................</p>
            </td>
            <td style="width: 300px; text-align: center;">
                <p>O Despachante</p>
                <br><br>
                <p><?= $model->send ? 'CONFERÊNCIA ELETRÓNICA' : '.................................' ?></p>
                <p><?= $formatter->asDate($model->data) ?></p>
            </td>
        </tr>
    </table>

</div>

































<div class="col-md-6" style="width: 525px; float: right;"
    id="<?= $model->status ? ($model->send ? '' : 'rascunho') : 'anulado' ?>">
    <div id="invoice">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <h1>FATURA PROFORMA Nº <?= $model->numero . '/' . $model->bas_ano_id ?></h1>
            <div class="address">PROCESSO: <?= $model->processo->numero . '/' . $model->processo->bas_ano_id ?> NORD:
                <?= empty($model->processo->nord) ? '' : $model->processo->nord->id ?> </div>
            <div class="address">Data: <?= $formatter->asDate($model->data) ?></div>

        </div>
    </div>

    <div id="details" class="clearfix">
        <div id="client">
            <div class="name1"><?= $company['name'] ?></div>
            <div><?= $company['adress2'] ?></div>
            <div>NIF: <?= $company['nif'] ?></div>

        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Tesouraria</strong>
        </div>
        <div id="invoice">
            <div class="">Exmos.(s) Sr.(s)</div>
            <h2 class="name"><?= $model->person->nome ?></h2>
            <div class="address"><?= $model->person->endereco ?></div>
            <div class="address">NIF <?= $model->person->nif ?></div>
            <div class="email"><a href="mailto:<?= $model->person->email ?>"><?= $model->person->email ?></a></div>
        </div>
    </div>

    <p id="mercadoria"><strong>Mercadoria:</strong> <?= $model->mercadoria ?></p>

    <table class="pdf">
        <thead>
            <tr>
                <th class="pdf desc" colspan="2">DESCRIÇÃO</th>
                <th class="pdf total">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0;
            foreach ($model->faturaProformaItem as $key => $modelItem) : ?>
            <tr>
                <td class="pdf desc" colspan="2">
                    <?= str_pad($modelItem->item->id, 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem->item->descricao ?>
                </td>
                <td class="pdf total"><?= $formatter->asCurrency($modelItem->valor) ?></td>
            </tr>
            <?php $i++;
            endforeach; ?>
            <?php for ($j = $i; $j < 18; $j++) : ?>
            <tr>
                <td class="pdf desc" colspan="2">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
            </tr>
            <?php endfor; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="pdf desc" colspan="2">TOTAL: <?= NumberHelper::ConvertToWords($model->valor) ?> escudos</td>
                <td class="pdf total"><?= $formatter->asCurrency($model->valor) ?></td>
            </tr>
        </tfoot>
    </table>
    <table>
        <tr>
            <td style="width: 300px; text-align: center; font-size: 12px;">
                <p>Recebi em: ....../....../......</p>
                <br><br>
                <p>..................................</p>
            </td>
            <td style="width: 300px; text-align: center;">
                <p>O Despachante</p>
                <br><br>
                <p><?= $model->send ? 'CONFERÊNCIA ELETRÓNICA' : '.................................' ?></p>
                <p><?= $formatter->asDate($model->data) ?></p>
            </td>
        </tr>
    </table>

</div>
