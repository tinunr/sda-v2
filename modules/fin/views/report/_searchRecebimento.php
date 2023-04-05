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
                          'header' => 'RELATÓRIO - RECEBIMENTO',
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
        'action' => ['recebimento-pdf'],
        'method' => 'get',
         'options'=>['target'=>'_blank'],
    ]); ?>

     
    <div class="col-md-6">
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
                ]);?>
      </div>
      <div class="col-md-6">
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
                ]);
    ?>
      </div>
      <div class="col-md-4">
    <?= $form->field($model,'por_person')->inline()->checkBox() ?>
          
      </div>
    <div class="col-md-8">
        <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map((new Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_recebimento A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => '','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ]);?>

    </div>
    <div class="col-md-12">
          <?= $form->field($model, 'documento_id')->widget(Select2::classname(), [
         'data' => ArrayHelper::map(Documento::find()->where(['id'=>[Documento::FATURA_PROVISORIA_ID,Documento::RECEBIMENTO_ID]])->orderBy('descricao')->all(), 'id', 'descricao'),
          
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
              'allowClear' => FALSE,
          ],
      ]);?>
    </div>
   
        <?= Html::submitButton('<i class="fas fa-print"></i> Imprimir', ['class' => 'btn' ,'title'=>'IMPRIMIR']) ?>
        

    <?php ActiveForm::end(); ?>
</div>
   <?php Modal::end();?>
