<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\Ano;
use app\modules\dsp\models\PedidoLevantamentoStatus;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-2">
      <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Ano ...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?> 
    </div>
    <div class="col-md-2">
          <?= $form->field($model, 'status')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(PedidoLevantamentoStatus::find()->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Estado ...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?>
    </div> 
    <div class="col-md-4">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Nº Levantamento'])->label(false) ?>
    </div>
        <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

