<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\Item;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Novo fatura definitava especial';
?>
<section>
    <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning', 'title' => 'VOLTAR']) ?>
    <div class="titulo-principal">
        <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>
    <div class="desciplina-perfil-form">

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
        <?= $form->errorSummary($model); ?>
        <div class="row">
            <div class="row">


                <div class="col-md-3">
                    <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => ' ', 'id' => 'dsp_processo_id', 'onchange' => 'getNordData(this)'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => Url::to(['/dsp/processo/dropdow-list']),
                                'dataType' => 'json',
                            ],
                        ],
                    ]); ?>
                </div>
                <div class="col-md-3">
                    <?php echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['id' => 'data', 'onchange' => 'updateFormNumero(this)'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                    ]);
                    ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'data' => ArrayHelper::map(Person::find()->where(['is_cliente' => 1])->orderBy('nome')->all(), 'id', 'nome'),
                        'options' => ['placeholder' => 'Selecione cliente ...', 'id' => 'dsp_person_id'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            /*'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/dsp/person/person-list']),
                'dataType' => 'json',
            ],*/
                        ],

                    ]); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'nord')->widget(Select2::classname(), [
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => 'Selecione N.O.R.D ...', 'id' => 'nord'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => Url::to(['/dsp/nord/ajax']),
                                'dataType' => 'json',
                            ],
                        ],

                    ]); ?>
                </div>
            </div>

            <div class="row">

                <?= $form->field($model, 'descricao')->textArea(['id' => 'mercadoria', 'placeholder' => 'Descrição / Mrecadoria', 'maxlength' => true]) ?>
            </div>
            <div class="row">
                <div class="titulo-principal">
                    <h5 class="titulo">Regime</h5>
                    <div id="linha-longa">
                        <div id="linha-curta"></div>
                    </div>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'dsp_regime_id')->textInput(['id' => 'dsp_regime_id']) ?>

                </div>
                <div class="col-md-10">
                    <?= $form->field($model, 'dsp_regime_descricao')->textInput(['id' => 'dsp_regime_descricao']) ?>

                </div>
            </div>



            <div class="row">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'dsp_regime_item_id')->widget(DepDrop::classname(), [
                            'type' => DepDrop::TYPE_SELECT2,
                            'options' => ['id' => 'dsp_regime_item_id', 'onchange' => 'getRegimeData(this)'],
                            //'data'=>ArrayHelper::map(RegimeItem::find()->all(), 'id', 'descricao'),
                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions' => [
                                'depends' => ['dsp_processo_id', 'dsp_regime_id'],
                                'placeholder' => 'Selecione ...',
                                'url' => Url::to(['/dsp/regime/regime-item']),
                                'initialize' => true,

                            ]
                        ]);
                        ?>

                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'dsp_regime_item_valor')->textInput(['id' => 'dsp_regime_item_valor']) ?>

                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <?= $form->field($model, 'dsp_regime_item_tabela_anexa')->widget(DepDrop::classname(), [
                            'type' => DepDrop::TYPE_SELECT2,
                            'options' => ['id' => 'dsp_regime_item_tabela_anexa', 'onchange' => 'getRegimeData(this)'],
                            //'data'=>ArrayHelper::map(RegimeItem::find()->all(), 'id', 'descricao'),
                            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            'pluginOptions' => [
                                'depends' => ['dsp_regime_item_id'],
                                'placeholder' => 'Selecione ...',
                                'url' => Url::to(['/dsp/regime/regime-item-table']),
                                'initialize' => true,

                            ]
                        ]);
                        ?>

                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'dsp_regime_item_tabela_anexa_valor')->textInput(['id' => 'dsp_regime_item_tabela_anexa_valor']) ?>

                    </div>
                </div>
            </div>


            <div class="row">
                <div class="titulo-principal">
                    <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
                    <div id="linha-longa">
                        <div id="linha-curta"></div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-2">

                        <?= $form->field($model, 'impresso_principal')->textInput(['id' => 'impresso_principal', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'impresso_intercalar')->textInput(['id' => 'impresso_intercalar', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'pl')->textInput(['id' => 'pl', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'gti')->textInput(['id' => 'gti', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'dv')->textInput(['id' => 'dv', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'tce')->textInput(['id' => 'tce', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">

                        <?= $form->field($model, 'tn')->textInput(['id' => 'tn', 'onchange' => 'updateFormTn(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'fotocopias')->textInput(['id' => 'fotocopias', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'form')->textInput(['id' => 'form', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>

                    <div class="col-md-2">
                        <?= $form->field($model, 'regime_normal')->textInput(['id' => 'regime_normal', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'regime_especial')->textInput(['id' => 'regime_especial', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'impresso_intercalar')->textInput(['id' => 'impresso_intercalar', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'exprevio_comercial')->textInput(['id' => 'exprevio_comercial', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'expedente_matricula')->textInput(['id' => 'expedente_matricula', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'taxa_comunicaco')->textInput(['id' => 'taxa_comunicaco', 'onchange' => 'getRegimeData(this)']) ?>
                    </div>
                </div>
            </div>




            <div class="titulo-principal">
                <h5 class="titulo">Taxas</h5>
                <div id="linha-longa">
                    <div id="linha-curta"></div>
                </div>
            </div>
            <table class="container-items table table-hover">
                <thead>
                    <tr>
                        <th scope="col" style="width: 40px">#</th>
                        <th scope="col" style="text-align: left;">ITEM</th>
                        <th style="width: 250px" scope="col">VALOR</th>
                        <th style="width: 50px" scope="col">OR.</th>
                    </tr>
                </thead>
            </table>

            <div class="row">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 50, // the maximum times, an element can be cloned (default 999)
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

                <table class="container-items table table-hover">


                    <?php foreach ($modelsFaturaDefinitivaItem as $i => $item) : ?>
                    <?php if (empty($item->item_origem_id)) {
                            $item->item_origem_id = 'M';
                        } ?>
                    <?php
                        // necessary for update action.
                        if (!$item->isNewRecord) {
                            echo Html::activeHiddenInput($item, "[{$i}]id");
                        }
                        ?>

                    <tr class="item">

                        <td style="width: 50px">
                            <button type="button" id="removeitem" class="remove-item btn btn-danger btn-xs"><i
                                    class="glyphicon glyphicon-minus"></i></button>

                        </td>
                        <td>
                            <?= $form->field($item, "[{$i}]dsp_item_id")->dropDownList(
                                    ArrayHelper::map(Item::find()->where(['dsp_item_type_id' => 1])->orderBy('descricao')->all(), 'id', 'descricao'),
                                    ['prompt' => 'Selecione ...']
                                )->label(false); ?>


                        </td>
                        <td style="width: 250px">
                            <?= $form->field($item, "[{$i}]valor")->textInput(['maxlength' => true])->label(false) ?>
                        </td>
                        <td style="width: 50px">
                            <?= $form->field($item, "[{$i}]item_origem_id")->textInput(['readonly' => false])->label(false) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                </table>
                <button id="add-item" onclick="addNullItem();" value=<?= $i ?> type="button"
                    class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                <?= Html::hiddenInput('hidemCount', $i, ['id' => 'hidemCount', 'class' => 'form-control']); ?>
                <?php DynamicFormWidget::end(); ?>
            </div>


            <table class="container-items table table-hover">
                <thead>
                    <tr>
                        <th scope="col" style="width: 40px">#</th>
                        <th scope="col">TOTAL</th>
                        <th style="width: 250px" scope="col">
                            <?= $form->field($model, 'fin_totoal_fp')->hiddenInput(['id' => 'fin_totoal_fp', 'readonly' => false])->label(false) ?>
                            <?= $form->field($model, 'valor')->textInput(['id' => 'total', 'readonly' => true])->label(false) ?>
                        </th>
                        <th style="width: 50px" scope="col">##</th>
                    </tr>
                </thead>
            </table>


            <div class="form-group">
                <?= Html::submitButton(Yii::$app->ImgButton->Img('save') . ' Salvar', [
                    'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                    'title' => 'SALVAR',
                    'data' => [
                        'confirm' => 'PRETENDE REALENTE SUBMETER O FORMULÁRIO?',
                    ]
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

</section>


<script type="text/javascript">
function updateTotal() {
    var total = 0;
    var honorario = 0;
    for (var i = 0; i <= $('#hidemCount').val(); i++) {
        if ($("#faturadefinitivaitem-" + i + "-valor").val() > 0) {
            var total = parseFloat(total) + parseFloat($("#faturadefinitivaitem-" + i + "-valor").val());
        }
    }
    $("#total").val(total);
    $("#fin_totoal_fp").val(total);
}
window.setInterval(updateTotal, 10);
</script>

<script type="text/javascript">
var urlLoaderGif = "<?php echo Url::to(['@web/loader.gif']) ?>";
var urlDspNordGetData = "<?php echo Url::to(['/dsp/nord/get-data']) ?>";
var urlDspNordGetItem = "<?php echo Url::to(['/dsp/nord/get-item']) ?>";
var urlDspRegimeGetRegimeData = "<?php echo Url::to(['/dsp/regime/get-regime-data']) ?>";
var urlAppDocumentoGetNumber = "<?php echo Url::to(['/documento/get-number']) ?>";

function getNordData(obj) {
    var x = obj.id.split("-");
    var dsp_processo_id = $("#dsp_processo_id").val();
    if (dsp_processo_id > 0) {
        $.blockUI({
            message: '<img src=' + urlLoaderGif + ' />'
        });

        $.getJSON(urlDspNordGetData, {
            id: dsp_processo_id
        }, function(data) {
            if (data) {
                addItem(dsp_processo_id);
                $("#dsp_regime_id").val(data.dsp_regime_id).attr("selected", "selected");
                $("#dsp_regime_descricao").val(data.dsp_regime_id + ' - ' + data.dsp_regime_descricao);
                $("#dsp_person_id").empty().append('<option value="' + data.dsp_person_id + '">' + data
                    .dsp_person_nome + '</option>').val(data.dsp_person_id).trigger('change');
                $("#nord").empty().append('<option value="' + data.nord + '">' + data.nord + '</option>').val(
                    data.nord).trigger('change');
                $("#mercadoria").val(data.mercadoria);
                $("#impresso_principal").val(data.impresso_principal);
                $("#impresso_intercalar").val(data.impresso_intercalar);
            } else {
                $.unblockUI();

            }
        });
    }

}

function addItem(dsp_processo_id) {
    for (var i = 0; i <= 25; i++) {
        for (var j = 0; j <= 25; j++) {
            if ($("#faturadefinitivaitem-" + i + "-item_origem_id").val() != 'M' || ($("#faturadefinitivaitem-" + i +
                    "-item_origem_id").val() == 'M' && ($("#faturadefinitivaitem-" + i + "-valor").val() <= 0))) {
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
                $("#faturadefinitivaitem-" + i + "-valor").val(null);
                $("#faturadefinitivaitem-" + i + "-dsp_item_id").val(null).attr("selected", "selected");
            }

        }
    }

    $.getJSON(urlDspNordGetItem, {
        id: dsp_processo_id
    }, function(data) {
        if (data) {
            $.each(data, function(key, Items) {
                if (Items.tax_amount > 0) {
                    jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
                        $("#faturadefinitivaitem-" + item.rowIndex + "-dsp_item_id").val(Items
                            .tax_code).attr("selected", "selected");
                        $("#faturadefinitivaitem-" + item.rowIndex + "-dsp_item_id").val(Items
                            .tax_code).attr("selected", "selected");
                        $("#faturadefinitivaitem-" + item.rowIndex + "-valor").val(Items
                            .tax_amount);
                        $("#faturadefinitivaitem-" + item.rowIndex + "-item_origem_id").val(
                            Items.item_origem_id);
                        $("#hidemCount").val(item.rowIndex);
                    });

                    var addItemss = document.getElementById("add-item").click();

                }
                Items.tax_code = null;
                Items.tax_amount = null;
                Items.item_origem_id = "M";
            });

            addImpressoItem();

            for (var i = 0; i <= 25; i++) {
                for (var j = 0; j <= 25; j++) {
                    if (($("#faturadefinitivaitem-" + i + "-valor").val() <= 0)) {
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

            $.unblockUI();

        }

    });

    // $.unblockUI();


}



function getRegimeData(obj) {
    var dsp_regime_item_id = $("#dsp_regime_item_id").val();
    var nord = $("#nord").val();
    var gti = $("#gti").val();
    var tri = $("#tri").val();
    var dv = $("#dv").val();
    var regime_especial = $("#regime_especial").val();
    var regime_normal = $("#regime_normal").val();
    var expedente_matricula = $("#expedente_matricula").val();
    var exprevio_comercial = $("#exprevio_comercial").val();
    var tce = $("#tce").val();
    var pl = $("#pl").val();
    var agenciamntos = 0;
    var honorario = 0;
    var id_tabela_anexa = $("#dsp_regime_item_tabela_anexa").val();
    var taxa_comunicaco = $("#taxa_comunicaco").val();

    if (nord != null && dsp_regime_item_id > 0) {
        $.blockUI({
            message: '<img src=' + urlLoaderGif + ' />'
        });

        $.getJSON(urlDspRegimeGetRegimeData, {
            id: dsp_regime_item_id,
            nord: nord,
            id_tabela_anexa: id_tabela_anexa
        }, function(data) {
            if (data) {

                $("#dsp_regime_item_tabela_anexa_valor").val(data.dsp_regime_item_tabela_anexa_valor);
                $("#dsp_regime_item_valor").val(data.dsp_regime_item_valor);
                if (data.isencao_honorario == 0) {

                    honorario = parseFloat(data.honorario);


                    if (data.Total_number_of_items > 0) {
                        agenciamntos = parseFloat(agenciamntos) + (parseFloat(data.Total_number_of_items *
                            100));
                    }
                    if (gti > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(gti * 100);
                    }
                    if (tri > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(tri * 200);
                    }
                    if (regime_especial > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(regime_especial * 1000);
                    }
                    if (regime_normal > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(regime_normal * 250);
                    }
                    if (expedente_matricula > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(expedente_matricula * 500);
                    }
                    if (exprevio_comercial > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(exprevio_comercial * 100);
                    }
                    if (tce > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(tce * 350);
                    }
                    if (dv > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(dv * 350);
                    }
                    if (pl > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(pl * 1000);
                    }
                    if (taxa_comunicaco > 0) {
                        agenciamntos = parseFloat(agenciamntos) + parseFloat(taxa_comunicaco);
                    }


                    var honorario = parseFloat(honorario) + parseFloat(agenciamntos);


                    if (Math.ceil(honorario) > 0) {
                        addHonorarioItem(Math.ceil(honorario));
                    }
                }
                if (data.dsp_desembaraco_id == 2) {
                    addTransportDeslocamentoItem();
                }
                $.unblockUI();

            } else {
                $.unblockUI();

            }


        });
    }
}



function addNullItem() {
    jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        if ($("#faturadefinitivaitem-" + item.rowIndex + "-item_origem_id").val() <= 0) {
            $("#faturadefinitivaitem-" + item.rowIndex + "-dsp_item_id").val(null).attr("selected", "selected");
            $("#faturadefinitivaitem-" + item.rowIndex + "-item_origem_id").val('M');
            $("#hidemCount").val(item.rowIndex);
        }

    });
}




function addHonorarioItem(honorario) {
    var id_honorario = 1002;
    var valor_honorario = honorario;
    var id_iva_honorario = 1003;
    var valor_iva_honorario = Math.ceil(honorario * 0.15);
    var item_origem_id = 'X';

    for (var i = 0; i <= 25; i++) {
        for (var j = 0; j <= 25; j++) {
            if (($("#faturadefinitivaitem-" + i + "-dsp_item_id").val() == 1002)) {
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


    jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        $("#faturadefinitivaitem-" + item.rowIndex + "-dsp_item_id").val(id_honorario).attr("selected",
            "selected");
        $("#faturadefinitivaitem-" + item.rowIndex + "-valor").val(valor_honorario);
        $("#faturadefinitivaitem-" + item.rowIndex + "-item_origem_id").val(item_origem_id);
        $("#hidemCount").val(item.rowIndex);

    });

    var a = document.getElementById("add-item").click();
    id_honorario = null;
    valor_honorario = null;

    for (var i = 0; i <= 25; i++) {
        for (var j = 0; j <= 25; j++) {
            if (($("#faturadefinitivaitem-" + i + "-dsp_item_id").val() == 1003)) {
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



    jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        $("#faturadefinitivaitem-" + item.rowIndex + "-dsp_item_id").val(id_iva_honorario).attr("selected",
            "selected");
        $("#faturadefinitivaitem-" + item.rowIndex + "-valor").val(valor_iva_honorario);
        $("#faturadefinitivaitem-" + item.rowIndex + "-item_origem_id").val(item_origem_id);
        $("#hidemCount").val(item.rowIndex);

    });

    var b = document.getElementById("add-item").click();

    id_iva_honorario = null;
    valor_iva_honorario = null;
    item_origem_id = 'M';
    $.unblockUI();

}







function addTransportDeslocamentoItem() {
    var id = 1010;
    var value = 600;
    var item_origem_id = 'M';

    for (var i = 0; i <= 25; i++) {
        for (var j = 0; j <= 25; j++) {
            if (($("#faturadefinitivaitem-" + i + "-dsp_item_id").val() == 1010)) {
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
    jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        $("#faturadefinitivaitem-" + item.rowIndex + "-dsp_item_id").val(id).attr("selected", "selected");
        $("#faturadefinitivaitem-" + item.rowIndex + "-valor").val(value);
        $("#faturadefinitivaitem-" + item.rowIndex + "-item_origem_id").val(item_origem_id);
        $("#hidemCount").val(item.rowIndex);

    });
    var a = document.getElementById("add-item").click();
    id = null;
    value = null;
    item_origem_id = 'M';
    $.unblockUI();

}





function addImpressoItem() {
    var id = 1000;
    var value = 600;
    var item_origem_id = 'M';

    for (var i = 0; i <= 25; i++) {
        for (var j = 0; j <= 25; j++) {
            if (($("#faturadefinitivaitem-" + i + "-dsp_item_id").val() == 1000)) {
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
    jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        $("#faturadefinitivaitem-" + item.rowIndex + "-dsp_item_id").val(id).attr("selected", "selected");
        $("#faturadefinitivaitem-" + item.rowIndex + "-valor").val(value);
        $("#faturadefinitivaitem-" + item.rowIndex + "-item_origem_id").val(item_origem_id);
        $("#hidemCount").val(item.rowIndex);

    });
    var a = document.getElementById("add-item").click();
    id = null;
    value = null;
    item_origem_id = 'M';
    $.unblockUI();

}



function updateFormTn(obj) {
    var tn = $("#tn").val();

    if (tn > 0) {
        $.blockUI({
            message: '<img src=' + urlLoaderGif + ' />'
        });
        $("#taxa_comunicaco").val(null)
        addHonorarioItem(200);
    }
}

function updateFormNumero(obj) {
    var data = $("#data").val();
    $.getJSON(urlAppDocumentoGetNumber, {
        documento_id: 1,
        data: data
    }, function(data) {
        if (data) {
            $("#numero").val(data);
        }
    });
}
</script>
