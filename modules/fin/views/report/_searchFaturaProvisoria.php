<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\dsp\models\Person;
use app\models\Documento;
use app\models\Ano;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\db\Query;
use yii\bootstrap\Modal;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs("(function($) {
      $('#modal').modal();
  })(jQuery);", yii\web\View::POS_END, 'lang');
?>

   <?php Modal::begin([
                          'id'=>'modal',
                          'header' => 'RELATÓRIO - FATURA PROVISÓRIA',
                          'toggleButton' => false,
                          'size'=>Modal::SIZE_DEFAULT,
                          'options' => [
                            // 'id' => 'kartik-modal',
                            'tabindex' => false // important for Select2 to work properly
                        ],
                      ]);
?>
  <div class="row">
   <?php $form = ActiveForm::begin([
        'action' => ['fatura-provisoria-pdf'],
        'method' => 'get',
        'options'=>['target'=>'_blank'],
    ]); ?>

    <div class="row">
    <div class="col-md-6">
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
      <div class="col-md-6">
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
     <div class="col-md-12">
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

     
      <div class="col-md-6">

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
      <div class="col-md-6">

    <?=$form->field($model, 'recebido')->widget(Select2::classname(), [
          'data' =>['naorecebido' => 'Não Recebido', 'recebido'=>'Recebido'],
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'RECEBIDO'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);

      ?>
    </div>

        <?= Html::submitButton('<i class="fa fa-file-pdf"> PDF </i>', ['class' => 'btn btn-primary']) ?>
       


    <?php ActiveForm::end(); ?>
</div>
   <?php Modal::end();?>
