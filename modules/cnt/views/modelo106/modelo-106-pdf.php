<?php 
$formatter = Yii::$app->formatter;

?>

 <div class="titulo-principal"> <h5 class="titulo">MODELO 106</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

 <table class="table">
  <thead>
    <tr>
      <th colspan="2">TIPO DE OPERAÇÃO</th>
      <th colspan="2">Base Tributável</th>
      <th colspan="2">Imposto a favor do sujeito passivo</th>
      <th colspan="2">Imposto a favor do Estado</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td rowspan="3" class="row-number">1</td>
      <td>Transmissões de bens e prestação de serviços em que liquidou imposto:</td>
      <td colspan="6"></td>
    </tr>
    <tr>
      <td>Taxa normal</td>
      <td class="td-number">01</td>
      <td><?=$formatter->asCurrency($model->valor_1)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="td-number">02</td>
      <td><?=$formatter->asCurrency($model->valor_2)?></td>
    </tr>
    <tr>
      <td>Taxa especial</td>
      <td class="td-number">03</td>
      <td><?=$formatter->asCurrency($model->valor_3)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="td-number">04</td>
      <td><?=$formatter->asCurrency($model->valor_4)?></td>
    </tr>

    <tr>
      <td>2</td>
      <td>Operações em que liquidou o IVA nos termos do Decreto - Lei nº 16/2004 de 20 de Maio (valor recebido)</td>
      <td class="td-number">05</td>
      <td><?=$formatter->asCurrency($model->valor_8)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="td-number">06</td>
      <td><?=$model->valor_6?></td>
    </tr>


    <tr>
      <td >3</td>
      <td>Operações em que o IVA foi liquidado pelo contratante</td>
      <td class="td-number">07</td>
      <td><?=$formatter->asCurrency($model->valor_7)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td rowspan="4">4</td>
      <td>Transmissões de bens e prestação de serviços:</td>
      <td colspan="6"></td>
    </tr>
    <tr>
      <td>Isentas com direito a dedução</td>
      <td class="td-number">08</td>
      <td><?=$formatter->asCurrency($model->valor_8)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>      
    </tr>
    <tr>
      <td>Isentas sem direito a dedução (art. 9º exc. 15, 28, 29, 32 e 33 do RIVA)</td>
      <td class="td-number">09</td>
      <td><?=$formatter->asCurrency($model->valor_9)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Não tributados (art. 6º, nº 7 do RIVA)</td>
      <td class="td-number">10</td>
      <td><?=$formatter->asCurrency($model->valor_10)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td >5</td>
      <td>Aquisições dos serviços fornecidos por um prestador que não tenha sede, estabelecimento estável ou domicílio em Cabo Verde, cujo imposto foi liquidado pelo declarante (art. nº6, nº5 e 6 do RIVA)</td>
      <td class="td-number">11</td>
      <td><?=$formatter->asCurrency($model->valor_11)?></td>
      <td class="td-number">12</td>
      <td><?=$formatter->asCurrency($model->valor_12)?></td>
      <td class="td-number">13</td>
      <td><?=$formatter->asCurrency($model->valor_13)?></td>
    </tr>

    <tr>
      <td>6</td>
      <td>Aquisição dos serviços efetuados nos termos do art. 2º, alínea f) do RIVA - Construção Civil, em que o IVA foi liquidado pelo declarante.</td>
      <td class="td-number">14</td>
      <td><?=$formatter->asCurrency($model->valor_14)?></td>
      <td class="td-number">15</td>
      <td><?=$formatter->asCurrency($model->valor_18)?></td>
      <td class="td-number">16</td>
      <td><?=$model->valor_16?></td>
    </tr>


    <tr>
      <td rowspan="5">7</td>
      <td>Transmissões de bens e prestações de serviço efectuadas ao sujeito passivo declarante:</td>
      <td colspan="6"></td>
    </tr>
    <tr>
      <td>Investimentos:</td>
      <td class="td-number">17</td>
      <td><?=$formatter->asCurrency($model->valor_17)?></td>
      <td class="td-number">18</td>
      <td><?=$formatter->asCurrency($model->valor_19)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
     <tr>
      <td>Inventários:</td>
      <td class="td-number">19</td>
      <td><?=$formatter->asCurrency($model->valor_19)?></td>
      <td class="td-number">20</td>
      <td><?=$formatter->asCurrency($model->valor_20)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
     <tr>
      <td>Outros Bens de Consumo:</td>
      <td class="td-number">21</td>
      <td><?=$formatter->asCurrency($model->valor_21)?></td>
      <td class="td-number">22</td>
      <td><?=$formatter->asCurrency($model->valor_22)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
     <tr>
      <td>Serviços:</td>
      <td class="td-number">23</td>
      <td><?=$formatter->asCurrency($model->valor_23)?></td>
      <td class="td-number">24</td>
      <td><?=$formatter->asCurrency($model->valor_24)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td>8</td>
      <td>Imposto Dedutível nas importações de bens efetuadas pelo SP</td>
      <td class="td-number">25</td>
      <td><<?=$formatter->asCurrency($model->valor_25)?></td>
      <td class="td-number">26</td>
      <td><?=$formatter->asCurrency($model->valor_26)?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      
    </tr>

    <tr>
      <td>9</td>
      <td>Regularizações mensais ou anuais comunicadas pela Admin. Fiscal</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="td-number">27</td>
      <td><?=$formatter->asCurrency($model->valor_27)?></td>      
      <td class="td-number">28</td>
      <td><?=$formatter->asCurrency($model->valor_28)?></td>
    </tr>


    <tr>
      <td>10</td>
      <td>Regularizações mensais ou anuais, exceto as comunicadas pela Admin. Fiscal</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="td-number">29</td>
      <td><?=$formatter->asCurrency($model->valor_29)?></td>      
      <td class="td-number">30</td>
      <td><?=$formatter->asCurrency($model->valor_30)?></td>
    </tr>



    <tr>
      <td colspan="6">PERCENTAGEM ESTIMADA (dedução parcial pro rata)</td>  
      <td class="td-number">31</td>
      <td><<?=$formatter->asCurrency($model->valor_31)?></td>
    </tr>
    <tr>
      <td colspan="2">SOMAS -></td>
      <td class="td-number">32</td>
      <td><?=$formatter->asCurrency($model->valor_32)?></td>
      <td class="td-number">33</td>
      <td><?=$formatter->asCurrency($model->valor_33)?></td>      
      <td class="td-number">34</td>
      <td><?=$formatter->asCurrency($model->valor_34)?></td>
    </tr>
    <tr>
      <td colspan="3">(32=01+03+05+07+08+09+10+11+14+17+19+21+23+25) </td>
      <td colspan="3">(33=12+15+18+20+22+24+26+27+29) </td>
      <td colspan="2">(34=02+04+06+13+16+28+30)</td>
    </tr>


  </tbody>
