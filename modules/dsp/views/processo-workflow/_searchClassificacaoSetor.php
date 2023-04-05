<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\modules\dsp\models\Setor;
use kartik\select2\Select2;

?>


<?php $form = ActiveForm::begin([
        'action' => ['classificacao-setor'],
        'method' => 'get',
    ]); ?>

<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'dsp_setor_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Setor::find()->where(['>','valor_subcidio',0])->all(), 'id','descricao'),
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'SETOR'],
          'pluginOptions' => [
              'allowClear' => false
          ],
      ])->label(false)?>
    </div>
    <div class="col-md-2">
        <?= Html::input('number', 'valor_subcidio', $dspSetor->valor_subcidio,['class'=>'form-control']) ?>
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
