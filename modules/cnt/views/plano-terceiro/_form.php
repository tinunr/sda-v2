<?php

use yii\helpers\Html;
use conquer\select2\Select2Widget;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Propetarios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="propetarios-form">

    <?php $form = ActiveForm::begin(); ?>

      <div class="row">
      
    <div class="col-md-12"> 
    <?= $form->field($model, 'nome')->textInput(['maxlength' => true])->label('Nome') ?>
    </div>
    
    </div>
    <div class="row">
    <div class="col-md-6"> 
    <?= $form->field($model, 'telefone')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6"> 
    <?= $form->field($model, 'telemovel')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6"> 
    <?= $form->field($model, 'nif')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6"> 
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
    </div>

    <div class="row">
    <div class="col-md-4"> 
    <?= $form->field($model,'is_cliente')->inline()->radioList([ '1'=>'Sim','0'=>'Não',]) ?>
</div>
    <div class="col-md-4"> 

    <?= $form->field($model,'is_fornecedor')->inline()->radioList([ '1'=>'Sim','0'=>'Não',]) ?>
</div>
<div class="col-md-4"> 

    <?= $form->field($model,'isencao_honorario')->inline()->radioList([ '1'=>'Sim','0'=>'Não',]) ?>
</div>
</div>

   
    <?= $form->field($model, 'endereco')->textArea(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
