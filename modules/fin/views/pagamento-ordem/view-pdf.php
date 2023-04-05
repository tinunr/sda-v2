<?php

use yii\helpers\Url;
use yii\widgets\ListView;
use yii\data\SqlDataProvider;
use yii\widgets\DetailView;
use kartik\mpdf\Pdf;


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

<div style="font-size: 12px">
<p>Exmos.(s) Sr.(s) <strong><?=$model->person->nome?></strong>  <?=$model->person->endereco?> <?=empty($model->person->nif)?'':'NIF '.$model->person->nif?></p>

<p>Recebi(emos) em <?=empty($model->documentoPagamento->descricao)?'':$model->documentoPagamento->descricao.' '.empty($model->numero_documento)?'':$model->numero_documento?>  - <?=empty($model->bancos->descricao)?'':$model->bancos->descricao?> <?=empty($model->bancoConta->numero)?'':$model->bancoConta->numero ?> pelos siguente(s) documento(s).</p>

 
  <table style="width: 100%; height: 5px; border-bottom: 1px solid #eee; font-size:12px">
   <thead >

    <tr >     
      <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; height:70%">Documento</td>
      <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;">Valor</td>
      <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;">Valor Pago</td>
      <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;">Saldo</td>
    </tr>
  </thead>
  <tbody>
    
 <?php $i=1; foreach ($model->item as $key => $item):?>

    <tr>      
      <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; height:70%"> <?=$item->despesa->numero.' - '.$item->despesa->descricao?></td>
      <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;"> <?= number_format($item->valor,2)?> </td>
      <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;"> <?= number_format($item->valor_pago,2)?> </td>
      <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;"> <?= number_format($item->saldo,2)?> </td>
    </tr>
   
   

    <?php $i++; endforeach;?>
<tr style="padding: 0px;">
        <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; height:70%">Total </td>
        <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;"></td>
        <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;"></td>
        <td style="width: 3px; padding: 5px; border-bottom: 1px solid #eee; text-align: right;"><?=number_format($model->valor)?></td>
    </tr>
  </tbody>
</table>


<br>
<br>
<div align="center";  float="left">
<?=convert_number_to_words($model->valor)?>
</div>
<br>
<p  align="center";  float="left">_________________________________________</p>
<p  align="center";  float="left">VºBº</p>





</div>