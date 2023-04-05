<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\web\JsExpression;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\Nord;
use app\modules\dsp\models\Item;
use kartik\depdrop\DepDrop;

$this->title = 'Nova fatura definitiva normal';

// $this->registerJsFile(Url::to('@web/js/formFaturaDefenitivaParceal.js'),['position' => \yii\web\View::POS_END]);

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Nova fatura definitiva normal';
?>
<section>
    <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning', 'title' => 'VOLTAR']) ?>
    <div class="titulo-principal">
        <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>

    <div class="row">

        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'validateOnChange' => true,
            'validateOnBlur' => false,
            'options' => [
                'enctype' => 'multipart/form-data',
                'id' => 'dynamic-form'
            ]
        ]); ?>

        <div class="row">


            <div class="col-md-4">
                <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => 'Selecione ...', 'id' => 'dsp_processo_id'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['/dsp/processo/list-processo-fd']),
                            'dataType' => 'json',
                        ],
                    ],

                ]); ?>
            </div>
            <div class="col-md-2">
                <?php echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_INPUT,
                    'removeButton' => ['icon' => 'trash'],
                    'pickerButton' => true,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ]);
                ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'fin_fatura_provisoria_id')->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'options' => ['id' => 'fin_fatura_provisoria_id', 'multiple' => true, 'onchange' => 'updateForm(this)'],
                    'pluginOptions' => [
                        'depends' => ['dsp_processo_id'],
                        'allowClear' => true,
                        'placeholder' => 'Select...',

                        'url' => Url::to(['/fin/fatura-definitiva/list-fatura-provisoria'])
                    ]
                ]); ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'data' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
                        'options' => ['placeholder' => 'Selecione cliente ...', 'id' => 'dsp_person_id'],
                        'pluginOptions' => [
                            'allowClear' => false,
                            /*'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/dsp/person/person-list']),
                'dataType' => 'json',
            ],*/
                        ],

                    ]); ?>
                </div>
                <div class="col-md-3" id="div-nord">
                    <?= $form->field($model, 'nord')->textInput(['id' => 'nord', 'readonly' => false]) ?>
                </div>


                <div class="col-md-3" id="div-n_registo">
                    <?= $form->field($model, 'n_registo')->textInput(['id' => 'n_registo', 'readonly' => false]) ?>
                </div>

                <div class="col-md-3" id="div-data_registo">
                    <?php echo $form->field($model, 'data_registo')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['id' => 'data_registo'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                    ]);
                    ?>
                </div>

                <div class="col-md-3" id="div-n_receita">
                    <?= $form->field($model, 'n_receita')->textInput(['id' => 'n_receita', 'readonly' => false]) ?>
                </div>

                <div class="col-md-3" id="div-data_receita">
                    <?php echo $form->field($model, 'data_receita')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['id' => 'data_receita'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                    ]);
                    ?>
                </div>

                <div class="col-md-3" id="div-impresso_principal">
                    <?= $form->field($model, 'impresso_principal')->textInput(['id' => 'impresso_principal', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-impresso_intercalar">
                    <?= $form->field($model, 'impresso_intercalar')->textInput(['id' => 'impresso_intercalar', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-pl">
                    <?= $form->field($model, 'pl')->textInput(['id' => 'pl', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-gti">
                    <?= $form->field($model, 'gti')->textInput(['id' => 'gti', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-tce">
                    <?= $form->field($model, 'tce')->textInput(['id' => 'tce', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-tn">
                    <?= $form->field($model, 'tn')->textInput(['id' => 'tn', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-form">
                    <?= $form->field($model, 'form')->textInput(['id' => 'form', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-regime_normal">
                    <?= $form->field($model, 'regime_normal')->textInput(['id' => 'regime_normal', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-regime_especial">
                    <?= $form->field($model, 'regime_especial')->textInput(['id' => 'regime_especial', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-exprevio_comercial">
                    <?= $form->field($model, 'exprevio_comercial')->textInput(['id' => 'exprevio_comercial', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-expedente_matricula">
                    <?= $form->field($model, 'expedente_matricula')->textInput(['id' => 'expedente_matricula', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-taxa_comunicaco">
                    <?= $form->field($model, 'taxa_comunicaco')->textInput(['id' => 'taxa_comunicaco', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-dv">
                    <?= $form->field($model, 'dv')->textInput(['id' => 'dv', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-fotocopias">
                    <?= $form->field($model, 'fotocopias')->textInput(['id' => 'fotocopias', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-qt_estampilhas">
                    <?= $form->field($model, 'qt_estampilhas')->textInput(['id' => 'qt_estampilhas', 'readonly' => false]) ?>
                </div>

                <div class="col-md-3" id="div-acrescimo">
                    <?= $form->field($model, 'acrescimo')->textInput(['id' => 'acrescimo', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-posicao_tabela">
                    <?= $form->field($model, 'posicao_tabela')->textInput(['id' => 'posicao_tabela', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-dsp_regime_item_valor">
                    <?= $form->field($model, 'dsp_regime_item_valor')->textInput(['id' => 'dsp_regime_item_valor', 'readonly' => false]) ?>
                </div>
                <div class="col-md-3" id="div-dsp_regime_item_tabela_anexa_valor">
                    <?= $form->field($model, 'dsp_regime_item_tabela_anexa_valor')->textInput(['id' => 'dsp_regime_item_tabela_anexa_valor', 'readonly' => false]) ?>
                </div>

            </div>




            <?= $form->field($model, 'descricao')->textArea(['id' => 'descricao']) ?>


            <div class="titulo-principal">
                <h5 class="titulo">DETALHES DA FATURA</h5>
                <div id="linha-longa">
                    <div id="linha-curta"></div>
                </div>
            </div>
            <table style="margin-bottom: -5px" class=" table table-hover">
                <tr>
                    <th style="padding: 10px">Item</th>
                    <th style="padding: 10px;width: 250px">Valor</th>
                </tr>
            </table>

            <div class="row">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 30, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelsFaturaDefinitivaItem[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'dsp_item_id',
                        'valor',
                        'ab',
                    ],
                ]); ?>

                <input id="item-control" type="hidden" name="_method" value=0 />

                <table style="margin-bottom: -5px" class="container-items table table-hover">


                    <?php foreach ($modelsFaturaDefinitivaItem as $i => $item) : ?>




                    <tr class="item">

                        <td>
                            <button style="display: none;" type="button" id="removeitem"
                                class="remove-item btn btn-danger btn-xs"><i
                                    class="glyphicon glyphicon-minus"></i></button>
                            <?php
                                // necessary for update action.
                                if (!$item->isNewRecord) {
                                    echo Html::activeHiddenInput($item, "[{$i}]id");
                                }
                                ?>
                        </td>
                        <td>
                            <?= $form->field($item, "[{$i}]dsp_item_id")->dropDownList(
                                    ArrayHelper::map(Item::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                                    ['prompt' => 'Selecione ...']
                                )->label(false); ?>


                        </td>
                        <td style="width: 250px">
                            <?= $form->field($item, "[{$i}]valor")->textInput(['readonly' => true])->label(false) ?>
                        </td>
                    </tr>

                    <?php endforeach; ?>

                </table>
                <button style="display: none;" id="add-item" value=<?= $i ?> type="button"
                    class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                <?= Html::hiddenInput('hidemCount', $i, ['id' => 'hidemCount', 'class' => 'form-control']); ?>
                <?php DynamicFormWidget::end(); ?>
            </div>
            <table class=" table table-hover">
                <tr>
                    <th style="padding: 10px">TOTAL</th>

                    <th style="width: 250px">
                        <?= $form->field($model, 'fin_totoal_fp')->hiddenInput(['id' => 'fin_totoal_fp', 'readonly' => false])->label(false) ?>
                        <?= $form->field($model, 'valor')->textInput(['id' => 'total_valor', 'readonly' => true])->label(false) ?>
                    </th>
                </tr>
            </table>











            <div class="form-group" id="save">
                <?= Html::submitButton(Yii::$app->ImgButton->Img('save') . ' Salvar', [
                    'class' => 'btn btn-success',
                    'title' => 'SALVAR',
                    'data' => [
                        'confirm' => 'PRETENDE REALENTE SUBMETER O FORMULÁRIO?',
                    ]
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
</section>

<script>
function updateTotal() {
    var total_valor = 0;
    for (var i = 0; i <= $('#hidemCount').val(); i++) {
        if ($("#faturadefinitivaitem-" + i + "-valor").val() > 0) {
            var total_valor = parseFloat(total_valor) + parseFloat($("#faturadefinitivaitem-" + i + "-valor").val());
        }
    }
    $("#total_valor").val(total_valor);
}
window.setInterval(updateTotal, 1000);






function updateForm(obj) {
    var dsp_processo_id = $("#dsp_processo_id").val();
    var fin_fatura_provisoria_id = $("#fin_fatura_provisoria_id").val();

    if (!isEmpty(dsp_processo_id) && !isEmpty(fin_fatura_provisoria_id)) {
        $.blockUI({
            message: '<img src="<?= Url::to('@web/loader.gif') ?>" />'
        });
        addItem(dsp_processo_id);
    }


    function isEmpty(val) {
        return (val === undefined || val == null || val.length <= 0) ? true : false;
    }


    function clerarItemsAll() {
        for (var i = 0; i <= 25; i++) {
            for (var j = 0; j <= 25; j++) {
                $("#faturadefinitivaitem-" + i + "-dsp_item_id").val(null);
                $("#faturadefinitivaitem-" + i + "-valor").val(null);
                if (document.getElementById("removeitem-" + i + '-' + j)) {
                    document.getElementById("removeitem-" + i + '-' + j).click();
                }
                if (document.getElementById("removeitem-" + i)) {
                    document.getElementById("removeitem-" + i).click();
                }
                if (document.getElementById("removeitem-" + i + j)) {
                    document.getElementById("removeitem-" + i + j).click();
                }
                if (document.getElementById("removeitem" + i)) {
                    document.getElementById("removeitem" + i).click();
                }
                if (document.getElementById("removeitem" + i + j)) {
                    document.getElementById("removeitem" + i + j).click();
                }
            }
        }
    }

    function clerarItems() {
        for (var i = 0; i <= 25; i++) {
            for (var j = 0; j <= 25; j++) {
                if ($("#faturadefinitivaitem-" + i + "-valor").val() <= 0) {
                    if (document.getElementById("removeitem-" + i + '-' + j)) {
                        document.getElementById("removeitem-" + i + '-' + j).click();
                    }
                    if (document.getElementById("removeitem-" + i)) {
                        document.getElementById("removeitem-" + i).click();
                    }
                    if (document.getElementById("removeitem-" + i + j)) {
                        document.getElementById("removeitem-" + i + j).click();
                    }
                    if (document.getElementById("removeitem" + i)) {
                        document.getElementById("removeitem" + i).click();
                    }
                    if (document.getElementById("removeitem" + i + j)) {
                        document.getElementById("removeitem" + i + j).click();
                    }
                }
            }
        }
    }



    function addItem(dsp_processo_id) {
        document.getElementById("add-item").click();
        clerarItemsAll();
        $.getJSON("/fin/fatura-definitiva/get-items-parceal", {
            id: dsp_processo_id,
            fin_fatura_provisoria_id: fin_fatura_provisoria_id
        }, function(data) {
            if (data) {
                if (data.error) {
                    $.unblockUI();
                    swal({
                        title: "Processo incompleto.",
                        text: data.error
                    });
                } else {
                    $('#descricao').val(data.descricao);
                    $("#dsp_person_id").empty().append('<option value="' + data.dsp_person_id + '">' + data
                        .dsp_person_nome + '</option>').val(data.dsp_person_id).trigger('change');

                    if (data.nord != null) {
                        $("#nord").val(data.nord);
                        document.getElementById("div-nord").style.display = "block";
                    } else {
                        document.getElementById('div-nord').style.display = "none";
                    }
                    if (data.n_registo > 0) {
                        $("#n_registo").val(data.n_registo);
                        document.getElementById("div-n_registo").style.display = "block";
                    } else {
                        document.getElementById('div-n_registo').style.display = "none";
                    }
                    if (data.data_registo != null) {
                        $("#data_registo").val(data.data_registo);
                        document.getElementById("div-data_registo").style.display = "block";
                    } else {
                        document.getElementById('div-data_registo').style.display = "none";
                    }
                    if (data.n_receita > 0) {
                        $("#n_receita").val(data.n_receita);
                        document.getElementById("div-n_receita").style.display = "block";
                    } else {
                        document.getElementById('div-n_receita').style.display = "none";
                    }
                    if (data.data_receita != null) {
                        $("#data_receita").val(data.data_receita);
                        document.getElementById("div-data_receita").style.display = "block";
                    } else {
                        document.getElementById('div-data_receita').style.display = "none";
                    }

                    if (data.impresso_principal > 0) {
                        $("#impresso_principal").val(data.impresso_principal);
                        document.getElementById("div-impresso_principal").style.display = "block";
                    } else {
                        document.getElementById('div-impresso_principal').style.display = "none";
                    }
                    if (data.taxa_comunicaco > 0) {
                        $("#taxa_comunicaco").val(data.taxa_comunicaco);
                        document.getElementById("div-taxa_comunicaco").style.display = "block";
                    } else {
                        document.getElementById('div-taxa_comunicaco').style.display = "none";
                    }
                    if (data.impresso_intercalar > 0) {
                        $("#impresso_intercalar").val(data.impresso_intercalar);
                        document.getElementById("div-impresso_intercalar").style.display = "block";
                    } else {
                        document.getElementById('div-impresso_intercalar').style.display = "none";
                    }
                    if (data.dv > 0) {
                        $("#dv").val(data.dv);
                        document.getElementById("div-dv").style.display = "block";
                    } else {
                        document.getElementById('div-dv').style.display = "none";
                    }
                    if (data.tce > 0) {
                        $("#tce").val(data.tce);
                        document.getElementById("div-tce").style.display = "block";
                    } else {
                        document.getElementById("div-tce").style.display = "none";
                    }
                    if (data.pl > 0) {
                        $("#pl").val(data.pl);
                        document.getElementById("div-pl").style.display = "block";
                    } else {
                        document.getElementById("div-pl").style.display = "none";
                    }
                    if (data.tn > 0) {
                        $("#tn").val(data.tn);
                        document.getElementById("div-tn").style.display = "block";
                    } else {
                        document.getElementById("div-tn").style.display = "none";
                    }
                    if (data.gti > 0) {
                        $("#gti").val(data.gti);
                        document.getElementById("div-gti").style.display = "block";
                    } else {
                        document.getElementById("div-gti").style.display = "none";
                    }
                    if (data.fotocopias > 0) {
                        $("#fotocopias").val(data.fotocopias);
                        document.getElementById("div-fotocopias").style.display = "block";
                    } else {
                        document.getElementById("div-fotocopias").style.display = "none";
                    }
                    if (data.qt_estampilhas > 0) {
                        $("#qt_estampilhas").val(data.qt_estampilhas);
                        document.getElementById("div-qt_estampilhas").style.display = "block";
                    } else {
                        document.getElementById("div-qt_estampilhas").style.display = "none";
                    }
                    if (data.form > 0) {
                        $("#form").val(data.form);
                        document.getElementById("div-form").style.display = "block";
                    } else {
                        document.getElementById("div-form").style.display = "none";
                    }
                    if (data.regime_normal > 0) {
                        $("#regime_normal").val(data.regime_normal);
                        document.getElementById("div-regime_normal").style.display = "block";
                    } else {
                        document.getElementById("div-regime_normal").style.display = "none";
                    }
                    if (data.regime_especial > 0) {
                        $("#regime_especial").val(data.regime_especial);
                        document.getElementById("div-regime_especial").style.display = "block";
                    } else {
                        document.getElementById("div-regime_especial").style.display = "none";
                    }
                    if (data.exprevio_comercial > 0) {
                        $("#exprevio_comercial").val(data.exprevio_comercial);
                        document.getElementById("div-exprevio_comercial").style.display = "block";
                    } else {
                        document.getElementById("div-exprevio_comercial").style.display = "none";
                    }
                    if (data.expedente_matricula > 0) {
                        $("#expedente_matricula").val(data.expedente_matricula);
                        document.getElementById("div-expedente_matricula").style.display = "block";
                    } else {
                        document.getElementById("div-expedente_matricula").style.display = "none";
                    }
                    if (data.acrescimo > 0) {
                        $("#acrescimo").val(data.acrescimo);
                        document.getElementById("div-acrescimo").style.display = "block";
                    } else {
                        document.getElementById("div-acrescimo").style.display = "none";
                    }
                    $("#posicao_tabela").val(data.posicao_tabela);
                    $("#dsp_regime_item_valor").val(data.dsp_regime_item_valor);
                    $("#dsp_regime_item_tabela_anexa_valor").val(data.dsp_regime_item_tabela_anexa_valor);
                    $("#fin_totoal_fp").val(data.fin_totoal_fp);

                    clerarItemsAll();

                    $.each(data.item, function(key, Items) {
                        if (Items.valor > 0) {
                            jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
                                $("#faturadefinitivaitem-" + item.rowIndex + "-dsp_item_id")
                                    .val(Items.dsp_item_id).attr("selected", "selected");
                                $("#faturadefinitivaitem-" + item.rowIndex + "-valor").val(Items
                                    .valor);
                                $("#hidemCount").val(item.rowIndex);
                            });
                            var addItem = document.getElementById("add-item").click();
                        }
                        Items.dsp_item_id = null;
                        Items.valor = null;
                    });



                }



                $.getJSON("/fin/fatura-definitiva/get-items-parceal", {
                    id: dsp_processo_id,
                    fin_fatura_provisoria_id: fin_fatura_provisoria_id
                }, function(data) {
                    if (data.isAdicional == 1) {
                        if (confirm(
                                "ESTE PROCESSO JÁ TEM UMA FATAURA DEFINITIVA PRETENDE CRIAR UMA FATURA DEFINITIVA ADICONAL?"
                            )) {
                            document.getElementById("save").style.display = "block";
                        } else {
                            document.getElementById("save").style.display = 'none';
                        }
                    }
                    if (data.avisoCredito > 0) {
                        if (confirm("SERA GERADO UM AVISO DE CREDITO NO VALOR DE  " + data
                                .avisoCredito)) {
                            document.getElementById("save").style.display = "block";
                        } else {
                            document.getElementById("save").style.display = 'none';
                        }
                    }

                });


            }

            clerarItems();
            $.unblockUI();

        });

    }
}
</script>
