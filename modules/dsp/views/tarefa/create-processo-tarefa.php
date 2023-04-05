<?php

use yii\helpers\Html;
use yii\helpers\url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use wbraganca\dynamicform\DynamicFormWidget;
use app\modules\dsp\models\Tarefa;


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

        <div class="row">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 99, // the maximum times, an element can be cloned (default 999)
                'min' => 0, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsTarefa[0],
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
                    <th>Tarefa</th>
                </tr>
                <?php foreach ($modelsTarefa as $i => $item) : ?>



                <tr class="item">
                    <?php
                        // necessary for update action.
                        if (!$item->isNewRecord) {
                            echo Html::activeHiddenInput($item, "[{$i}]id");
                        }
                        $item->dsp_processo_id = $model->id;
                        ?>
                    <td>
                        <button type="button" id="removeitem" class="remove-item btn btn-danger btn-xs"><i
                                class="glyphicon glyphicon-minus"></i></button>

                    </td>
                    <td>
                        <?= $form->field($item, "[{$i}]dsp_tarefa_id")->dropDownList(
                                ArrayHelper::map(Tarefa::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                                ['prompt' => 'Selecione ...']
                            )->label(false); ?>
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
