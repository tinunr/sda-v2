<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use app\modules\dsp\models\Person;

use kartik\select2\Select2;

$initValueText = empty($model->user_id) ? '' : User::findOne($model->user_id)->name;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>
<div class="row">

    <?php $form = ActiveForm::begin(); ?>


            <?= $form->field($model, 'descricao')->textInput() ?> 


             





   



    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>