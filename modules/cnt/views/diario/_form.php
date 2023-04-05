<?php

use yii\helpers\Html;
use conquer\select2\Select2Widget;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use app\modules\dsp\models\Person;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

            <?= $form->field($model, 'id')->textInput() ?> 
            <?= $form->field($model, 'descricao')->textInput() ?> 





   



    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
