<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;
use app\modules\cnt\models\Modelo106Cliente;
use app\modules\cnt\models\Modelo106Fornecedor;
$formatter = Yii::$app->formatter;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Mdelo 106 Ano:'.$model->ano.' Mes:'.$model->mes;
?>

<section>
    <div class="row button-bar">
        <div class="row pull-left">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class'=>'btn btn-warning']) ?>
        </div>
        <div class="row pull-right">

            <?= Html::a('<i class="fas fa-edit"></i> Editar MANUAL', ['modelo106-update', 'cnt_modelo_106_id'=>$model->id], ['class' => 'btn btn-primary']) ?>


            <?= \yii\bootstrap\ButtonDropdown::widget([
        'label' => '<i class="fas fa-sync"></i> Resetar',
        'options'=>['class'=>'dropdown-btn dropdown-new'],
        'encodeLabel' => false,
        'dropdown' => [
            'encodeLabels' => false,
            'options'=>['class'=>'dropdown-new'],
            'items' => [
                ['label' =>'<i class="fas fa-sync"> Modelo 106</i> ', 'url' =>  ['modelo106-update-auto', 'id'=>$model->id]],
                ['label' =>'<i class="fas fa-sync"> Anexo Cliente</i> ', 'url' =>  ['update-cliente-cnt', 'id'=>$model->id]],
                ['label' =>'<i class="fas fa-sync"> Anexo Fornecedor</i> ', 'url' => ['update-fornecedor-cnt','id'=>$model->id]],
            ],
        ],
    ]);?>

            <?= \yii\bootstrap\ButtonDropdown::widget([
        'label' => '<i class="fas fa-plus"></i> Novo',
        'options'=>['class'=>'dropdown-btn dropdown-new'],
        'encodeLabel' => false,
        'dropdown' => [
            'encodeLabels' => false,
            'options'=>['class'=>'dropdown-new'],
            'items' => [
                ['label' =>'<i class="fas fa-file-pdf"> Anexo Cliente</i> ', 'url' => ['cliente-create','cnt_modelo_106_id'=>$model->id]],
                ['label' =>'<i class="fas fa-file-pdf"> Anexo Fornecedor</i> ', 'url' => ['fornecedor-create','cnt_modelo_106_id'=>$model->id]],
            ],
        ],
    ]);?>



            <?= \yii\bootstrap\ButtonDropdown::widget([
        'label' => '<i class="fas fa-file-pdf"></i> PDF',
        'options'=>['class'=>'dropdown-btn dropdown-new'],
        'encodeLabel' => false,
        'dropdown' => [
            'encodeLabels' => false,
            'options'=>['class'=>'dropdown-new'],
            'items' => [
                ['label' =>'<i class="fas fa-file-pdf"> Modelo 106</i> ', 'url' => ['modelo106-pdf','cnt_modelo_106_id'=>$model->id]],
                ['label' =>'<i class="fas fa-file-pdf"> Anexo Cliente</i> ', 'url' => ['cliente-pdf','cnt_modelo_106_id'=>$model->id]],
                ['label' =>'<i class="fas fa-file-pdf"> Anexo Fornecedor</i> ', 'url' => ['fornecedor-pdf','cnt_modelo_106_id'=>$model->id]],
            ],
        ],
    ]);?>

            <?= \yii\bootstrap\ButtonDropdown::widget([
        'label' => '<i class="fas fa-file-code"></i> XML',
        'options'=>['class'=>'dropdown-btn dropdown-new'],
        'encodeLabel' => false,
        'dropdown' => [
            'encodeLabels' => false,
            'options'=>['class'=>'dropdown-new'],
            'items' => [
              ['label' =>'<i class="fas fa-file-code"> Modelo 106</i> ', 'url' => ['modelo106-xml','cnt_modelo_106_id'=>$model->id]],
              ['label' =>'<i class="fas fa-file-code"> Anexo Cliente</i> ', 'url' => ['cliente-xml','cnt_modelo_106_id'=>$model->id]],
              ['label' =>'<i class="fas fa-file-code"> Anexo Fornecedor</i> ', 'url' => ['fornecedor-xml','cnt_modelo_106_id'=>$model->id]],
            ],
        ],
    ]);?>

            <?= \yii\bootstrap\ButtonDropdown::widget([
        'label' => '<i class="fas fa-file-excel"></i> EXCEL',
        'options'=>['class'=>'dropdown-btn dropdown-new'],
        'encodeLabel' => false,
        'dropdown' => [
            'encodeLabels' => false,
            'options'=>['class'=>'dropdown-new'],
            'items' => [
              ['label' =>'<i class="fas fa-file-excel"> Modelo 106</i> ', 'url' => ['modelo106-excel','cnt_modelo_106_id'=>$model->id]],
              ['label' =>'<i class="fas fa-file-excel"> Anexo Cliente</i> ', 'url' => ['cliente-excel','cnt_modelo_106_id'=>$model->id]],
              ['label' =>'<i class="fas fa-file-excel"> Anexo Fornecedor</i> ', 'url' => ['fornecedor-excel','cnt_modelo_106_id'=>$model->id]],
            ],
        ],
    ]);?>
        </div>
    </div>

    <div class="titulo-principal">
        <h5 class="titulo">MODELO 106</h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>


    <div class="w3-bar w3-black">
        <button class="w3-bar-item w3-button" onclick="openCity('Tokyo')">Modelo 106</button>
        <button class="w3-bar-item w3-button" onclick="openCity('London')">Anexo Cliente</button>
        <button class="w3-bar-item w3-button" onclick="openCity('Paris')">Anexo Fornecedor</button>
    </div>
    <div class="row">
        <div id="London" class="city" style="display:none">

            <div class="titulo-principal">
                <h5 class="titulo">ANEXO de Clientee</h5>
                <div id="linha-longa">
                    <div id="linha-curta"></div>
                </div>
            </div>

            <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter'=>TRUE,
        'columns' => [
           [
            'class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['style' => 'width:70px;'],
            'header'=>'Ações',
            'template' => '{update}{delete}',
             'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-pencil-alt"></i>', $url, [
                                    'title' => 'ATULAIZAR',
                                    'class'=>'btn  btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fa fa-trash-alt"></i>', $url, [
                                    'title' => 'ELIMINAR',
                                    'class'=>'btn  btn-xs',
                                    'data' => [
                                      'confirm' => 'Pretende realmente eleminar este registro?',
                                  ],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url = Url::to(['/cnt/modelo106/update-cliente', 'id'=>$model->id,'cnt_modelo_106_id'=>$model->cnt_modelo_106_id]);;
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = Url::to(['/cnt/modelo106/cliente-delete', 'id'=>$model->id,'cnt_modelo_106_id'=>$model->cnt_modelo_106_id]);;
                        return $url;
                    }
              }

            ],
           ['class' => 'yii\grid\SerialColumn'], 
           'origem',
           'nif',
           'designacao',
           'tp_doc',
           'num_doc',
           'data:date',
           ['attribute' => 'vl_fatura',
             'value'=>'vl_fatura',
             'footer'=>$formatter->asCurrency(Modelo106Cliente::totalItem($dataProvider->models,'vl_fatura')),
            ],
            ['attribute' => 'vl_base_incid',
             'value'=>'vl_base_incid',
             'footer'=>$formatter->asCurrency(Modelo106Cliente::totalItem($dataProvider->models,'vl_base_incid')),
            ],
           'tx_iva',
            ['attribute' => 'iva_liq',
             'value'=>'iva_liq',
             'footer'=>$formatter->asCurrency(Modelo106Cliente::totalItem($dataProvider->models,'iva_liq')),
            ],
            ['attribute' => 'nao_liq_imp',
             'value'=>'nao_liq_imp',
             'footer'=>$formatter->asCurrency(Modelo106Cliente::totalItem($dataProvider->models,'nao_liq_imp')),
            ],
           'linha_dest_mod',
        ],
    ]); ?>
        </div>







        <div id="Paris" class="city" style="display:none">
            <div class="titulo-principal">
                <h5 class="titulo">ANEXO de fornecedor</h5>
                <div id="linha-longa">
                    <div id="linha-curta"></div>
                </div>
            </div>

            <?= GridView::widget([
        'dataProvider' => $dataProviderFornecedor,
        'showFooter'=>TRUE,
        'columns' => [
           [
            'class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['style' => 'width:70px;'],
            'header'=>'Ações',
            'template' => '{update}{delete}',
             'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fa fa-pencil-alt"></i>', $url, [
                                    'title' => 'ATULAIZAR',
                                    'class'=>'btn  btn-xs',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fa fa-trash-alt"></i>', $url, [
                                    'title' => Yii::t('app', 'ELIMINAR'),
                                    'class'=>'btn  btn-xs',
                                    'data' => [
                                      'confirm' => 'Pretende realmente eleminar este registro?',
                                  ],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url = Url::to(['/cnt/modelo106/update-fornecedor', 'id'=>$model->id,'cnt_modelo_106_id'=>$model->cnt_modelo_106_id]);;
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = Url::to(['/cnt/modelo106/fornecedor-delete', 'id'=>$model->id,'cnt_modelo_106_id'=>$model->cnt_modelo_106_id]);;
                        return $url;
                    }
              }

            ],
           ['class' => 'yii\grid\SerialColumn'], 
           'origem',
           'nif',
           'designacao',
           'tp_doc',
           'num_doc',
           'data:date',
           ['attribute' => 'vl_fatura',
             'value'=>'vl_fatura',
             'footer'=>$formatter->asCurrency(Modelo106Fornecedor::totalItem($dataProviderFornecedor->models,'vl_fatura')),
            ],
            ['attribute' => 'vl_base_incid',
             'value'=>'vl_base_incid',
             'footer'=>$formatter->asCurrency(Modelo106Fornecedor::totalItem($dataProviderFornecedor->models,'vl_base_incid')),
            ],
           'tx_iva',
            ['attribute' => 'iva_sup',
             'value'=>'iva_sup',
             'footer'=>$formatter->asCurrency(Modelo106Fornecedor::totalItem($dataProviderFornecedor->models,'iva_sup')),
            ],
           'direito_ded',
           ['attribute' => 'iva_ded',
             'value'=>'iva_ded',
             'footer'=>$formatter->asCurrency(Modelo106Fornecedor::totalItem($dataProviderFornecedor->models,'iva_ded')),
            ],
           'tipologia',
           'linha_dest_mod',
        ]
    ]); ?>

        </div>






        <div id="Tokyo" class="city">



            <table class="table">
                <thead>
                    <tr>
                        <th class="td-number"> I </th>
                        <td colspan="5" style="text-align: left;"> Zona para Validação Mecânica</td>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                        <th colspan="2" style="text-align: center;">
                            <p>MODELO 106</p>
                            <p>IMPOSTO SOBRE O VALOR ACRESCENTADO</p>
                        </th>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <th class="td-number">II</th>
                        <th></th>
                        <th class="td-number">III</th>
                        <td style="width: 280px;">TIPO DE DECLARAÇÃO / ANEXOS</td>
                        <th class="td-number">IV</th>
                        <td>NÚMERO DE IDENTIFICAÇÃO FISCAL</td>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align: center;">
                            <p>MODELO 106</p>
                            <p>IMPOSTO SOBRE O VALOR ACRESCENTADO</p>
                        </th>
                        <th colspan="2" style="text-align: center;">
                            <p>Clientes</p>
                            <p>Fornecedores</p>
                        </th>
                        <th colspan="2" style="text-align: center;">
                            <p>2 5 9 0 7 7 2 7 5</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="td-number">V</td>
                        <td>PERÍODO A QUE RESPEITA A DECLARAÇÃO</td>
                        <td colspan="1"></td>
                        <td colspan="1"></td>
                        <td class="td-number">VI</td>
                        <td>REPARTIÇÃO DE FINANÇAS COMPETENTE</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Ano:</td>
                        <td> Mês:</td>
                        <td></td>
                        <td>Código:</td>
                        <td>Descrição:</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>2 0 1 8:</td>
                        <td> 1 2 Dezembro</td>
                        <td></td>
                        <td>2 2 3</td>
                        <td>Praia:</td>
                    </tr>




                    <tr>
                        <td class="td-number">VII</td>
                        <td colspan="5" style="text-align: center;">NOME, DESIGNAÇÃO SOCIAL DO SUJEITO PASSIVO E DO SEU
                            REPRESENTANTE LEGAL</td>
                    </tr>
                    <tr>
                        <td colspan="2">NOME/DESIGNAÇÃO SOCIAL:</td>
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td colspan="2">REPRESENTANTE LEGAL:</td>
                        <td colspan="2"></td>
                        <td colspan="2">NIF:</td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <p style="font-size: 9px;">Caso tenha alterações a efectuar ao seu Cadastro, assinale aqui
                            </p>
                        </td>

                        <td colspan="2">e registe no campo OBSERVAÇÕES:</td>
                    </tr>




                    <tr>
                        <td class="td-number">VIII</td>
                        <td colspan="5" style="text-align: center;">EXISTÊNCIA DE OPERAÇÕES</td>
                    </tr>
                    <tr>
                        <td colspan="3">Se no período não realizou operações ativas nem passivas, assinale aqui</td>
                        <td colspan="1"></td>
                        <td colspan="2">e passe para o quadro XIII</td>
                    </tr>
                    <tr>
                        <td colspan="3">Se realizou uma única operação tributavel e pela 1ª vez, assinale aqui</td>
                        <td colspan="1"></td>
                        <td colspan="2">e passe para o quadro IX</td>
                    </tr>




                </tbody>

            </table>









            <table class="table">
                <thead>
                    <tr>
                        <td class="td-number">IX</td>
                        <td colspan="7" style="text-align: center;">APURAMENTO DO IMPOSTO RESPEITANTE AO PERÍODO A QUE
                            RESPEITA A DECLARAÇÃO</td>
                    </tr>
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
                        <td>Operações em que liquidou o IVA nos termos do Decreto - Lei nº 16/2004 de 20 de Maio (valor
                            recebido)</td>
                        <td class="td-number">05</td>
                        <td><?=$formatter->asCurrency($model->valor_5)?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="td-number">06</td>
                        <td><?=$model->valor_6?></td>
                    </tr>


                    <tr>
                        <td>3</td>
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
                        <td>5</td>
                        <td>Aquisições dos serviços fornecidos por um prestador que não tenha sede, estabelecimento
                            estável ou domicílio em Cabo Verde, cujo imposto foi liquidado pelo declarante (art. nº6,
                            nº5 e 6 do RIVA)</td>
                        <td class="td-number">11</td>
                        <td><?=$formatter->asCurrency($model->valor_11)?></td>
                        <td class="td-number">12</td>
                        <td><?=$formatter->asCurrency($model->valor_12)?></td>
                        <td class="td-number">13</td>
                        <td><?=$formatter->asCurrency($model->valor_13)?></td>
                    </tr>

                    <tr>
                        <td>6</td>
                        <td>Aquisição dos serviços efetuados nos termos do art. 2º, alínea f) do RIVA - Construção
                            Civil, em que o IVA foi liquidado pelo declarante.</td>
                        <td class="td-number">14</td>
                        <td><?=$formatter->asCurrency($model->valor_14)?></td>
                        <td class="td-number">15</td>
                        <td><?=$formatter->asCurrency($model->valor_15)?></td>
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
                        <td><?=$formatter->asCurrency($model->valor_18)?></td>
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
                        <td><?=$formatter->asCurrency($model->valor_25)?></td>
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
                        <td><?=$formatter->asCurrency($model->valor_31)?></td>
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
                        <td>Se o valor inscrito no campo 34 é superior ao de campo 33 (caso se aplique, multiplicar pelo
                            campo 31), campo 35=34-33 </td>
                        <td class="td-number">35</td>
                        <td><?=$formatter->asCurrency($model->valor_35) ?></td>
                    </tr>
                    <tr>
                        <td>Se o valor inscrito no campo 33 (caso se aplique, multiplicar pelo campo 31), é superior ao
                            campo 34, campo 36=33-34
                        </td>
                        <td class="td-number">36</td>
                        <td><?=$formatter->asCurrency($model->valor_36) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">UTILIZAÇÃO DE CRÉDITOS DE PERÍODOS ANTERIORES: o campo 37 só pederá ser
                            preenchido se Declaração apresentada dentro no prazo legal</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Excesso a reportar dos períodos anteriores</td>
                        <td class="td-number">37</td>
                        <td><?=$formatter->asCurrency($model->valor_37) ?></td>
                    </tr>
                    <tr>
                        <th>1-ENTIDADE COMPETENTE:</th>
                        <th colspan="5" class="td-form"></th>
                    </tr>
                    <tr>
                        <th colspan="4"></th>
                        <th class="td-number">38</th>
                        <th><?=$formatter->asCurrency($model->valor_38) ?></th>
                    </tr>
                </tbody>
            </table>



            <table class="table">
                <thead>
                    <tr>
                        <td class="td-number"> XI </td>
                        <td colspan="4"> IMPOSTO A RECUPERAR</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" style="text-align: center;">SE ESTA DECLARAÇÃO FOR APRESENTADA DENTRO DE PRAZO
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"> CRÉDITO DE IMPOSTO </td>
                        <td> 1-REPORTE PARA O PERÍODO SEGUINTE:</td>
                        <td class="td-number">40</td>
                        <td> <?=$formatter->asCurrency($model->valor_40)?></td>
                    </tr>

                    <tr>
                        <td class="td-number">39</td>
                        <td><?=$formatter->asCurrency($model->valor_39)?></td>
                        <td>2.PEDIDO DE REEMBOLSO:</td>
                        <td class="td-number">41</td>
                        <td><?=$formatter->asCurrency($model->valor_41) ?></td>
                    </tr>
                    <tr>
                        <td>(39=36+37)</td>
                        <td colspan="4"></td>
                    <tr>
                        <td></td>
                        <td colspan="4">
                            <p style="font-size: 9px;">Se esta declaração for apresentada fora do prazo legal, por culpa
                                de contribuinte, este quadro náo poderá ser preenchido.
                                Os pedidos de reembolso deverão observar as disposições legais aplicáveis (Artigo 21º do
                                RIVA, bem como decreto-Lei n.º 65/2003, de 31 de Dezembro.
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>






            <table class="table">
                <thead>
                    <tr>
                        <td class="td-number"> XII </td>
                        <th colspan="3" style="text-align: center;"> DESENVOLVIMENTO DO QUADRO XI</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <th></th>
                        <th colspan="3" style="text-align: left;">A-Valores de base tributável inscritos nos campos 01 e
                            03</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Adiantamento Transmições de bens e prestações de serviços tributadas </td>
                        <td class="td-number">42</td>
                        <td> <?=$formatter->asCurrency($model->valor_42)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Amostras e ofertas para além do limite geral </td>
                        <td class="td-number">43</td>
                        <td> <?=$formatter->asCurrency($model->valor_43)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Operações sujeitas a tributação da margem </td>
                        <td class="td-number">44</td>
                        <td> <?=$formatter->asCurrency($model->valor_43)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Operações efectuadas ao abrigo das alíneas e) e f) do nº 3 do art.3º e do nº 2 do art. 4º
                            do RIVA </td>
                        <td class="td-number">42</td>
                        <td> <?=$formatter->asCurrency($model->valor_45)?></td>
                    </tr>



                    <tr>
                        <th></th>
                        <th colspan="3" style="text-align: left;">B-Valores de base tributável inscritos no campo 08
                        </th>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Operações destinadas à exportação </td>
                        <td class="td-number">46</td>
                        <td> <?=$formatter->asCurrency($model->valor_43)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Operações efectuadas nos termos do Decreto-Lei 88/2005 de 26 de Dez. </td>
                        <td class="td-number">47</td>
                        <td> <?=$formatter->asCurrency($model->valor_47)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Bens da lista anexada </td>
                        <td class="td-number">48</td>
                        <td> <?=$formatter->asCurrency($model->valor_48)?></td>
                    </tr>




                    <tr>
                        <th></th>
                        <th colspan="3" style="text-align: left;">C-Operações efectuadas no termo Decreto-Lei nº16/2004,
                            de 20 de Maio</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Faturas de prestação de serviços emitidas(Valor Faturado) </td>
                        <td class="td-number">49</td>
                        <td> <?=$formatter->asCurrency($model->valor_49)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> Recibos de prestação de serviços faturados(Valor Recebido) </td>
                        <td class="td-number">50</td>
                        <td> <?=$formatter->asCurrency($model->valor_50)?></td>
                    </tr>

                    </tr>
                </tbody>
            </table>






            <table class="table">
                <thead>
                    <tr>
                        <td class="td-number"> XIII </td>
                        <th colspan="2" style="text-align: center;"> APRESENTAÇÃO DA DECLARAÇÃO</th>
                        <th></th>
                        <td class="td-number"> XIV </td>
                        <th colspan="2" style="text-align: center;"> IDENTIFICAÇÃO DO TÉCNICO OFICIAL DE CONTAS</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td colspan="3" rowspan="2" style="text-align: center;">
                            <p style="text-align: left;">A PRESENTE DECLARAÇÃO É VERDADEIRA E NÃO OMITE QUALQUER
                                INFORMAÇÃO RELEVANTE:<p>
                                    <p>DATA:0 7 / 0 1 / 2 0 1 9 LOCAL: Praia</p>
                                    <p>ASSINATURA DO SUJ.PASSIVO/REPRESENTANTE:</p>
                        </td>
                        <th></th>
                        <td colspan="3" style="text-align: left;">
                            <p>NOME: Julio Cesar Morais e Cruz</p>
                            <p>NIF: 112404740</p>
                            <p>Nº ORDEM TOC: 0223</p>
                            /
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td colspan="3" style="text-align: center;">
                            <p>DATA: __ __ / __ __ / __ __ __ __</p>
                            <p>ASSINATURA DO RECETOR:</p>
                            <p>________________________________________________________</p>
                        </td>
                    </tr>

                    </tr>
                </tbody>
            </table>



            <table class="table">
                <thead>
                    <tr>
                        <th colspan="4" style="text-align: left;"> OBSERVAÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">.</td>
                    </tr>
                    <tr>
                        <td colspan="4">.</td>
                    </tr>
                    <tr>
                        <td colspan="4">.</td>
                    </tr>
                    <tr>
                        <td colspan="4">.</td>
                    </tr>
                    <tr>
                        <td colspan="4">.</td>
                    </tr>
                    <tr>
                        <td colspan="4">.</td>
                    </tr>
                </tbody>

            </table>




        </div>
    </div>




</section>






<script type="text/javascript">
function openCity(cityName) {
    var i;
    var x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    document.getElementById(cityName).style.display = "block";
}
</script>
