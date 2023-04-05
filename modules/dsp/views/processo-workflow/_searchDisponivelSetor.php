<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\dsp\models\Setor;
use app\modules\dsp\models\ProcessoWorkflowStatus;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">

    <?php $form = ActiveForm::begin([
        'action' => ['processo-setor'],
        'method' => 'get',
    ]); ?>

    <div class="col-md-2">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Nº PROCESSO'])->label(false) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'dsp_setor_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Setor::find()->all(), 'id','descricao'),
        //   'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'SETOR'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?>
    </div>
    <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>


    <?php ActiveForm::end(); ?>
</div>
