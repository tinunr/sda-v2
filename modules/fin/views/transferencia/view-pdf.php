<?php
$formatter = \Yii::$app->formatter;
use app\components\helpers\NumberHelper;
use app\components\helpers\UserHelper;
$total_valor = 0;
$total_saldo = 0;
$nmRow= 18;
?>


<div class="col-md-6" style="width: 525px; float: left;" id="">

    <div id="invoice">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <h1>TRANSFERÊNCIA Nº <?= $model->numero?></h1>
            <div class="address">Data: <?= $formatter->asDate($model->data) ?></div>
        </div>
    </div>

    <div id="details" class="clearfix">
        <div id="client">
            <div class="name1"><?= $company['name'] ?></div>
            <div><?= $company['adress2'] ?></div>
            <div>NIF: <?= $company['nif'] ?></div>

        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Original</strong>
        </div>
        
    </div>

<br></br></br>
<br></br></br>
<br></br></br>
    <table class="pdf" style='overflow: hidden;'>
        <tbody>
            <tr>
                <td class="pdf desc">Nº Transferência</td>
                <td class="pdf desc"><?= $model->numero ?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Data de Transferência</td>
                <td class="pdf desc"><?= $formatter->asDate($model->data) ?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Conta de Origem</td>
                <td class="pdf desc"><?=$model->bancoContaOrigem->banco->sigla.' - '.$model->bancoContaOrigem->numero ?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Conta do Destino</td>
                <td class="pdf desc"><?=$model->bancoContaDestino->banco->sigla.' - '.$model->bancoContaDestino->numero?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Valor</td>
                <td class="pdf desc"><?=$formatter->asCurrency($model->valor)?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Descrição</td>
                <td class="pdf desc"><?=$model->descricao?></td>
            </tr>
        </tbody>
        
        </tfoot>
    </table>
    <br><br>
    <br><br>
    <br><br>
    
                <p style="text-align: center;">O Tesoureiro</p>
                <br>
                <p style="text-align: center;">.....................................................</p>
                <p style="text-align: center;"> / <?=UserHelper::getUserName($model->created_by)?> / </p>


</div>
































<div class="col-md-6" style="width: 525px; float: right;" id="">

    <div id="invoice">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <h1>TRANSFERÊNCIA Nº <?= $model->numero?></h1>
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
        
    </div>

<br></br></br>
<br></br></br>
<br></br></br>
    <table class="pdf" style='overflow: hidden;'>
        <tbody>
            <tr>
                <td class="pdf desc">Nº Transferência</td>
                <td class="pdf desc"><?= $model->numero ?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Data de Transferência</td>
                <td class="pdf desc"><?= $formatter->asDate($model->data) ?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Conta de Origem </td>
                <td class="pdf desc"><?=$model->bancoContaOrigem->banco->sigla.' - '.$model->bancoContaOrigem->numero ?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Conta de Destino</td>
                <td class="pdf desc"><?=$model->bancoContaDestino->banco->sigla.' - '.$model->bancoContaDestino->numero?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Valor</td>
                <td class="pdf desc"><?=$formatter->asCurrency($model->valor)?></td>
            </tr>
            <tr>
                <td class="pdf desc" >Descrição</td>
                <td class="pdf desc"><?=$model->descricao?></td>
            </tr>
        </tbody>
        
        </tfoot>
    </table>
    <br><br>
    <br><br>
    <br><br>
    
                <p style="text-align: center;">O Tesoureiro</p>
                <br>
                <p style="text-align: center;">.....................................................</p>
                <p style="text-align: center;"> / <?=UserHelper::getUserName($model->created_by)?> / </p>


</div>

