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
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\Processo;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['report'],
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
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);?>
      </div>
     <div class="col-md-5">
         <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map((new Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_fatura_provisoria A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => 'CLIENTE','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label(false);?>

    </div>

     
      <div class="col-md-3">

    <?=$form->field($model, 'send')->widget(Select2::classname(), [
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

    <?=$form->field($model, 'recebido')->widget(Select2::classname(), [
          'data' =>[0 => 'Não Recebido', 1=>'Recebido'],
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'RECEBIDO'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);

      ?>
    </div>

        <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
       


    <?php ActiveForm::end(); ?>

     