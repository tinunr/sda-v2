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

        <?php $form = ActiveForm::begin( ); ?>

        <?= $form->errorSummary($model); ?>

        <?= $form->field($model, 'abreviatura')->textInput() ?>
        <?= $form->field($model, 'name')->textInput() ?>


        <div class="form-group">
            <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</section>
