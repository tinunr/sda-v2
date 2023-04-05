<?php
use yii\helpers\Url;
use app\modules\fin\models\FaturaProvisoria;
$formatter = \Yii::$app->formatter;
$faturaProvisoria = FaturaProvisoria::find()
                  ->where(['dsp_processo_id'=>$model->dsp_processo_id])
                  ->one();

$fpRc = Yii::$app->FinQuery->fpFaturaDefinitiva($model->id);
$total_honorario = 0;
$total_outros = 0;
$total_despesas = 0;
$total_honorariob = 0;
$total_outrosb = 0;
$total_despesasb = 0;
$hta = 0;
$htb = 0;
$regimeConfig = Yii::$app->params['regimeConfig'];
$valorBaseHonorario = Yii::$app->FinQuery->valorBaseHonorario($model->id);

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


   
  
    <section  id="<?=($model->status)?'':'anulado'?>">
    <div id="invoice" >           
            <div id="logo"><img src="logo.png"></div>
            <div id="company">
              <h1>FATURA DEFINITIVA  Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
              <div class="address">PROCESSO: <?=$model->processo->numero.'/'.$model->processo->bas_ano_id?> NORD: <?=empty($model->processo->nord->id)?'':$model->processo->nord->id?> </div>
              <div class="address"> <?=$fpRc['fatura_provisorias']?>  <?=$fpRc['recibos']?> </div>
                <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
              
            </div>
        </div>
        
      <div id="details" class="clearfix">
        <div id="client">
        <div class="name1"><?=$company['name']?></div>
              <div><?=$company['adress2']?></div>
              <div>NIF: <?=$company['nif']?></div>
          
        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Original</strong></div>
        <div id="invoice">
        <div class="">Exmos.(s) Sr.(s)</div>
          <h2 class="name"><?=$model->person->nome?></h2>
          <div class="address"><?=$model->person->endereco?></div>
          <div class="address">NIF <?=$model->person->nif?></div>
          <div class="email"><a href="mailto:<?=$model->person->email?>"><?=$model->person->email?></a></div>
        </div>
      </div>
      <br>

      <p ><strong>Biblhete de despacho (b.d):</strong>   Regime: <strong><?=$faturaProvisoria->dsp_regime_id?> / <?=str_pad(\app\modules\dsp\services\NordService::subRegime($model->nord),3,'0',STR_PAD_LEFT)?></strong>, N.º Ordem <strong><?=$model->n_registo?></strong>, de <strong><?=$formatter->asDate($model->data_registo)?></strong>, N.º Receita <strong><?=$model->n_receita?></strong> de <strong><?=$formatter->asDate($model->data_receita)?></strong> , Número artigos Pautais: <strong><?=\app\modules\dsp\services\NordService::totalNumberOfItems($model->nord)?></strong>.</p>
       <p style="margin-top: -20px; " ><strong>Mercadoria: </strong><?=$model->descricao?></p>
       <?php if(strlen($model->descricao)>120){$hta = $hta+1;}?>









       <p><strong>Honorário</strong></p>
       <table class="pdf", >
        <thead>
          <tr>
            <th class="pdf desc"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
            <th class="pdf total" style="width: 5px;"><strong>Uni./Ton./Hec.</strong></th>
            <th class="pdf total" style="width: 310px;"><strong>Posição da Tabela</strong></th>
            <th class="pdf total"><strong>Total</strong></th>
          </tr>
        </thead>
        <?php if(!$model->person->isencao_honorario&&Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)>0): ?>
        <tbody>

        <?php if($model->taxa_comunicaco>0) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->taxa_comunicaco?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['taxa_comunicaco']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->taxa_comunicaco)?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$posicao_tabela['taxa_comunicaco']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->taxa_comunicaco)?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->tn)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->tn*$posicao_tabela['tn']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['tn']?></td>
            <td class="pdf total"><?=$model->tn?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$posicao_tabela['tn']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->tn*$posicao_tabela['tn'])?></td>
          </tr>
          <?php endif;?>
           <?php if(!empty($model->form)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->form*$posicao_tabela['form']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['form']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->form?></td>
            <td class="pdf total"><?=$posicao_tabela['form']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->form*$posicao_tabela['form'])?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->regime_normal)) :?>
        <?php $total_honorario = $total_honorario + $model->regime_normal*$posicao_tabela['regime_normal']?>
        <?php $hta = $hta+1?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['regime_normal']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->regime_normal?></td>
            <td class="pdf total"><?=$posicao_tabela['regime_normal']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->regime_normal*$posicao_tabela['regime_normal'])?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->regime_especial)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->regime_especial*$posicao_tabela['regime_especial']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['regime_especial']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->regime_especial?></td>
            <td class="pdf total"><?=$posicao_tabela['regime_especial']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->regime_especial*$posicao_tabela['regime_especial'])?></td>
          </tr>
          <?php endif;?>
        <?php $hta = $hta+1?>
          <?php if(!empty($model->exprevio_comercial)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->exprevio_comercial*$posicao_tabela['exprevio_comercial']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['exprevio_comercial']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->exprevio_comercial?></td>
            <td class="pdf total"><?=$posicao_tabela['exprevio_comercial']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->exprevio_comercial*$posicao_tabela['exprevio_comercial'])?></td>
          </tr>
          <?php endif;?>
           <?php if(!empty($model->expedente_matricula)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->expedente_matricula*$posicao_tabela['expedente_matricula']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['expedente_matricula']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->expedente_matricula?></td>
            <td class="pdf total"><?=$posicao_tabela['expedente_matricula']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->expedente_matricula*$posicao_tabela['expedente_matricula'])?></td>
          </tr>
          <?php endif;?>           
          <?php if(!empty($model->dv)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->dv*$posicao_tabela['dv']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['dv']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->dv?></td>
            <td class="pdf total"><?=$posicao_tabela['dv']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->dv*$posicao_tabela['dv'])?></td>
          </tr>
          <?php endif;?>

          <?php if(!empty($model->gti)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->gti*$posicao_tabela['gti']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['gti']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->gti?></td>
            <td class="pdf total"><?=$posicao_tabela['gti']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->gti*$posicao_tabela['gti'])?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->pl)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->pl*$posicao_tabela['pl']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['pl']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->pl?></td>
            <td class="pdf total"><?=$posicao_tabela['pl']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->pl*$posicao_tabela['pl'])?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->tce)) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + $model->tce*$posicao_tabela['tce']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['tce']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->tce?></td>
            <td class="pdf total"><?=$posicao_tabela['tce']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->tce*$posicao_tabela['tce'])?></td>
          </tr>
          <?php endif;?>
          <?php if( $model->acrescimo >0) :?>
        <?php $hta = $hta+1?>
        <?php $total_honorario = $total_honorario + ($model->acrescimo*$regimeConfig['valorPorItem']) ?>
          <tr>
            <td class="pdf desc">Acréscimo</td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->acrescimo?></td>
            <td class="pdf total">100 por item</td>
            <td class="pdf total"><?=($model->acrescimo*$regimeConfig['valorPorItem'])?></td>
          </tr>
          <?php endif;?>
        
        <?php $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)-$total_honorario;?>
          <?php if(empty($model->tn)&&!$model->person->isencao_honorario&&((Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)-$total_honorario) >0)):?>
        <tr>
            <td class="pdf desc">Tabela Honorário</td>
             <td class="pdf total">
              <?=($valorBaseHonorario['tipo']&&$valor_tabel_honorario>500)?$formatter->asCurrency($valorBaseHonorario['valor']):''?></td>
            <td class="pdf total"><?=(!$valorBaseHonorario['tipo']&&$valor_tabel_honorario>500)?$valorBaseHonorario['valor']:''?></td>
            <td class="pdf desc">
              <?php if($valor_tabel_honorario==500):?>
                <?php echo "Valor Mininimo 500";?>
              <?php elseif (!empty($model->posicao_tabela)):?>
               <?=$model->posicao_tabela?>
               <?php else:?>
              <?=empty($faturaProvisoria->regimeItem->forma)?(empty($faturaProvisoria->regimeItemItem->forma)?'Valor Mininimo 500':$faturaProvisoria->regimeItemItem->forma):$faturaProvisoria->regimeItem->forma?>
               <?php endif;?>
                 
               </td>
            <td class="pdf total"><?=$formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)-$total_honorario)?></td>
            <?php $a = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)-$total_honorario?>
            <?php $total_honorario = $total_honorario + $a?>
          </tr>
          <?php endif;?>

          </tbody>
          <?php endif;?>
          
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="4"><strong>Total Geral Honorário</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id))?></strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>IVA sobre Honorário</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id))?></strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>SUB. TOTAL (Honorário mais IVA de Honorário) <?=convert_number_to_words(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)+Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id))?> </strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)+Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id))?></strong></td>
            </tr>
        </tfoot>

      </table>


      <p><strong>Outros</strong></p>
      
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>
          <?php  foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->id) as $key => $modelItem) :?>
          <tr>
            <td class="pdf desc"colspan="2"><?=str_pad($modelItem['id'],2,'0',STR_PAD_LEFT).' - '.$modelItem['descricao']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($modelItem['valor'])?></td>
            <?php $total_outros = $total_outros +  $modelItem['valor'];?>
          </tr>
        <?php $hta++; endforeach;?>
        </tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>SUB. TOTAL: (Outros) <?=convert_number_to_words($total_outros)?> escudos</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency($total_outros)?></strong></td>
          </tr>
        </tfoot>
      </table>     
    </div>


