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

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

    <div class="row">

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'numero')->textInput(['readonly' => false]) ?>
            </div>
            <div class="col-md-4">
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
            <div class="col-md-4">
                <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => ' ', 'id' => 'dsp_processo_id'],
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
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'data' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
                    'options' => ['placeholder' => 'Selecione cliente ...', 'id' => 'dsp_person_id'],
                    'pluginOptions' => [
                        'allowClear' => false,
                    ],

                ]); ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'nord')->textInput(['id' => 'nord', 'readonly' => false]) ?>
            </div>


            <div class="col-md-3">
                <?= $form->field($model, 'n_registo')->textInput(['id' => 'n_registo', 'readonly' => false]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'n_receita')->textInput(['id' => 'n_registo', 'readonly' => false]) ?>
            </div>

        </div>


        <div class="row">

            <div class="col-md-2">

                <?= $form->field($model, 'impresso_principal')->textInput(['id' => 'impresso_principal']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'impresso_intercalar')->textInput(['id' => 'impresso_intercalar']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'pl')->textInput(['id' => 'pl']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'gti')->textInput(['id' => 'gti']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'dv')->textInput(['id' => 'dv']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'tce')->textInput(['id' => 'tce']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">

                <?= $form->field($model, 'tn')->textInput(['id' => 'tn']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'fotocopias')->textInput(['id' => 'fotocopias']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'form')->textInput(['id' => 'form']) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'regime_normal')->textInput(['id' => 'regime_normal']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'regime_especial')->textInput(['id' => 'regime_especial']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'impresso_intercalar')->textInput(['id' => 'impresso_intercalar']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'exprevio_comercial')->textInput(['id' => 'exprevio_comercial']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'expedente_matricula')->textInput(['id' => 'expedente_matricula']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'honorario')->textInput(['id' => 'honorario']) ?>

            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'iva_honorario')->textInput(['id' => 'iva_honorario']) ?>
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
                'limit' => 25, // the maximum times, an element can be cloned (default 999)
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


                <?php
                    // necessary for update action.
                    if (!$item->isNewRecord) {
                        echo Html::activeHiddenInput($item, "[{$i}]id");
                    }
                    ?>

                <tr class="item">

                    <th>
                        <button type="button" id="removeitem" class="remove-item btn btn-danger btn-xs"><i
                                class="glyphicon glyphicon-minus"></i></button>

                    </th>
                    <th>
                        <?= $form->field($item, "[{$i}]dsp_item_id")->dropDownList(
                                ArrayHelper::map(Item::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                                ['prompt' => 'Selecione ...']
                            )->label(false); ?>


                    </th>
                    <th style="width: 250px">
                        <?= $form->field($item, "[{$i}]valor")->textInput(['readonly' => false])->label(false) ?>
                    </th>

                    <?php endforeach; ?>

            </table>
            <button id="add-item" type="button" class="add-item btn btn-success btn-xs"><i
                    class="glyphicon glyphicon-plus"></i></button>

            <?= Html::hiddenInput('hidemCount', $i, ['id' => 'hidemCount', 'class' => 'form-control']); ?>
            <?php DynamicFormWidget::end(); ?>
        </div>
        <table class=" table table-hover">
            <tr>
                <th style="padding: 10px">TOTAL</th>

                <th style="width: 250px">
                    <?= $form->field($model, 'valor')->textInput(['id' => 'total_valor', 'readonly' => true])->label(false) ?>
                </th>
            </tr>
        </table>











        <div class="form-group">
            <?= Html::submitButton(Yii::$app->ImgButton->Img('save') . ' Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'title' => 'SALVAR', 'id' => 'save']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


</section>

<script type="text/javascript">
function updateTotal() {
    var total_valor = 0;
    for (var i = 0; i <= 25; i++) {
        if ($("#faturadefinitivaitem-" + i + "-valor").val() > 0) {
            var total_valor = parseFloat(total_valor) + parseFloat($("#faturadefinitivaitem-" + i + "-valor").val());
        }
    }
    $("#total_valor").val(total_valor);
}
window.setInterval(updateTotal, 1000);
</script>
