<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\dsp\models\Person;
use app\models\Ano;
use app\models\User;
use app\modules\dsp\models\ProcessoStatus;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\Modal;

$this->title = 'Relatório filtors';


/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs("(function($) {
      $('#modal').modal();
  })(jQuery);", yii\web\View::POS_END, 'lang');
?>
<section>
<?php Modal::begin([
              'id'=>'modal',
              'header' => 'RELATÓRIO - PROCESSO REGISTADO',
              'toggleButton' => false,
              'size'=>Modal::SIZE_DEFAULT,
              'options' => [
                // 'id' => 'kartik-modal',
                'tabindex' => false // important for Select2 to work properly
            ],
          ]);?>
    <?php $form = ActiveForm::begin([
        'action' => ['pr-registrado-pdf'],
        'method' => 'get',
        'options'=>['target'=>'_blank'],
    ]); ?>

    <div class="row">
     <div class="col-md-4">
      <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Ano ...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);?> 
    </div>
      <div class="col-md-4">
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
      <div class="col-md-4">
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
  </div>
   
    <div class="col-md-12">
    <?= $form->field($model, 'dsp_desembaraco_id')->widget(Select2::classname(), [
          'data' =>['1'=>'CVST 1 ','2'=>'CVST 2'],
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
              'allowClear' => true,
          ],
      ])->label('Desembarco');?>
    </div>

    
        <?= Html::submitButton('<i class="fas fa-print"></i> Imprimir', ['class' => 'btn btn-primary']) ?>
        
        

    <?php ActiveForm::end(); ?>
      <?php Modal::end();?>


</section>