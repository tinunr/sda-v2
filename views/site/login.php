<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>


    <?php $form = ActiveForm::begin(['fieldConfig' => ['template' => '{label}{input}']]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('E-mail *') ?>

        <?= $form->field($model, 'password')->passwordInput()->label('Password *') ?>

        <?= Html::submitButton('ENTRAR ', ['class' => 'btn  btn-block btn-flat', 'name' => 'login-button']) ?>

    <?php ActiveForm::end(); ?>
