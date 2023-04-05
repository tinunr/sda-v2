<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use conquer\select2\Select2Widget;

use app\models\AuthItem;
use app\models\User;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-assignment-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'numero')->textInput(['placeholder'=>'Documento']) ?>

	  
	  
	 

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
