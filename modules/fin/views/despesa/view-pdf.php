<?php 
$formatter = \Yii::$app->formatter;
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
    
    <main>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">Para:</div>
          <h2 class="name"><?=$model->person->nome?></h2>
          <div class="address"><?=$model->person->endereco?></div>
          <div class="address">NIF <?=$model->person->nif?></div>
          <div class="email"><a href="mailto:<?=$model->person->email?>"><?=$model->person->email?></a></div>
        </div>
        <div id="invoice">
          <h1>DESPESA  # <?=$model->numero?></h1>
          <div class="date">Data: <?=$formatter->asDate($model->data)?></div>
        </div>
      </div>

      <p><?=$model->descricao?></p>
      
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="no">#</th>
            <th class="desc">DETALHES</th>
            <th class="total">VALOR</th>
          </tr>
        </thead>
        <tbody>
        <?php 
            $valor = 0;
        ?>
        <?php $i=1; foreach ($model->despesaItem as $key => $item) :?>
        <?php 
            
            $valor =$valor+ $item->valor;
        ?>
          <tr>
            <td class="no"><?=$i?></td>
            <td class="desc"><?=$item->item->descricao?></td>
            <td class="qty"><?=$formatter->asCurrency($item->valor)?></td>
          </tr>
        <?php $i++; endforeach ;?>
        </tbody>
        <tfoot>
          <tr>
            <td ></td>
            <td class="no">TOTAL</td>
            <td ><?=$formatter->asCurrency($valor)?></td>
          </tr>
           <tr>
            <td ></td>
            <td class="no">TOTAL PAGO</td>
            <td ><?=$formatter->asCurrency($model->valor_pago)?></td>
          </tr>
           <tr>
            <td ></td>
            <td class="no">SALDO</td>
            <td ><?=$formatter->asCurrency($model->saldo)?></td>
          </tr>
        </tfoot>
      </table>

       <div id="notices">
        <div>TOTAL DESPESA: <?=$formatter->asCurrency($valor)?></div>
        <div class="notice"><?=convert_number_to_words($valor)?> escudos</div>
      </div>
     
    </main>
  