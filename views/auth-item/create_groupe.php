<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\models\AuthItem; 

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */

$this->registerCss("
.radio-inline, .checkbox-inline {
    width: 32%;
}");

?>
<section>
<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => 'Nome do item','maxlength' => true]) ?>

    <?php #$form->field($model, 'type')->dropDownList([ '1' => 'GRUPO', '2' => 'ITEM'], ['prompt' => ' Selecione ...']) ?>


    <?= $form->field($model, 'description')->textArea(['placeholder' => 'Descrição','maxlength' => true]) ?>
    

      

    <?= $form->field($model, 'rule_name')->textInput(['placeholder' => 'Rule name','maxlength' => true]) ?>

     
        <?= $form->field($model,'list')->inline()->checkboxList(ArrayHelper::map(AuthItem::find()->where(['type'=>2])->orderBy('name')->all(), 'name','name'))->label('Lista'); ?>


    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i> Salvar', ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>