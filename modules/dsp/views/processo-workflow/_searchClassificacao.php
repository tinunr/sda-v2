<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\models\User;
use kartik\select2\Select2;

?>


<?php $form = ActiveForm::begin([
        'action' => ['classificacao'],
        'method' => 'get',
    ]); ?>

<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(User::find()->all(), 'id','name'),
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'FUNCIONÁRIO'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false)?>
    </div>
    <div class="col-md-2">
        <?php echo $form->field($model, 'dataInicio')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);?>
    </div>
    <div class="col-md-2">
        <?php echo $form->field($model, 'dataFim')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);
    ?>
    </div>
    <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
    <div class="row pull-right">


        <?php ActiveForm::end(); ?>