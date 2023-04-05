<?php 
use app\modules\fin\models\RecebimentoTipo;
use app\components\helpers\NumberHelper;
use app\components\helpers\UserHelper;
$formatter = Yii::$app->formatter;

?>

<main class="col-md-6" style="width: 525px; float: left;" id="<?=($model->status)?'':'anulado'?>">
    <div id="invoice">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <h1>RECIBO Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
            <div class="address">Tipo: <?=$model->recebimentoTipo->descricao?></div>
            <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
        </div>
    </div>

    <div id="details" class="clearfix">
        <div id="client">
            <div class="name1"><?=$company['name']?></div>
            <div><?=$company['adress2']?></div>
            <div>NIF: <?=$company['nif']?></div>

        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>original</strong></div>
        <div id="invoice">
            <div class="">Exmos.(s) Sr.(s)</div>
            <h2 class="name"><?=$model->person->nome?></h2>
            <div class="address"><?=$model->person->endereco?></div>
            <div class="address">NIF <?=$model->person->nif?></div>
            <div class="email"><a href="mailto:<?=$model->person->email?>"><?=$model->person->email?></a></div>
        </div>
    </div>

    <p>Recebemos em <?=$model->documentoPagamento->descricao?>
        <?=($model->fin_documento_pagamento_id==1)?'':' nº '.$model->numero_documento?>
        <?=($model->fin_documento_pagamento_id==1)?'':' - '.$model->bancos->sigla?> pelos seguinte(s) documento(s).</p>
    <?php if ($model->fin_recebimento_tipo_id != RecebimentoTipo::ADIANTAMENTO) :?>
    <table class="pdf">
        <thead>
            <tr class="pdf">
                <th class="pdf desc">Documento</th>
                <th class="pdf data">Data Doc.</th>
                <th class="pdf unit">Valor Doc.</th>
                <th class="pdf qty">Valor Recebido</th>
                <th class="pdf total">Saldo Doc.</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $valor = 0;
            $valor_recebido = 0;
            $saldo = 0;
        ?>
            <?php $i=1; foreach ($model->recebimentoItem as $key => $item) :?>
            <?php 
            
            $valor =$valor+ $item->valor;
            $valor_recebido =$valor_recebido+ $item->valor_recebido;
            $saldo = $saldo+$item->saldo;
        ?>
            <tr class="pdf">
                <td class="pdf desc"><?=$item->descricao_item?></td>
                <td class="pdf data">
                    <?=!empty($item->receita->faturaProvisoria->data)?$formatter->asDate($item->receita->faturaProvisoria->data):null?>
                </td>
                <td class="pdf qty"><?=$formatter->asCurrency($item->valor)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency($item->valor_recebido)?></td>
                <td class="pdf total"><?=$formatter->asCurrency($item->saldo)?></td>
            </tr>
            <?php $i++; endforeach ;?>
            <?php for ($j=$i; $j< 18; $j++):?>
            <tr class="pdf">
                <td class="desc pdf">&nbsp; </td>
                <td class="desc pdf">&nbsp;</td>
                <td class="qty pdf">&nbsp;</td>
                <td class="desc pdf">&nbsp;</td>
                <td class="desc pdf">&nbsp;</td>
            </tr>
            <?php endfor;?>
        </tbody>
        <tfoot>
            <tr>
                <td class="pdf no">TOTAL</td>
                <td class="pdf total"></td>
                <td class="pdf total"><?=$formatter->asCurrency($valor)?></td>
                <td class="pdf total"><?=$formatter->asCurrency($valor_recebido)?></td>
                <td class="pdf total"><?=$formatter->asCurrency($saldo)?></td>
            </tr>
        </tfoot>
    </table>


    <?php endif?>




    <?php if ($model->fin_recebimento_tipo_id == RecebimentoTipo::ADIANTAMENTO) :?>


    <table class="pdf">
        <thead>
            <tr class="pdf">
                <th class="desc pdf">Documento </th>
                <th class="data pdf">Data</th>
                <th class="qty pdf">Valor Doc.</th>
                <th class="qty pdf">Valor Recebido</th>
                <th class="qty pdf">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php 
    $total_pago = 0;
    ?>
            <?php $i = 1;foreach ($model->despesa as $key => $despesa):  ?>
            <?php 
    $total_valor = 0;
    $total_saldo = 0;
    $total_pago = $despesa->valor;
    ?>
            <tr class="pdf">
                <td class="pdf desc">Adiantamento Nº<?=$despesa->numero.'/'.$despesa->bas_ano_id?></td>
                <td class="pdf desc"><?=$formatter->asDate($despesa->data)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency(0)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency($model->valor)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency(0)?></td>
            </tr>





            <?php $i++; endforeach;?>

            <?php for ($j=$i; $j< 18; $j++):?>
            <tr class="pdf">
                <td class="desc pdf">&nbsp; </td>
                <td class="desc pdf">&nbsp;</td>
                <td class="qty pdf">&nbsp;</td>
                <td class="desc pdf">&nbsp;</td>
                <td class="desc pdf">&nbsp;</td>
            </tr>
            <?php endfor;?>
        </tbody>
        <tfoot class="pdf">
            <tr class="pdf">
                <td class="pdf no">TOTAL</td>
                <td class="pdf desc"></td>
                <td class="pdf qty"><?=$formatter->asCurrency($total_valor)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency($total_pago)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency($total_saldo)?></td>

            </tr>
        </tfoot>
    </table>



    <?php endif;?>
    <div id="notices">
        <div>TOTAL RECEBIDO: <?=$formatter->asCurrency($model->valor)?></div>
        <div class="notice"><?=NumberHelper::ConvertToWords($model->valor)?> escudos</div>
    </div>
    <br>
    <p style="text-align: center;">O Tesoureiro</p>
        <br>
        <p style="text-align: center;">.....................................................</p>
        <p style="text-align: center;">/ <?=UserHelper::getUserName($model->created_by)?> /</p>

