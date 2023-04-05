<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;

use app\models\Module;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-assignment-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'bas_module_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Module::find()->orderBy('descricao')->all(), 'id', 'descricao'),
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);?>

    <?= $form->field($model, 'parameter')->textInput(['placeholder'=>'']) ?>

    <?= $form->field($model, 'descricao')->textArea(['placeholder'=>'']) ?>

    <?= $form->field($model, 'value')->textInput(['placeholder'=>'']) ?>

    <?= $form->field($model, 'type')->textInput(['placeholder'=>'']) ?>


    <?= $form->field($model, 'isvaluechangeable')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>[1=>'SIM',0=>'NÃO'],
        'pluginOptions' => [
            'allowClear' => FALSE,
        ],
    ]);?>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