<hr>

       <p><strong>Despesas</strong></p>
      
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>
          <?php  foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->id) as $key => $modelItem) :?>
          <tr>
            <td class="pdf desc"colspan="2"><?=str_pad($modelItem['id'],2,'0',STR_PAD_LEFT).' - '.$modelItem['descricao']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($modelItem['valor'])?></td>
            <?php $total_despesas = $total_despesas +  $modelItem['valor'];?>
          </tr>
        <?php $hta++; endforeach;?>
        <?php for ($j=$hta; $j < 22; $j++):?>
            <tr>
            <td class="pdf desc"colspan="2">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
          </tr>
        <?php endfor;?>
        </tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>SUB. TOTAL: (Despesas) <?=convert_number_to_words($total_despesas)?> escudos</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency($total_despesas)?></strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="2"><strong>TOTAL: <?=convert_number_to_words($model->valor)?> escudos</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency($model->valor)?></strong></td>
          </tr>
        </tfoot>
      </table>  
    </div>

                  <p style="text-align: center;">O Despachante</p>
                  <p><?= $model->send ? 'CONFERÊNCIA ELETRÓNICA' : '.................................' ?></p>
                  <p style=" text-align: center;">/ <?=$formatter->asDate($model->data)?> /</p>
    
  </section>  





























































 
  
      <section  id="<?=($model->status)?'':'anulado'?>">
    <div id="invoice" >           
            <div id="logo"><img src="logo.png"></div>
            <div id="company">
              <h1>FATURA DEFINITIVA  Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
              <div class="address">PROCESSO: <?=$model->processo->numero.'/'.$model->processo->bas_ano_id?> NORD: <?=empty($model->processo->nord->id)?'':$model->processo->nord->id?> </div>
              <div class="address"> <?=$fpRc['fatura_provisorias']?> <?=$fpRc['recibos']?> </div>
                <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
              
            </div>
        </div>
        
      <div id="details" class="clearfix">
        <div id="client">
        <div class="name1"><?=$company['name']?></div>
              <div><?=$company['adress2']?></div>
              <div>NIF: <?=$company['nif']?></div>
          
        </div>
        <div style="text-align: right; margin-top: -40px; text-transform: uppercase;  "><strong>Contabilidade</strong></div>
        <div id="invoice">
        <div class="">Exmos.(s) Sr.(s)</div>
          <h2 class="name"><?=$model->person->nome?></h2>
          <div class="address"><?=$model->person->endereco?></div>
          <div class="address">NIF <?=$model->person->nif?></div>
          <div class="email"><a href="mailto:<?=$model->person->email?>"><?=$model->person->email?></a></div>
        </div>
      </div>

      <br>

      <p ><strong>Biblhete de despacho (b.d):</strong>   Regime: <strong><?=$faturaProvisoria->dsp_regime_id?> / <?=str_pad(Yii::$app->DspHonorario->subRegime($model->nord),3,'0',STR_PAD_LEFT)?></strong>, N.º Ordem <strong><?=$model->n_registo?></strong>, de <strong><?=$formatter->asDate($model->data_registo)?></strong>, N.º Receita <strong><?=$model->n_receita?></strong> de <strong><?=$formatter->asDate($model->data_receita)?></strong> , Número artigos Pautais: <strong><?=Yii::$app->DspHonorario->totalNumberOfItems($model->nord)?></strong>.</p>
       <p style="margin-top: -20px; " ><strong>Mercadoria: </strong><?=$model->descricao ?></p>
       <?php if(strlen($model->descricao)>120){$htb = $htb+1;}?>


       <p><strong>Honorário</strong></p>
       <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
            <th class="pdf total"><strong>Uni./Ton./Hec.</strong></th>
            <th class="pdf total" style="width: 310px;"><strong>Posisão da Tabela</strong></th>
            <th class="pdf total"><strong>Total</strong></th>
          </tr>
        </thead>
        <?php if(!$model->person->isencao_honorario&&Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)>0): ?>
        <tbody>
        <?php if(!empty($model->taxa_comunicaco)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->taxa_comunicaco?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['taxa_comunicaco']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->taxa_comunicaco)?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$posicao_tabela['taxa_comunicaco']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->taxa_comunicaco)?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->tn)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->tn*$posicao_tabela['tn']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['tn']?></td>
            <td class="pdf total"><?=$model->tn?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$posicao_tabela['tn']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->tn*$posicao_tabela['tn'])?></td>
          </tr>
          <?php endif;?>
           <?php if(!empty($model->form)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->form*$posicao_tabela['form']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['form']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->form?></td>
            <td class="pdf total"><?=$posicao_tabela['form']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->form*$posicao_tabela['form'])?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->regime_normal)) :?>
        <?php $total_honorariob = $total_honorariob + $model->regime_normal*$posicao_tabela['regime_normal']?>
        <?php $htb = $htb+1?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['regime_normal']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->regime_normal?></td>
            <td class="pdf total"><?=$posicao_tabela['regime_normal']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->regime_normal*$posicao_tabela['regime_normal'])?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->regime_especial)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->regime_especial*$posicao_tabela['regime_especial']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['regime_especial']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->regime_especial?></td>
            <td class="pdf total"><?=$posicao_tabela['regime_especial']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->regime_especial*$posicao_tabela['regime_especial'])?></td>
          </tr>
          <?php endif;?>
        <?php $htb = $htb+1?>
          <?php if(!empty($model->exprevio_comercial)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->exprevio_comercial*$posicao_tabela['exprevio_comercial']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['exprevio_comercial']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->exprevio_comercial?></td>
            <td class="pdf total"><?=$posicao_tabela['exprevio_comercial']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->exprevio_comercial*$posicao_tabela['exprevio_comercial'])?></td>
          </tr>
          <?php endif;?>
           <?php if(!empty($model->expedente_matricula)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->expedente_matricula*$posicao_tabela['expedente_matricula']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['expedente_matricula']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->expedente_matricula?></td>
            <td class="pdf total"><?=$posicao_tabela['expedente_matricula']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->expedente_matricula*$posicao_tabela['expedente_matricula'])?></td>
          </tr>
          <?php endif;?>           
          <?php if(!empty($model->dv)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->dv*$posicao_tabela['dv']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['dv']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->dv?></td>
            <td class="pdf total"><?=$posicao_tabela['dv']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->dv*$posicao_tabela['dv'])?></td>
          </tr>
          <?php endif;?>
         
          <?php if(!empty($model->gti)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->gti*$posicao_tabela['gti']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['gti']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->gti?></td>
            <td class="pdf total"><?=$posicao_tabela['gti']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->gti*$posicao_tabela['gti'])?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->pl)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->pl*$posicao_tabela['pl']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['pl']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->pl?></td>
            <td class="pdf total"><?=$posicao_tabela['pl']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->pl*$posicao_tabela['pl'])?></td>
          </tr>
          <?php endif;?>
          <?php if(!empty($model->tce)) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + $model->tce*$posicao_tabela['tce']?>
          <tr>
            <td class="pdf desc"><?=$posicao_tabela_desc['tce']?></td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->tce?></td>
            <td class="pdf total"><?=$posicao_tabela['tce']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($model->tce*$posicao_tabela['tce'])?></td>
          </tr>
          <?php endif;?>
          <?php if( $model->acrescimo >0) :?>
        <?php $htb = $htb+1?>
        <?php $total_honorariob = $total_honorariob + ($model->acrescimo*$regimeConfig['valorPorItem']) ?>
          <tr>
            <td class="pdf desc">Acréscimo</td>
            <td class="pdf total"></td>
            <td class="pdf total"><?=$model->acrescimo?></td>
            <td class="pdf total">100 por item</td>
            <td class="pdf total"><?=($model->acrescimo*$regimeConfig['valorPorItem'])?></td>
          </tr>
          <?php endif;?>
        
          <?php $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)-$total_honorariob;?>
          <?php if(empty($model->tn)&&!$model->person->isencao_honorario&&((Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)-$total_honorariob) >0)):?>
        <tr>
            <td class="pdf desc">Tabela Honorário</td>
            <td class="pdf total"><?=($valorBaseHonorario['tipo']&&$valor_tabel_honorario>500)?$formatter->asCurrency($valorBaseHonorario['valor']):''?></td>
            <td class="pdf total"><?=(!$valorBaseHonorario['tipo']&&$valor_tabel_honorario>500)?$valorBaseHonorario['valor']:''?></td>
            <td class="pdf desc">
             <?php if($valor_tabel_honorario==500):?>
                <?php echo "Valor Mininimo 500";?>
              <?php elseif (!empty($model->posicao_tabela)):?>
               <?=$model->posicao_tabela?>
               <?php else:?>
              <?=empty($faturaProvisoria->regimeItem->forma)?(empty($faturaProvisoria->regimeItemItem->forma)?'Valor Mininimo 500':$faturaProvisoria->regimeItemItem->forma):$faturaProvisoria->regimeItem->forma?>
               <?php endif;?>
                
              </td>
            <td class="pdf total"><?=$formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)-$total_honorariob)?></td>
            <?php $a = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)-$total_honorariob?>
            <?php $total_honorariob = $total_honorariob + $a?>
          </tr>
          <?php endif;?>
          </tbody>
          <?php endif;?>
          
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="4"><strong>Total Geral Honorário</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id))?></strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>IVA sobre Honorário</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id))?></strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="4"><strong>SUB. TOTAL (Honorário mais IVA de Honorário) <?=convert_number_to_words(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)+Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id))?></strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)+Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id))?></strong></td>
            </tr>
        </tfoot>

      </table>


      <p><strong>Outros</strong></p>
      
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>
          <?php  foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->id) as $key => $modelItem) :?>
          <tr>
            <td class="pdf desc"colspan="2"><?=str_pad($modelItem['id'],2,'0',STR_PAD_LEFT).' - '.$modelItem['descricao']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($modelItem['valor'])?></td>
            <?php $total_outrosb = $total_outrosb +  $modelItem['valor'];?>
          </tr>
        <?php $htb++; endforeach;?>
        </tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>SUB. TOTAL: (Outros) <?=convert_number_to_words($total_outrosb)?> escudos</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency($total_outrosb)?></strong></td>
          </tr>
        </tfoot>
      </table>     
    </div>


