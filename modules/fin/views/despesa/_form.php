<?php

use yii\helpers\Html;
use yii\helpers\url;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\select2\Select2;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\Item;
use kartik\depdrop\DepDrop;
use app\modules\dsp\models\Processo;
$initValueText = null;
if (!empty($model->dsp_processo_id)) {
    $processo = Processo::findOne($model->dsp_processo_id);
    $initValueText = $processo->numero . '/' . $processo->bas_ano_id;
}
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Url::to('@web/js/src/fin_despesa_cliente.js'), ['position' => \yii\web\View::POS_END]);

?>

<section>

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

    <?= $form->errorSummary($model); ?>

    <div class="row">

        <div class="col-md-3">
            <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [ 
                 'initValueText' => $initValueText,
                'theme' => Select2::THEME_BOOTSTRAP,
                'options' => ['placeholder' => ' ', 'id' => 'dsp_processo_id',],
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
            <?= $form->field($model, 'dsp_fatura_provisoria_id')->widget(DepDrop::classname(), [
                'type' => DepDrop::TYPE_SELECT2,
                'options' => ['id' => 'dsp_fatura_provisoria_id', 'onchange' => 'formDespesaCliente(this)'],
                'pluginOptions' => [
                    'depends' => ['dsp_processo_id'],
                    'initialize' => true,
                    'allowClear' => true,
                    'placeholder' => 'Select...',
                    'url' => Url::to(['/fin/fatura-provisoria/list-processo'])
                ]
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_BOOTSTRAP,
                'data' => ArrayHelper::map(Person::find()->where(['is_fornecedor' => 1])->orderBy('nome')->all(), 'id', 'nome'),
                'options' => ['placeholder' => 'Selecione ...', 'id' => 'dsp_person_id', 'onchange' => 'formDespesaCliente(this)', 'disabled' => (!$model->isNewRecord && $readonly)],
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => 'Select...',
                    'url' => Url::to(['/fin/fatura-provisoria/list-processo'])
                ]
            ]); ?>
        </div>
        <div class="col-md-3">
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
        <div class="col-md-3">
            <?php echo $form->field($model, 'data_vencimento')->widget(DatePicker::classname(), [
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

        <div class="col-md-3">
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
        <div class="col-md-3">
            <?php echo $form->field($model, 'data_vencimento')->widget(DatePicker::classname(), [
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

    </div>

    <?= $form->field($model, 'descricao')->textArea(['id' => 'descricao', 'placeholder' => 'Descrição', 'maxlength' => true]) ?>




    <div class="titulo-principal">
        <h5 class="titulo">DETALHES DA DESPESA</h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>
    <div class="row">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 25, // the maximum times, an element can be cloned (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $modelsDespesaItem[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'item_descricao',
                'valor',
            ],
        ]); ?>

        <input id="item-control" type="hidden" name="_method" value=0 />

        <table class="container-items table ">
            <?php foreach ($modelsDespesaItem as $i => $item) : ?>



            <tr class="item">
                
                <td>
                    <button type="button" id="removeitem" class="remove-item btn btn-danger btn-xs"><i
                            class="glyphicon glyphicon-minus"></i></button>
                            <?php
                    // necessary for update action.
                    if (!$item->isNewRecord) {
                        echo Html::activeHiddenInput($item, "[{$i}]id");
                    }
                    ?>

                </td>
                <td>
                    <?= $form->field($item, "[{$i}]item_id")->dropDownList(
                            ArrayHelper::map(Item::find()->where(['dsp_item_type_id' => 1])->orderBy('descricao')->all(), 'id', 'descricao'),
                            ['prompt' => ' ']
                        )->label(false); ?>
                </td>

                <td style="width: 250px">
                    <?= $form->field($item, "[{$i}]valor")->textInput(['maxlength' => true])->label(false) ?></td>
            </tr>

            <?php endforeach; ?>
            <tfoot>
            <tr>
                <th><button id="add-item" type="button" class="add-item btn btn-success btn-xs"><i
                class="glyphicon glyphicon-plus"></i></button></th>
            <th >
                Total<?= Html::hiddenInput('hidemCount', $i, ['id' => 'hidemCount', 'class' => 'form-control']); ?></th>
            <th><?= $form->field($model, 'valor')->textInput(['id' => 'valor', 'readonly' => true])->label(false) ?>
            </th>
        </tr>
        </tfoot>


        </table>
        <?php DynamicFormWidget::end(); ?>
    </div>

  








    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save') . ' Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    </div>
</section>





<script type="text/javascript">
function updateTotal() {
    var valor = 0
    for (var i = 0; i <= 25; i++) {
        if ($("#despesaitem-" + i + "-valor").val() > 0) {
            var valor = parseFloat(valor) + parseFloat($("#despesaitem-" + i + "-valor").val());
        }
    }
    $("#valor").val(valor);
}
window.setInterval(updateTotal, 1000);
</script>