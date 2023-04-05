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
use app\modules\fin\models\DocumentoPagamento;

$total_valor = 0;
$total_valor_pagar = 0;
$this->registerJsFile(Url::to('@web/js/recebimento.js'), ['position' => \yii\web\View::POS_END]);
$this->registerJsFile(Url::to('@web/js/formaReceber.js'), ['position' => \yii\web\View::POS_END]);


/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row">
        <div class="row">

            <div class="col-md-2">
                <?= $form->field($model, 'numero')->textInput(['id' => 'numero', 'readonly' => false]) ?>

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
            <div class="col-md-7">
                <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'data' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
                    'options' => ['placeholder' => 'Selecione ...', 'id' => 'dsp_person_id'],
                    'pluginOptions' => [
                        #'allowClear' => true,
                        #'disabled' => true,
                    ],

                ]); ?>

            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'fin_documento_pagamento_id')->widget(Select2::classname(), [
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'data' => ArrayHelper::map(DocumentoPagamento::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                    'options' => ['placeholder' => 'Selecione ...', 'id' => 'fin_documento_pagamento_id', 'onchange' => 'disableFields(this)'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],

                ])->label('Forma de Receber'); ?>

            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fin_banco_id')->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'options' => ['id' => 'fin_banco_id', 'onchange' => 'disableFields(this)'],
                    'pluginOptions' => [
                        'depends' => ['fin_documento_pagamento_id'],
                        'allowClear' => true,
                        'placeholder' => 'Select...',
                        'url' => Url::to(['/fin/banco/banco-list'])
                    ]
                ]); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fin_banco_conta_id')->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'options' => ['id' => 'fin_banco_conta_id', 'onchange' => 'disableFields(this)'],
                    'pluginOptions' => [
                        'depends' => ['fin_banco_id'],
                        'allowClear' => true,
                        'placeholder' => 'Select...',
                        'url' => Url::to(['/fin/banco-conta/conta-list'])
                    ]
                ]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'numero_documento')->textInput(['id' => 'numero_documento']) ?>
            </div>
            <div class="col-md-6">
                <?php echo $form->field($model, 'data_documento')->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_INPUT,
                    'removeButton' => ['icon' => 'trash'],
                    'pickerButton' => true,
                    'options' => ['id' => 'data_documento'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ]);
                ?>
            </div>
        </div>

    </div>

    <?= $form->field($model, 'descricao')->textArea(['id' => 'descricao']) ?>


    <div class="titulo-principal">
        <h5 class="titulo">Itens</h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>

    <div class="row">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 300, // the maximum times, an element can be cloned (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $modelsReceita[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'dsp_item_id',
                'valor',
                'ab',
            ],
        ]); ?>

        <input id="item-control" type="hidden" name="_method" value=0 />

        <table class="container-items table table-hover">
            <tr>
                <th style="width: 40%">Documento</th>
                <th>Valor</th>
                <th>Valor Recebido</th>
                <th>Saldo</th>
            </tr>
            <?php foreach ($modelsReceita as $i => $item) : ?>

            <?php
                // necessary for update action.
                if (!$item->isNewRecord) {
                    echo Html::activeHiddenInput($item, "[{$i}]id");
                    echo Html::activeHiddenInput($item, "[{$i}]dsp_fataura_provisoria_id");
                }
                $item->valor_recebido = $item->saldo;
                $item->valor = $item->saldo;
                $total_valor = $item->valor + $total_valor;
                $item->saldo = $item->valor - $item->valor_recebido;
                // $item->descricao = 'FP Nº '.$item->faturaProvisoria->numero;
                $total_valor_pagar = $total_valor_pagar + $item->valor;
                $model->valor = $total_valor_pagar;
                ?>


            <tr class="item">


                <td style="width: 40%">
                    <?= $form->field($item, "[{$i}]descricao")->textInput(['maxlength' => true, 'readonly' => true])->label(false) ?>
                </td>
                <td><?= $form->field($item, "[{$i}]valor")->textInput(['maxlength' => true, 'readonly' => true])->label(false) ?>
                </td>
                <td><?= $form->field($item, "[{$i}]valor_recebido")->textInput(['onchange' => 'updateData(this)'])->label(false) ?>
                </td>
                <td> <?= $form->field($item, "[{$i}]saldo")->textInput(['maxlength' => true, 'readonly' => true])->label(false) ?>
                </td>
            </tr>
            </tr>

            <?php endforeach; ?>
            <tr>
                <th>Total</th>
                <th><?= Html::textInput('total_valor',  $total_valor, ['id' => 'total_valor', 'class' => 'form-control', 'readonly' => true]) ?>
                </th>
                <th>
                    <?= $form->field($model, 'valor')->textInput(['id' => 'total_valor_pagar', 'readonly' => true])->label(false) ?>

                    <?= Html::hiddenInput('hidemCount', $i, ['id' => 'hidemCount', 'class' => 'form-control']); ?>
                </th>

                <th><?= Html::textInput('total_saldo',  0, ['id' => 'total_saldo', 'class' => 'form-control', 'readonly' => true]) ?>
                </th>

            </tr>
        </table>

        <?php DynamicFormWidget::end(); ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save') . ' SALVAR', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
