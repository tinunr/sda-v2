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



<div class="content" style="width: 800px; margin: 0 auto;">

<p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 90%;"><strong><i class="fa fa-graduation-cap"></i>  <?= $company['name']?></strong>
    </p>
    <p style="margin: 0px 0px; text-align: center; padding: 2px; font-size: 60%;"><strong><i class="fa fa-graduation-cap"></i><?= $company['adress1']?></strong>
    </p>
    <p style="margin: 0px 0px; text-align: center; padding: 2px;font-size: 60%;"><strong><i class="fa fa-graduation-cap"></i> <?= $company['adress2']?></strong>
    <br><strong><i class="fa fa-graduation-cap"></i> C.P. Nº <?= $company['cp']?> - Tel.: <?= $company['teletone']?> - FAX: <?= $company['fax']?> - Praia, Santiago</strong>
    </p>
    <p style="margin: 0px 0px; text-align: center; padding: 5px;font-size: 50%;"><strong><i class="fa fa-graduation-cap"></i> NIF: <?= $company['nif']?></strong>
    </p>
    <p style="margin: 0px 0px; text-align: center; padding:0px;font-size: 80%;"><strong><i class="fa fa-graduation-cap"></i> FACTURA PROVISÓRIA</strong>
    </p>

    </p>

            <p style=" margin-top: 5px; text-align: right; padding: 7px; height:6px"><i class="fa fa-graduation-cap">Nº <?= $model->numero ?></i><strong></strong>
            </p>



<p></p><p></p><p></p>


<p style="text-align: left; padding: 7px; height:6px"><i class="fa fa-graduation-cap"></i> Ilmo. Sr <?= $model->person->nome ?>  Mercadoria:  <font style="text-decoration: underline; text-decoration-style: dashed"> <?= $model->mercadoria ?> </font>
</p>
 <section>
 
  <table class="table table-hover">
  
  <tbody>
    
    <?php $i=1; foreach ($modelsFaturaProvisoriaItem as $key => $item):?>

    <tr>      
      <td style="whidgt: 10%; text-align: left; padding: 3px;vertical-align: top; border-top: 0px"></td>
      <td> <?=$item->item->descricao?></td>
      <td style="whidgt: 10%; "></td>

      <td style="text-align: right;padding: 3px;vertical-align: top; border-top: 0px"> <?= number_format($item->valor,2)?> </td>
    </tr>
   
    

   

  

    <?php $i++; endforeach;?>

  </tbody>
</table>


<p style="text-align: right; padding: 7px; height:6px"><i class="fa fa-graduation-cap"></i>Total ................................................................................ <?= number_format($model->valor,2)?>$</p>

</section>
<p><strong>São</strong> <font style="text-decoration: underline; text-decoration-style: dashed"><?= convert_number_to_words($model->valor)?> escudos</font></p>
<p></p><p></p>

<p></p>
<p style="text-align: center; text-decoration: underline; text-decoration-style: dashed">Praia, <?php echo date("d/m/y")?> </p>

<div>
<div align="center";  float="left">




</div>
</div>

<table class="table table-hover">
  
  <tbody>
   
    <tr>      
      <td style="text-align: left; padding: 3px;vertical-align: top; border-top: 0px">
        <p  align="Left";>Recebido Em ....../....../....... </p>
        <br> </br>
        <p  align="Left";>--------------------------------------</p>
       
      </td>
      <td style="text-align: center;padding: 3px;vertical-align: top; border-top: 0px">
        <p  align="right";>O Despanchante, </p>
        <br> </br>
        <p  align="right";>-------------------------------</p>
       
      </td>
    </tr>
  
    

  </tbody>
</table>
</div>


