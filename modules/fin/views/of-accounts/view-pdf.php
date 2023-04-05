<?php
use yii\helpers\Url;
$formatter = \Yii::$app->formatter;
use app\modules\fin\models\Receita;


function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' e ';
    $separator   = ', ';
    $negative    = 'menos ';
    $decimal     = ' ponto ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'um',
        2                   => 'dois',
        3                   => 'três',
        4                   => 'quatro',
        5                   => 'cinco',
        6                   => 'seis',
        7                   => 'sete',
        8                   => 'oito',
        9                   => 'nove',
        10                  => 'dez',
        11                  => 'onze',
        12                  => 'doze',
        13                  => 'treze',
        14                  => 'quatorze',
        15                  => 'quinze',
        16                  => 'dezesseis',
        17                  => 'dezessete',
        18                  => 'dezoito',
        19                  => 'dezenove',
        20                  => 'vinte',
        30                  => 'trinta',
        40                  => 'quarenta',
        50                  => 'cinquenta',
        60                  => 'sessenta',
        70                  => 'setenta',
        80                  => 'oitenta',
        90                  => 'noventa',
        100                 => 'cento',
        200                 => 'duzentos',
        300                 => 'trezentos',
        400                 => 'quatrocentos',
        500                 => 'quinhentos',
        600                 => 'seiscentos',
        700                 => 'setecentos',
        800                 => 'oitocentos',
        900                 => 'novecentos',
        1000                => 'mil',
        1000000             => array('milhão', 'milhões'),
        1000000000          => array('bilhão', 'bilhões'),
        1000000000000       => array('trilhão', 'trilhões'),
        1000000000000000    => array('quatrilhão', 'quatrilhões'),
        1000000000000000000 => array('quinquilhão', 'quinquilhões')
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words só aceita números entre ' . PHP_INT_MAX . ' à ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $conjunction . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = floor($number / 100)*100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            if ($baseUnit == 1000) {
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[1000];
            } elseif ($numBaseUnits == 1) {
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit][0];
            } else {
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit][1];
            }
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

?>



<main style="width: 525px; float: left;" id="<?=($model->status)?'':'anulado'?>">
    <div id="header">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <div class="name1"><?=$company['name']?></div>
            <div><?=$company['adress2']?></div>
            <div>NIF: <?=$company['nif']?></div>
        </div>
    </div>
    <div id="details" class="clearfix">
        <div id="client">
            <h2 class="name"><?=$model->person->nome?></h2>
            <div class="address"><?=$model->person->endereco?></div>
            <div class="address">NIF <?=$model->person->nif?></div>
            <div class="email"><a href="mailto:<?=$model->person->email?>"><?=$model->person->email?></a></div>
        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Original</strong></div>
        <div id="invoice">
            <h1>ENCONTRO DE CONTA Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
            <div class="address">
                <?=(empty($model->fin_receita_id)?'DESPESA Nº '.$model->despesa->numero.'/'.$model->despesa->bas_ano_id:' FATURA PROVISÓRIA Nº '.$model->receita->faturaProvisoria->numero.'/'.$model->receita->faturaProvisoria->bas_ano_id)?>
            </div>
            <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
        </div>
    </div>

    <p><strong>Descrição:</strong> <br><?=$model->descricao?></p>

    <table class="pdf">
        <thead>
            <tr class="pdf">
                <th class="pdf desc">Descrição</th>
                <th class="pdf total">Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; foreach ($model->item as $key => $value) :?>
            <tr class="pdf">
                <td class="pdf desc">
                    <?php if (!empty($value->fin_despesa_id)):?>
                    <?=$value->despesa->numero.'/'.$value->despesa->bas_ano_id.' - '.$value->despesa->descricao?>
                    <?php elseif(!empty($value->fin_receita_id)):?>
                    <?=$value->receita->fin_receita_tipo_id == Receita::FATURA_PROVISORIA?$value->receita->faturaProvisoria->numero.'/'.$value->receita->faturaProvisoria->bas_ano_id.' - '.$value->receita->descricao:$value->receita->faturaDefinitiva->numero.'/'.$value->receita->faturaDefinitiva->bas_ano_id.' - '.$value->receita->descricao?>
                    <?php ?>
                    <?php elseif(!empty($value->fin_nota_debito_id)):?>
                    <?=$value->notaDebito->numero.'/'.$value->notaDebito->bas_ano_id.' - '.$value->notaDebito->descricao?>
                    <?php ?>
                    <?php endif;?>
                </td>
                <td class="pdf total"><?=$formatter->asCurrency($value->valor)?></td>
            </tr>
            <?php $i++; endforeach;?>
            <?php for ($j=$i; $j < 18; $j++):?>
            <tr>
                <td class="pdf desc">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
            </tr>
            <?php endfor;?>
        </tbody>
        <tfoot>
            <tr class="pdf">
                <td class="pdf desc">TOTAL: São <?=convert_number_to_words($model->valor)?> escudos</td>
                <td class="pdf total"><?=$formatter->asCurrency($model->valor)?></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <p style="text-align: center;">............................................................</p>
    <p style="text-align: center;">VºBº</p>



