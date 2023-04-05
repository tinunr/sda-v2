<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\ProcessoWorkflowStatus;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

<div class="row">
    <div class="col-md-1">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Nº'])->label(false) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'status')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(ProcessoWorkflowStatus::find()->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Estado do workflow','multiple' => true],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?>
    </div>
    <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
    <div class="row pull-right">


        <?php ActiveForm::end(); ?>
