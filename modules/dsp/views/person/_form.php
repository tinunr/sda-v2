<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Pais;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Propetarios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="propetarios-form">

    <?php $form = ActiveForm::begin(); ?>

      <div class="row">
      
    <div class="col-md-12"> 
    <?= $form->field($model, 'nome')->textInput()->label('Nome') ?>
    </div>
    
    </div>
    <div class="row">
    <div class="col-md-4"> 
    <?= $form->field($model, 'telefone')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4"> 
    <?= $form->field($model, 'telemovel')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4"> 
    <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>
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

   <?= $form->field($model, 'bas_pais_id')->widget(Select2::classname(), [
                 'theme' => Select2::THEME_BOOTSTRAP,
                 'data' =>ArrayHelper::map(Pais::find()->orderBy('name')->all(), 'id', 'name'),
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'allowClear' => true,
                            'todayHighlight' => true,
                    
                ],                
            ]);?> 
    <?= $form->field($model, 'endereco')->textArea(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
