<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\BasPais;
use common\models\BasConcelho;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

   <?= $form->field($model,'status')->inline()->radioList([ '10'=>'Ativo','0'=>'Desativo',]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


