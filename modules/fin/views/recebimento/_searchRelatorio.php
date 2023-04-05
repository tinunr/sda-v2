<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use conquer\select2\Select2Widget;
use app\modules\dsp\models\Person;
use app\models\Ano;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\dialog\Dialog;
$initValueText = empty($model->dsp_person_id) ? '' : Person::findOne($model->dsp_person_id)->nome;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['relatorio'],
        'method' => 'get',
    ]); ?>

    <div class="row">
     <div class="col-md-2">
     <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
        'options' => ['placeholder' => 'Selecione ...'],
        'pluginOptions' => [
            'allowClear' => true,

        ],

    ])->label(false);?>
    </div>
    <div class="col-md-5">
    <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'initValueText' => $initValueText,
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => 'Selecione ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/dsp/person/ajax']),
                'dataType' => 'json',
            ],
        ],

    ])->label(false);?>
</div>
     <div class="col-md-2">
      <?php echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);?>
      </div>



        <?= Html::submitButton('<i class="fa fa-filter"></i> ', ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">


    <?php ActiveForm::end(); ?>

    <?= Html::a('<i class="fas fa-print"></i>', ['reportpdf', 'bas_ano_id'=>$model->bas_ano_id, '$dsp_person_id'=>$model->dsp_person_id, 'data'=>$model->data], ['class'=>'btn btn-warning']) ?>
