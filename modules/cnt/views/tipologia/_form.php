<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'abreviatura')->textInput(); ?>
        <?= $form->field($model, 'descricao')->textInput(); ?>
        <?= $form->field($model, 'numero_destino')->textInput(); ?>
        <?= $form->field($model, 'numero_destino_b')->textInput(); ?>
    

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
