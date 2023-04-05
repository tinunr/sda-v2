<?php
use yii\helpers\Url;
$formatter = \Yii::$app->formatter;
$saldo = $model->valor;
?>
  
   
  
    <main>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="">Exmos.(s) Sr.(s)</div>
          <div class="name"><?=$model->person->nome?></div>
          <div class="address">NIF: <?=$model->person->nif?></div>
        </div>
         <div id="invoice">
          <h1>HISTÓRICO</h1>
          <h1>NOTA DE CREDITO Nº <?=$model->numero.'/'.$model->bas_ano_id?></h1>
          <div class="address">Data: <?=$formatter->asDate($model->data)?></div>
        </div>
      </div>

<br>
<br>
<br>
<br>
<br>
      <p><strong>Nota de Credito</strong></p>

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
        <?php $i= 2;foreach ($model->despesa->ofAccounts as $key => $value):?>
        <?php 
        $saldo = $saldo - $value->valor;
        ?>
          <tr>
            <td class="pdf desc">Enconto de Conta</td>
            <td class="pdf desc"><?=$value->numero.'/'.$value->bas_ano_id ?></td>
            <td class="pdf date"><?=$formatter->asDate($value->data) ?></td>
            <td class="pdf total"><?=$formatter->asCurrency($value->valor) ?></td>
            <td class="pdf total"><?=$formatter->asCurrency($saldo)?>;</td>
          </tr>
          <?php $i++; endforeach ;?>
        <?php foreach ($model->despesa->ofAccountsItem as $key => $value_b):?>
            <?php 
            $saldo = $saldo - $value_b->valor;
            ?>
          <tr>
            <td class="pdf desc">Encontro de Conta</td>
             <td class="pdf desc"><?=$value_b->ofAccounts->numero.'/'.$value_b->ofAccounts->bas_ano_id ?></td>
            <td class="pdf date"><?=$formatter->asDate($value_b->ofAccounts->data) ?></td>
            <td class="pdf total"><?=$formatter->asCurrency($value_b->valor) ?></td>
            <td class="pdf total"><?=$formatter->asCurrency($saldo)?></td>
          </tr>
          <?php $i++; endforeach ;?>
          <?php foreach ($model->despesa->pagamentoItem as $key => $value_c):?>
            <?php 
            $saldo = $saldo - $value_c->valor;
            ?>
          <tr>
            <td class="pdf desc">Pagamento</td>
             <td class="pdf desc"><?=$value_c->pagamento->numero.'/'.$value_c->pagamento->bas_ano_id ?></td>
            <td class="pdf date"><?=$formatter->asDate($value_c->pagamento->data) ?></td>
            <td class="pdf total"><?=$formatter->asCurrency($value_c->valor) ?></td>
            <td class="pdf total"><?=$formatter->asCurrency($saldo)?></td>
          </tr>
          <?php $i++; endforeach ;?>
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




