<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\models\AuthItem; 

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */



?>

<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => '','maxlength' => true]) ?>

    <?php #$form->field($model, 'type')->dropDownList([ '1' => 'GRUPO', '2' => 'ITEM'], ['prompt' => ' Selecione ...']) ?>


    <?= $form->field($model, 'description')->textArea(['placeholder' => '','maxlength' => true]) ?>
    

      

    <?= $form->field($model, 'rule_name')->textInput(['placeholder' => '','maxlength' => true]) ?>

    

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i> Salvar', ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
