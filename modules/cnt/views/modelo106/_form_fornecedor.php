<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Ano;
use app\models\Mes;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['view','id'=>$model->cnt_modelo_106_id], ['class' => 'btn btn-warning']) ?>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-2">
      <?= $form->field($model, 'origem')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'nif')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-4">
      <?= $form->field($model, 'designacao')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'tp_doc')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'num_doc')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?php echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            // 'todayHighlight' => true,
                        ]
                ]);?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'vl_fatura')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'vl_base_incid')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'tx_iva')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'iva_sup')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'direito_ded')->textInput(['placeholder'=>''])?>
    </div>
     <div class="col-md-2">
      <?= $form->field($model, 'iva_ded')->textInput(['placeholder'=>''])?>
    </div>
     <div class="col-md-2">
      <?= $form->field($model, 'tipologia')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'linha_dest_mod')->textInput(['placeholder'=>''])?>
    </div>
  </div>

    
          


    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</section>