</table>



<table class="table table-border">
 <tbody>
  <tr>
    <td rowspan="3">Valor antes da utilização do acesso a reportar de períodos anteriores
</td>
    <td colspan="3">Apuramento do Período</td>
  </tr>
  <tr>
    <td>Se o valor inscrito no campo 34 é superior ao de campo 33 (caso se aplique, multiplicar pelo campo 31), campo 35=34-33 </td>
    <td class="td-number">35</td>
    <td><?=$formatter->asCurrency($model->valor_35) ?></td>
  </tr>
  <tr>
    <td>Se o valor inscrito no campo 33  (caso se aplique, multiplicar pelo campo 31), é superior ao campo 34, campo 36=33-34
</td>
    <td class="td-number">36</td>
    <td><?=$formatter->asCurrency($model->valor_36) ?></td>
  </tr>
  <tr>
    <td colspan="3">UTILIZAÇÃO DE CRÉDITOS DE PERÍODOS ANTERIORES:                         o campo 37 só pederá ser preenchido se Declaração apresentada dentro no prazo legal</td>
  </tr>
  <tr>
    <td></td>
    <td>Excesso a reportar dos períodos anteriores</td>
    <td class="td-number">37</td>
    <td><?=$formatter->asCurrency($model->valor_37) ?></td>
  </tr>
  </tbody>
</table>


<table class="table">
  <tbody>
    <tr>
      <th>1-ENTIDADE COMPETENTE:</th>
      <th colspan="5">____________________________________________________________________________________________________________________________________________________________</th>
    </tr>
    <tr>
      <th colspan="4"></th>
      <th class="td-number">38</th>
      <th><?=$formatter->asCurrency($model->valor_38) ?></th>
    </tr>
  </tbody>
</table>
