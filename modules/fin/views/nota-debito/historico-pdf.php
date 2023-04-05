<?php
use yii\helpers\Url;
$formatter = \Yii::$app->formatter;
$saldo = $model->valor;

?>
  
   
  
    <main  id="<?=($model->status)?'':'anulado'?>">
      <div id="details" class="clearfix">
        <div id="client">
          <div class="">Exmos.(s) Sr.(s) Aviso de credito nº <?=$model->numero.'/'.$model->bas_ano_id?></div>
          <div class="name"><?=$model->person->nome?></div>
          <div class="address"><?=$model->person->nif?></div>
          <div class="address"><?=$model->person->email?></div>
        </div>
         <div id="invoice">
          <h1>NOTA DE DEBITO Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
          <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
        </div>
      </div>

<br>
<br>
<br>
<br>
<br>
      <p><strong>Nota de Debito</strong></p>

      <table class="pdf">
          <tr>
            <th class="pdf total">Descrição</th>
            <th class="pdf desc"><?=$model->descricao?></th>
          </tr>
        <tbody>
        <tr>
            <th class="pdf total">Valor</th>
            <th class="pdf desc"><?=$formatter->asCurrency($model->valor)?></th>
          </tr>
        </tbody>
      </table>


<br>
<br>
<br>
<br>
<br>

      
    <!-- despesas geradas -->
    <p><strong>Encontro de Contas</strong></p>

<table class="pdf">
    <tr>
      <th class="pdf desc">Numero</th>
      <th class="pdf date">Data</th>
      <th class="pdf total">Valor</th>
      <th class="pdf total">Saldo</th>
    </tr>
  <tbody>
  <?php foreach($model->ofAccountsItem as $value):?>
  <tr>
  <?php 
  $saldo = $saldo - $value->valor;?>
      <td class="pdf desc"><?=$value->ofAccounts->numero.'/'.$value->ofAccounts->bas_ano_id?></td>
      <td class="pdf date"><?=$formatter->asDate($value->ofAccounts->data )?></td>
      <td class="pdf total"><?=$formatter->asCurrency($value->valor) ?></td>
      <td class="pdf total"><?=$formatter->asCurrency($saldo)?></td>
    </tr>
<?php endforeach;?>
    <?php $i=1; for ($j=$i; $j <20; $j++):?>
      <tr class="pdf">
          <td  class="desc pdf">&nbsp; </td>
          <td  class="desc pdf">&nbsp;</td>
          <td  class="qty pdf">&nbsp;</td>
          <td  class="desc pdf">&nbsp;</td>
      </tr>
    <?php endfor ;?>

  </tbody>
   <tfoot>
    <tr>
      <td class="pdf no">Saldo</td>
      <td class="pdf total">&nbsp;</td>
      <td class="pdf total">&nbsp;</td>
      <td class="pdf total"><?=$formatter->asCurrency($saldo)?></td>
    </tr>
  </tfoot>
</table> 

      
     
    </main>


