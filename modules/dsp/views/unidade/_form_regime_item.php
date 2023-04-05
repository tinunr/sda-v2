<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-2',
                'offset' => 'col-sm-offset-1',
                'wrapper' => 'col-sm-10',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]); ?>

    <?= $form->errorSummary($modelItem); ?>
    <div class="row">
    <div class="col-md-7">
        <?= $form->field($modelItem, 'descricao')->textInput(['placeholder'=>'Codigo']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($modelItem, 'forma')->textInput(['placeholder'=>'Codigo']) ?>
    </div>
    <div class="col-md-1">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

        

       

     
   
        



    <div class="form-group">
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>