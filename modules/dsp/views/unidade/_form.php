<?php

use yii\helpers\Html;
use conquer\select2\Select2Widget;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

use app\models\Zona;
use app\models\TipoResidencia;
use app\models\Denominacao;
use app\models\MeioResidencia;
use app\models\Tipologia;
use app\models\Propetario;

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

    <?= $form->errorSummary($model); ?>

        <?= $form->field($model, 'id')->textInput(['placeholder'=>'Codigo']) ?>
        <?= $form->field($model, 'descricao')->textInput(['placeholder'=>'Codigo']) ?>

       

     
   
        



    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>