<hr>

       <p><strong>Despesas</strong></p>
      
      <table class="pdf" >
        <thead>
          <tr>
            <th class="pdf desc" colspan="2"><strong>Designação</strong></th>
            <th class="pdf total"><strong>Valor</strong></th>
          </tr>
        </thead>
        <tbody>
          <?php  foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->id) as $key => $modelItem) :?>
          <tr>
            <td class="pdf desc"colspan="2"><?=str_pad($modelItem['id'],2,'0',STR_PAD_LEFT).' - '.$modelItem['descricao']?></td>
            <td class="pdf total"><?=$formatter->asCurrency($modelItem['valor'])?></td>
            <?php $total_despesasb = $total_despesasb +  $modelItem['valor'];?>
          </tr>
        <?php $htb++; endforeach;?>
        <?php for ($j=$htb; $j < 22; $j++):?>
            <tr>
            <td class="pdf desc"colspan="2">&nbsp;</td>
            <td class="pdf total">&nbsp;</td>
          </tr>
        <?php endfor;?>
        </tbody>
        <tfoot>
          <tr>
            <td class="pdf desc" colspan="2"><strong>SUB. TOTAL: (Despesas) <?=convert_number_to_words($total_despesasb)?> escudos</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency($total_despesasb)?></strong></td>
          </tr>
          <tr>
            <td class="pdf desc" colspan="2"><strong>TOTAL: <?=convert_number_to_words($model->valor)?> escudos</strong></td>
            <td class="pdf total"><strong><?=$formatter->asCurrency($model->valor)?></strong></td>
          </tr>
        </tfoot>
      </table>  
    </div>

                  <p style="text-align: center;">O Despachante</p>
                  <p><?= $model->send ? 'CONFERÊNCIA ELETRÓNICA' : '.................................' ?></p>
                  <p style=" text-align: center;">/ <?=$formatter->asDate($model->data)?> /</p>
     
  </section> 