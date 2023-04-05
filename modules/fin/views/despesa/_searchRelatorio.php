<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use conquer\select2\Select2Widget;
use app\models\Ano;
use app\modules\dsp\models\Person;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['relatorio'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="col-md-2">
      <?php echo $form->field($model, 'beginDate')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => 'DATA DE INICIO'],
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
                        'options' => ['placeholder' => 'DATA DE FIM'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
              // 'allowClear' => true,
                            'autoClose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);?>
      </div>
     <div class="col-md-5">
      <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map((new Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_despesa A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => 'CLIENTE / FORNECIDOR','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label(false);?>

    </div>

     
      <div class="col-md-3">

    <?=$form->field($model, 'is_lock')->widget(Select2::classname(), [
          'data' =>[0 => 'Não Fechado', 1 => 'Fechado'],
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'ESTADO'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);

      ?>
    </div>
      <div class="col-md-3">

    <?=$form->field($model, 'pagamento')->widget(Select2::classname(), [
          'data' =>[0 => 'Não Pago', 1=>'Pago'],
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'PAGAMENTO'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);

      ?>
    </div>

        <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
       


    <?php ActiveForm::end(); ?>

     