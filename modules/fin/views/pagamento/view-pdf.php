<?php

$formatter = \Yii::$app->formatter;

use app\components\helpers\NumberHelper;
$total_valor = 0;
$total_saldo = 0;
$nmRow= 18;
?>

<div class="col-md-6" style="width: 525px; float: left;" id="">

    <div id="invoice">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <h1>PAGAMENTO Nº <?= $model->numero . '/' . $model->bas_ano_id ?></h1>
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

    <p>Pagamo(s) em
        <?= empty($model->fin_documento_pagamento_id) ? '' : $model->documentoPagamento->descricao ?>
        <?= empty($model->numero_documento) ? '' :  ' de numero '.$model->numero_documento ?>
        <?= empty($model->fin_banco_id) ? '' : ' do banco '.$model->bancos->descricao ?>
        <?= empty($model->bancoConta->numero) ? '' : ' atravez da conta '.$model->bancoConta->numero ?> pelos
        siguente(s) documento(s).</p>

    <table class="pdf" style='overflow: hidden;'>
        <thead>
            <tr>
                <th class="pdf desc" colspan="2">DOCUMENTO</th>
                <th class="pdf total">VALOR</th>
                <th class="pdf total">VALOR PAGO</th>
                <th class="pdf total">SALDO</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0;
            foreach ($model->item as $key => $item) : ?>
            <?php 
                $total_saldo = $item->saldo +$total_saldo;
                $total_valor = $item->valor + $total_valor;
    
                 
            ?>
            <tr>
                <td class="pdf desc" colspan="2">
                    <?= $item->despesa->numero . ' - ' . $item->despesa->descricao ?>
                </td>
                <td class="pdf total"><?= $formatter->asCurrency($item->valor) ?></td>
                <td class="pdf total"><?= $formatter->asCurrency($item->valor_pago) ?></td>
                <td class="pdf total"><?= $formatter->asCurrency($item->saldo) ?></td>
            </tr>
            <?php $i++;
            endforeach; ?>
            <?php for ($j = $i; $j < $nmRow; $j++) : ?>
            <tr>
                <td class="pdf desc" colspan="2">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
            </tr>
            <?php endfor; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="pdf desc" colspan="2">TOTAL: <?= NumberHelper::ConvertToWords($model->valor) ?> escudos</td>
                <td class="pdf total"><?= $formatter->asCurrency($total_valor) ?></td>
                <td class="pdf total"><?= $formatter->asCurrency($model->valor) ?></td>
                <td class="pdf total"><?= $formatter->asCurrency($total_saldo) ?></td>
            </tr>
        </tfoot>
    </table>
    <br><br>
    <table>
        <tr>
            <td style="width: 300px; text-align: center;">
                <p>O Tesoureiro</p>
                <br><br>
                <p>.....................................................</p>
                <p><?=!empty($model->user->name)?' / '.$model->user->name.' / ':''?>
                </p>

            </td>
            <td style="width: 300px; text-align: center;">
                <p>Aoutorizado Por</p>
                <br><br>
                <p>.....................................................</p>
                <p><?=!empty($model->pagamentoOrdem->personValidacao->name)?' / '.$model->pagamentoOrdem->personValidacao->name.' / ':''?>
                </p>

            </td>
        </tr>
    </table>

</div>
































<div class="col-md-6" style="width: 525px; float: right;" id="">

    <div id="invoice">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <h1>PAGAMENTO Nº <?= $model->numero . '/' . $model->bas_ano_id ?></h1>
            <div class="address">Data: <?= $formatter->asDate($model->data) ?></div>
        </div>
    </div>

    <div id="details" class="clearfix">
        <div id="client">
            <div class="name1"><?= $company['name'] ?></div>
            <div><?= $company['adress2'] ?></div>
            <div>NIF: <?= $company['nif'] ?></div>

        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Contabilidade</strong>
        </div>
        <div id="invoice">
            <div class="">Exmos.(s) Sr.(s)</div>
            <h2 class="name"><?= $model->person->nome ?></h2>
            <div class="address"><?= $model->person->endereco ?></div>
            <div class="address">NIF <?= $model->person->nif ?></div>
            <div class="email"><a href="mailto:<?= $model->person->email ?>"><?= $model->person->email ?></a></div>
        </div>
    </div>

    <p>Pagamo(s) em
        <?= empty($model->fin_documento_pagamento_id) ? '' : $model->documentoPagamento->descricao ?>
        <?= empty($model->numero_documento) ? '' :  ' de numero '.$model->numero_documento ?>
        <?= empty($model->fin_banco_id) ? '' : ' do banco '.$model->bancos->descricao ?>
        <?= empty($model->bancoConta->numero) ? '' : ' atravez da conta '.$model->bancoConta->numero ?> pelos
        siguente(s) documento(s).</p>

    <table class="pdf" style='overflow: hidden;'>
        <thead>
            <tr>
                <th class="pdf desc" colspan="2">DOCUMENTO</th>
                <th class="pdf total">VALOR</th>
                <th class="pdf total">VALOR PAGO</th>
                <th class="pdf total">SALDO</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0;
            foreach ($model->item as $key => $item) : ?>
            <tr>
                <td class="pdf desc" colspan="2">
                    <?= $item->despesa->numero . ' - ' . $item->despesa->descricao ?>
                </td>
                <td class="pdf total"><?= $formatter->asCurrency($item->valor) ?></td>
                <td class="pdf total"><?= $formatter->asCurrency($item->valor_pago) ?></td>
                <td class="pdf total"><?= $formatter->asCurrency($item->saldo) ?></td>
            </tr>
            <?php $i++;
            endforeach; ?>
            <?php for ($j = $i; $j < $nmRow; $j++) : ?>
            <tr>
                <td class="pdf desc" colspan="2">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
            </tr>
            <?php endfor; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="pdf desc" colspan="2">TOTAL: <?= NumberHelper::ConvertToWords($model->valor) ?> escudos</td>
                <td class="pdf total"><?= $formatter->asCurrency($total_valor) ?></td>
                <td class="pdf total"><?= $formatter->asCurrency($model->valor) ?></td>
                <td class="pdf total"><?= $formatter->asCurrency($total_saldo) ?></td>
            </tr>
        </tfoot>
    </table>
    <br><br>
    <table>
        <tr>
            <td style="width: 300px; text-align: center;">
                <p>O Tesoureiro</p>
                <br><br>
                <p>.....................................................</p>
                <p><?=!empty($model->user->name)?' / '.$model->user->name.' / ':''?>
                </p>

            </td>
            <td style="width: 300px; text-align: center;">
                <p>Autorizado Por</p>
                <br><br>
                <p>.....................................................</p>
                <p><?=!empty($model->pagamentoOrdem->personValidacao->name)?' / '.$model->pagamentoOrdem->personValidacao->name.' / ':''?>
                </p>

            </td>
        </tr>
    </table>

</div>
