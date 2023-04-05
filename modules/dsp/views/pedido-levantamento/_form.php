<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(); ?>

 <div class="row">
  
       <?php echo $form->field($model, 'data_proragacao')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);
    ?>
  
  
 </div>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


