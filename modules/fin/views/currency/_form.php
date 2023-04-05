<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;



/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

        <?= $form->field($model, 'code')->textInput() ?>
        <?= $form->field($model, 'sigla')->textInput() ?>
        <?= $form->field($model, 'descricao')->textInput() ?>

       

     
   
        



    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>