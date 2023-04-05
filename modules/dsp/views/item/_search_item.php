<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index-item'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Procurar ...'])->label(false) ?>
    </div>
        <?= Html::submitButton('<i class="fa fa-search"></i> Procurar', ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

