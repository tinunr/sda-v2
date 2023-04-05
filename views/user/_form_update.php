<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use backend\models\CpiPercurso;
use backend\models\Departamento;
use backend\models\CpiDisciplina;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Desciplina */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="desciplina-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder' => 'Nome']) ?>

    <?= $form->field($model, 'username')->textInput(['readyonly' => true,'placeholder' => 'Nome']) ?>



    <div class="form-group">
        <?= Html::submitButton( 'Criar', ['class' =>'btn btn-success' ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