</main>















<main style="width: 525px; float: right;" id="<?=($model->status)?'':'anulado'?>">
    <div id="header">
        <div id="logo"><img src="logo.png"></div>
        <div id="company">
            <div class="name1"><?=$company['name']?></div>
            <div><?=$company['adress2']?></div>
            <div>NIF: <?=$company['nif']?></div>
        </div>
    </div>
    <div id="details" class="clearfix">
        <div id="client">
            <h2 class="name"><?=$model->person->nome?></h2>
            <div class="address"><?=$model->person->endereco?></div>
            <div class="address">NIF <?=$model->person->nif?></div>
            <div class="email"><a href="mailto:<?=$model->person->email?>"><?=$model->person->email?></a></div>
        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Contabilidade</strong>
        </div>
        <div id="invoice">
            <h1>ENCONTRO DE CONTA Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
            <div class="address">
                <?=(empty($model->fin_receita_id)?'DESPESA Nº '.$model->despesa->numero.'/'.$model->despesa->bas_ano_id:' FATURA PROVISÓRIA Nº '.$model->receita->faturaProvisoria->numero.'/'.$model->receita->faturaProvisoria->bas_ano_id)?>
            </div>
            <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
        </div>
    </div>

    <p><strong>Descrição:</strong> <br><?=$model->descricao?></p>

    <table class="pdf">
        <thead>
            <tr class="pdf">
                <th class="pdf desc">Descrição</th>
                <th class="pdf total">Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; foreach ($model->item as $key => $value) :?>
            <tr class="pdf">
                <td class="pdf desc">
                    <?php if (!empty($value->fin_despesa_id)):?>
                    <?=$value->despesa->numero.'/'.$value->despesa->bas_ano_id.' - '.$value->despesa->descricao?>
                    <?php elseif(!empty($value->fin_receita_id)):?>
                    <?=$value->receita->fin_receita_tipo_id == Receita::FATURA_PROVISORIA?$value->receita->faturaProvisoria->numero.'/'.$value->receita->faturaProvisoria->bas_ano_id.' - '.$value->receita->descricao:$value->receita->faturaDefinitiva->numero.'/'.$value->receita->faturaDefinitiva->bas_ano_id.' - '.$value->receita->descricao?>
                    <?php ?>
                    <?php elseif(!empty($value->fin_nota_debito_id)):?>
                    <?=$value->notaDebito->numero.'/'.$value->notaDebito->bas_ano_id.' - '.$value->notaDebito->descricao?>
                    <?php ?>
                    <?php endif;?>
                </td>
                <td class="pdf total"><?=$formatter->asCurrency($value->valor)?></td>
            </tr>
            <?php $i++; endforeach;?>
            <?php for ($j=$i; $j < 18; $j++):?>
            <tr>
                <td class="pdf desc">&nbsp;</td>
                <td class="pdf total">&nbsp;</td>
            </tr>
            <?php endfor;?>
        </tbody>
        <tfoot>
            <tr class="pdf">
                <td class="pdf desc">TOTAL: São <?=convert_number_to_words($model->valor)?> escudos</td>
                <td class="pdf total"><?=$formatter->asCurrency($model->valor)?></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <p style="text-align: center;">............................................................</p>
    <p style="text-align: center;">VºBº</p>



</main>
