<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\dsp\models\ItemTipo;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="col-md-3">
    <?= $form->field($model, 'dsp_item_type_id')->widget(Select2::classname(), [
                 'theme' => Select2::THEME_BOOTSTRAP,
                 'data' =>ArrayHelper::map(ItemTipo::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_item_type_id'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],                
            ])->label(false);?> 
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Procurar ...'])->label(false) ?>
    </div>
        <?= Html::submitButton('<i class="fa fa-search"></i> Procurar', ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

