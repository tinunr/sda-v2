<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Ano;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

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
              'allowClear' => true,
          ],
      ])->label(false)?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Nº DESPACHO / Nº REGISTRO / NORDE '])->label(false) ?>
    </div>
    <div class="col-md-2">
      <?php echo $form->field($model, 'beginDate')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options'=>['placeholder'=>'DATA REG. INICIO'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);?>
      </div>
      <div class="col-md-2">
      <?php echo $form->field($model, 'endDate')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options'=>['placeholder'=>'DATA REG. FIM'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);?>
      </div>
        <?= Html::submitButton(Yii::$app->ImgButton->Img('filter'), ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

