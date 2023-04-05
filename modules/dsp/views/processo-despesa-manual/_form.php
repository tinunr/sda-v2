<?php

use yii\helpers\Html;
use yii\helpers\url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use wbraganca\dynamicform\DynamicFormWidget;
use app\modules\dsp\models\Desembaraco;
use app\modules\dsp\models\Item;
use kartik\select2\Select2;
use app\models\Ano;
use kartik\depdrop\DepDrop;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>

<section>

    <div class="row">

        <?php $form = ActiveForm::begin([
            'options' => [
                'enctype' => 'multipart/form-data',
                'id' => 'dynamic-form'
            ]
        ]); ?>

        <?php
        if (!$model->isNewRecord) {
            echo $form->field($model, 'id')->hiddenInput(['id' => 'dsp_processo_id'])->label(false);
        }
        ?>



        <div class="row">

            <div class="col-md-3">
                <?= $form->field($model, 'n_levantamento')->textInput(['id' => 'n_levantamento']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'n_levantamento_ano_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
                    'hideSearch' => false,
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>

            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'n_levantamento_desembarco_id')->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'options' => ['id' => 'n_levantamento_desembarco_id'],
                    'data' => ArrayHelper::map(Desembaraco::find()->orderBy('descricao')->all(), 'id', 'code'),
                    'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                    'pluginOptions' => [
                        'depends' => ['dsp_processo_id'],
                        'placeholder' => 'Selecione ...',
                        'url' => Url::to(['/dsp/nord/desembarco-pr']),
                        'initialize' => true,

                    ]
                ]);
                ?>
            </div>
            <div class="col-md-3">

                <?php echo $form->field($model, 'pl_data_prorogacao')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['id'=>'data'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label('PL Data de Prorogação');?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'n_registro_tn')->textInput() ?>
            </div>
            <div class="col-md-9">
                <?= $form->field($model, 'processo_tce')->widget(Select2::classname(), [
                'options' => ['placeholder' => '', 'multiple' => true],
                'pluginOptions' => [
                    'tags' => true,
                    'tokenSeparators' => [',', ' '],
                    'maximumInputLength' => 10
                ],
            ])->label('TCE'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'dv')->textInput() ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'exame_previo_comercial')->textInput(['id' => 'exame_previo_comercial']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'requerimento_espeical')->textInput(['id' => 'requerimento_espeical']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'requerimento_normal')->textInput(['id' => 'requerimento_normal']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'policia_desova')->textInput() ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'policia_selagem')->inline()->radioList(['0' => 'Não', '1' => 'Sim']) ?>
            </div>
        </div>

        <div class="titulo-principal">
            <h5 class="titulo">item de despesa manual</h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>
        <div class="row">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 99, // the maximum times, an element can be cloned (default 999)
                'min' => 0, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsProcessoDespesaManual[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'item_descricao',
                    'valor',
                ],
            ]); ?>

            <input id="item-control" type="hidden" name="_method" value=0 />

            <table class="container-items table table-hover">
                <tr>
                    <th>#</th>
                    <th>ITEM</th>
                    <th>VALOR</th>
                </tr>
                <?php foreach ($modelsProcessoDespesaManual as $i => $item) : ?>



                <tr class="item">
                    <?php
                        // necessary for update action.
                        if (!$item->isNewRecord) {
                            echo Html::activeHiddenInput($item, "[{$i}]id");
                        }
                        ?>
                    <td>
                        <button type="button" id="removeitem" class="remove-item btn btn-danger btn-xs"><i
                                class="glyphicon glyphicon-minus"></i></button>

                    </td>
                    <td>
                        <?= $form->field($item, "[{$i}]dsp_item_id")->dropDownList(
                                ArrayHelper::map(Item::find()->where(['protocolo_processo' => 1])->orderBy('descricao')->all(), 'id', 'descricao'),
                                ['prompt' => 'Selecione ...']
                            )->label(false); ?>
                    </td>

                    <td style="width: 150px">
                        <?= $form->field($item, "[{$i}]valor")->textInput(['maxlength' => true])->label(false) ?>
                    </td>
                </tr>

                <?php endforeach; ?>


            </table>

            <button id="add-item" type="button" class="add-item btn btn-success btn-xs"><i
                    class="glyphicon glyphicon-plus"></i></button>

            <?php DynamicFormWidget::end(); ?>
        </div>










        <div class="form-group">
            <?= Html::submitButton('<i class="fas fa-save"></i>' . ' Salvar', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</section>
