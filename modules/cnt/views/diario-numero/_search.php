<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\models\Ano;
use app\models\Mes;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="col-md-2">
    <?= $form->field($model, 'ano')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'ano', 'ano'),
        'options' => ['placeholder' => 'Selecione ...'],
        'pluginOptions' => [
            'allowClear' => true, 
        ], 
    ])->label(false);?> 
    </div>
    <div class="col-md-2">
    <?= $form->field($model, 'mes')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' => ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id', 'descricao'),
        'options' => ['placeholder' => 'Selecione ...'],
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

