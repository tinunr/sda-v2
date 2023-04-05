<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Ano;
use app\modules\dsp\models\Desembaraco;
use yii\helpers\ArrayHelper;

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
         'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Ano ...'],
          'pluginOptions' => [
              // 'disabled' => true,
              'allowClear' => true,
          ],
      ])->label(false)?>
    </div>
    <div class="col-md-2">
         <?= $form->field($model, 'dsp_desembaraco_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Desembaraco::find()->orderBy('descricao')->all(), 'id','code'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Estância'],
          'pluginOptions' => [
              'disabled' => false,
              'allowClear' => true,
          ],
      ])->label(false)
            ?>
    </div>
    <div class="col-md-5">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Nº NORD / Nº PROCESSO / XML'])->label(false) ?>
    </div>
        <?= Html::submitButton(Yii::$app->ImgButton->Img('filter'), ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