</main>






























<main class="col-md-6" style="width: 525px; float: right;" id="<?=($model->status)?'':'anulado'?>">
    <div id="invoice">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <h1>RECIBO Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
            <div class="address">Tipo: <?=$model->recebimentoTipo->descricao?></div>
            <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
        </div>
    </div>

    <div id="details" class="clearfix">
        <div id="client">
            <div class="name1"><?=$company['name']?></div>
            <div><?=$company['adress2']?></div>
            <div>NIF: <?=$company['nif']?></div>

        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Contabilidade</strong>
        </div>
        <div id="invoice">
            <div class="">Exmos.(s) Sr.(s)</div>
            <h2 class="name"><?=$model->person->nome?></h2>
            <div class="address"><?=$model->person->endereco?></div>
            <div class="address">NIF <?=$model->person->nif?></div>
            <div class="email"><a href="mailto:<?=$model->person->email?>"><?=$model->person->email?></a></div>
        </div>
    </div>

    <p>Recebemos em <?=$model->documentoPagamento->descricao?>
        <?=($model->fin_documento_pagamento_id==1)?'':' nº '.$model->numero_documento?>
        <?=($model->fin_documento_pagamento_id==1)?'':' - '.$model->bancos->sigla?> pelos seguinte(s) documento(s).</p>
    <?php if ($model->fin_recebimento_tipo_id != RecebimentoTipo::ADIANTAMENTO) :?>
    <table class="pdf">
        <thead>
            <tr class="pdf">
                <th class="pdf desc">Documento</th>
                <th class="pdf data">Data Doc.</th>
                <th class="pdf unit">Valor Doc.</th>
                <th class="pdf qty">Valor Recebido</th>
                <th class="pdf total">Saldo Doc.</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $valor = 0;
            $valor_recebido = 0;
            $saldo = 0;
        ?>
            <?php $i=1; foreach ($model->recebimentoItem as $key => $item) :?>
            <?php 
            
            $valor =$valor+ $item->valor;
            $valor_recebido =$valor_recebido+ $item->valor_recebido;
            $saldo = $saldo+$item->saldo;
        ?>
            <tr class="pdf">
                <td class="pdf desc"><?=$item->descricao_item?></td>
                <td class="pdf data">
                    <?=!empty($item->receita->faturaProvisoria->data)?$formatter->asDate($item->receita->faturaProvisoria->data):null?>
                </td>
                <td class="pdf qty"><?=$formatter->asCurrency($item->valor)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency($item->valor_recebido)?></td>
                <td class="pdf total"><?=$formatter->asCurrency($item->saldo)?></td>
            </tr>
            <?php $i++; endforeach ;?>
            <?php for ($j=$i; $j< 18; $j++):?>
            <tr class="pdf">
                <td class="desc pdf">&nbsp; </td>
                <td class="desc pdf">&nbsp;</td>
                <td class="qty pdf">&nbsp;</td>
                <td class="desc pdf">&nbsp;</td>
                <td class="desc pdf">&nbsp;</td>
            </tr>
            <?php endfor;?>
        </tbody>
        <tfoot>
            <tr>
                <td class="pdf no">TOTAL</td>
                <td class="pdf total"></td>
                <td class="pdf total"><?=$formatter->asCurrency($valor)?></td>
                <td class="pdf total"><?=$formatter->asCurrency($valor_recebido)?></td>
                <td class="pdf total"><?=$formatter->asCurrency($saldo)?></td>
            </tr>
        </tfoot>
    </table>


    <?php endif?>




    <?php if ($model->fin_recebimento_tipo_id == RecebimentoTipo::ADIANTAMENTO) :?>


    <table class="pdf">
        <thead>
            <tr class="pdf">
                <th class="desc pdf">Documento </th>
                <th class="data pdf">Data</th>
                <th class="qty pdf">Valor Doc.</th>
                <th class="qty pdf">Valor Recebido</th>
                <th class="qty pdf">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php 
    $total_pago = 0;
    ?>
            <?php $i = 1;foreach ($model->despesa as $key => $despesa):  ?>
            <?php 
    $total_valor = 0;
    $total_saldo = 0;
    $total_pago = $despesa->valor;
    ?>
            <tr class="pdf">
                <td class="pdf desc">Adiantamento Nº<?=$despesa->numero.'/'.$despesa->bas_ano_id?></td>
                <td class="pdf desc"><?=$formatter->asDate($despesa->data)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency(0)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency($model->valor)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency(0)?></td>
            </tr>





            <?php $i++; endforeach;?>

            <?php for ($j=$i; $j< 18; $j++):?>
            <tr class="pdf">
                <td class="desc pdf">&nbsp; </td>
                <td class="desc pdf">&nbsp;</td>
                <td class="qty pdf">&nbsp;</td>
                <td class="desc pdf">&nbsp;</td>
                <td class="desc pdf">&nbsp;</td>
            </tr>
            <?php endfor;?>
        </tbody>
        <tfoot class="pdf">
            <tr class="pdf">
                <td class="pdf no">TOTAL</td>
                <td class="pdf desc"></td>
                <td class="pdf qty"><?=$formatter->asCurrency($total_valor)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency($total_pago)?></td>
                <td class="pdf qty"><?=$formatter->asCurrency($total_saldo)?></td>

            </tr>
        </tfoot>
    </table>



    <?php endif;?>
    <div id="notices">
        <div>TOTAL RECEBIDO: <?=$formatter->asCurrency($model->valor)?></div>
        <div class="notice"><?=NumberHelper::ConvertToWords($model->valor)?> escudos</div>
    </div>
    <br>
    <p style="text-align: center;">O Tesoureiro</p>
        <br>
        <p style="text-align: center;">.....................................................</p>
        <p style="text-align: center;">/ <?=UserHelper::getUserName($model->created_by)?> /</p>

</main>
