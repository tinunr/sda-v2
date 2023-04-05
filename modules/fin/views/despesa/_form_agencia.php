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
use app\modules\cnt\models\Documento;
use app\modules\cnt\models\PlanoTerceiro;



/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>

<section>

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

        <?= $form->errorSummary($model); ?>
        <?= $form->field($model, 'fin_despesa_tipo_id')->hiddenInput(['id' => 'fin_despesa_tipo_id'])->label(false); ?>
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'numero')->textInput(['id' => 'numero', 'placeholder' => 'Numero', 'maxlength' => true]) ?>

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

        <div class="col-md-4">
            <?= $form->field($model, 'cnt_documento_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_BOOTSTRAP,
                'data' => ArrayHelper::map(Documento::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                'options' => ['placeholder' => '', 'id' => 'cnt_documento_id'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]); ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
                'theme' => Select2::THEME_BOOTSTRAP,
                'data' => ArrayHelper::map(Person::find()->where(['is_fornecedor' => 1])->orderBy('nome')->all(), 'id', 'nome'),
                'options' => ['placeholder' => 'Selecione ...', 'id' => 'dsp_person_id'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],

            ]); ?>
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
                    'valor_iva',
                ],
            ]); ?>

            <input id="item-control" type="hidden" name="_method" value=0 />

            <table class="container-items table table-hover">
                <tr>
                    <th>Acções</th>
                    <th>Descrição</th>
                    <th>Valor C/IVA</th>
                    <th>IVA</th>
                    <th>Terceiro</th>
                </tr>
                <?php foreach ($modelsDespesaItem as $i => $item) : ?>



                    <tr class="item">

                        <td style="width:20px;">
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            <?php
                            // necessary for update action.
                            if (!$item->isNewRecord) {
                                echo Html::activeHiddenInput($item, "[{$i}]id");
                            }
                            ?>
                        </td>
                        <td>
                            <?= $form->field($item, "[{$i}]item_id")->widget(Select2::classname(), [
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'data' => ArrayHelper::map(Item::find()->where(['dsp_item_type_id' => 2])->orderBy('descricao')->all(), 'id', 'descricao'),
                                'options' => ['placeholder' => ''],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ])->label(false); ?>
                        </td>

                        <td style="width: 150px">
                            <?= $form->field($item, "[{$i}]valor")->textInput(['maxlength' => true])->label(false) ?></td>
                        <td style="width: 150px">
                            <?= $form->field($item, "[{$i}]valor_iva")->textInput(['maxlength' => true])->label(false) ?>
                        </td>
                        <td style="width: 250px">
                            <?= $form->field($item, "[{$i}]cnt_plano_terceiro_id")->widget(Select2::classname(), [
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'data' => ArrayHelper::map(PlanoTerceiro::find()->orderBy('nome')->all(), 'id', 'nome'),
                                'options' => ['placeholder' => ''],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ])->label(false); ?>
                        </td>
                    </tr>

                <?php endforeach; ?>


            </table>
            <button id="add-item" type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>


            <?php DynamicFormWidget::end(); ?>
        </div>

        <table class="table table-hover">
            <tr>
                <th style="width: 55%">
                    Total<?= Html::hiddenInput('hidemCount', $i, ['id' => 'hidemCount', 'class' => 'form-control']); ?></th>
                <th><?= $form->field($model, 'valor')->textInput(['id' => 'valor', 'readonly' => true])->label(false) ?>
                </th>
            </tr>
        </table>








        <div class="form-group">
            <?= Html::submitButton(Yii::$app->ImgButton->Img('save') . ' SALVAR', ['class' => 'btn btn-success']) ?>
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