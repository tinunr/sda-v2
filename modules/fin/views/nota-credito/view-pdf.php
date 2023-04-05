<?php
use yii\helpers\Url;
$formatter = \Yii::$app->formatter;
$saldo = $model->despesa->valor;
$total_recebido = 0;
$data = Yii::$app->FinQuery->recebimetoDaFaturaDefinitiva($model->fin_fatura_defenitiva_id);


?>
  
   
  
    <main>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="">Exmos.(s) Sr.(s) Aviso de credito nº <?=$model->numero.'/'.$model->bas_ano_id?></div>
          <div class="name"><?=$model->person->nome?></div>
          <div class="address"><?=$model->person->nif?></div>
          <div class="address"><?=$model->person->email?></div>
        </div>
         <div id="invoice">
          <h1>AVISO DE CREDITO Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
          <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
        </div>
      </div>



      <!-- valores despachante -->
     
      <p><strong>Recebimento</strong></p>
      <table class="pdf">
          <tr>
            <th class="pdf desc">Fatura Provisória</th>
            <th class="pdf desc">Data Fatura Provisória</th>
            <th class="pdf total">Nº Recebibo</th>
            <th class="pdf total">Data Recibo</th>
            <th class="pdf total">Valor Recebido</th>
          </tr>
        <tbody>
        <?php $i= 2;foreach ($data as $key => $value):?>
        <?php 
        // print_r($value);die();
        $total_recebido = $total_recebido + $value['valor_recebido'];
        ?>
          <tr>
            <td class="pdf desc"><?=$value['fatura_provisorias']?></td>
            <td class="pdf desc"><?=$formatter->asDate($value['data_fatura'])?></td> 
            <td class="pdf total"><?=$value['recibos']?></td> 
            <td class="pdf total"><?=$formatter->asDate($value['data_recebimento'])?></td> 
            <td class="pdf total"><?=$formatter->asCurrency($value['valor_recebido'])?></td>
          </tr>
        <?php $i++; endforeach ;?>
        </tbody>
         <tfoot>
          <tr>
            <td class="pdf no" colspan="4"><stront>Total Recebido</stront></td>
            <td class="pdf total"><?=$formatter->asCurrency($total_recebido)?></td>
          </tr>
        </tfoot>
      </table>





      <!-- diferença de valores -->
    </br>
    <?php if(!empty($model->faturaDefinitiva)):?>
      <p><strong>Fatura Definitiva</strong></p>

      <table class="pdf">
          <tr>
            <th class="pdf desc">Fatura Definitiva</th>
            <th class="pdf desc">Data Fatura Definitiva</th>
            <th class="pdf total">Valor Fatura Definitiva</th>
          </tr>
        <tbody>
        <tr>
            <td class="pdf desc"><?=$model->faturaDefinitiva->numero.'/'.$model->faturaDefinitiva->bas_ano_id?></td>
            <td class="pdf desc"><?=$formatter->asDate($model->faturaDefinitiva->data)?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->faturaDefinitiva->valor)?></td>
          </tr>
        </tbody>
         
      </table>

<?php endif;?>
       <!-- diferença de valores -->
    </br>
      <p><strong>Aviso de credito</strong></p>

      <table class="pdf">
          <tr>
            <th class="pdf desc">Total Valor Recebido</th>
            <th class="pdf date">Valor Fatura Definitiva</th>
            <th class="pdf total">Saldo a V/Favor</th>
          </tr>
        <tbody>
        <tr>
            <td class="pdf date"><?=$formatter->asCurrency($total_recebido)?></td>
            <td class="pdf total"><?=!empty($model->faturaDefinitiva->valor)??$formatter->asCurrency($model->faturaDefinitiva->valor) ?></td>
            <td class="pdf total"><?=$formatter->asCurrency(abs($model->valor - $total_recebido))?></td>
          </tr>
        </tbody>
        <tfoot>
            <tr>
            <td class="pdf date" colspan="2"><strong>Valor de Aviso de Credito</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency(abs($model->valor))?></strong></td>
          </tr>
        </tfoot>
      </table>


      


<br>
<br>
<br>
<br>
<hr>


      <!-- despesas geradas -->
      <p><strong>Despesa Gerada</strong></p>

      <table class="pdf">
          <tr>
            <th class="pdf desc">Documento</th>
            <th class="pdf desc">Numero</th>
            <th class="pdf date">Data</th>
            <th class="pdf total">Valor</th>
            <th class="pdf total">Saldo</th>
          </tr>
        <tbody>
        <tr>
            <td class="pdf desc"> Despesa</td>
            <td class="pdf desc"><?=$model->despesa->numero.'/'.$model->despesa->bas_ano_id ?></td>
            <td class="pdf date"><?=$formatter->asDate($model->despesa->data )?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->despesa->valor) ?></td>
            <td class="pdf total"><?=$formatter->asCurrency($saldo)?></td>
          </tr>
          <?php for ($j=$i; $j <20; $j++):?>
            <tr class="pdf">
            <td  class="desc pdf">&nbsp; </td>
            <td  class="desc pdf">&nbsp;</td>
            <td  class="qty pdf">&nbsp;</td>
            <td  class="desc pdf">&nbsp;</td>
            <td  class="desc pdf">&nbsp;</td>
        </tr>
          <?php endfor ;?>

        </tbody>
         <tfoot>
          <tr>
            <td class="pdf no">Saldo</td>
            <td class="pdf total">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
            <td class="pdf total"><?=$formatter->asCurrency($saldo)?></td>
          </tr>
        </tfoot>
      </table>
     
    </main>




<?php 

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
