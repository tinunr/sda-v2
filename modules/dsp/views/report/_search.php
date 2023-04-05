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
$initValueText = empty($model->user_id) ? '' : User::findOne($model->user_id)->name;
$initDspPersonId = empty($model->dsp_person_id) ? '' : Person::findOne($model->dsp_person_id)->nome;

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
              'header' => 'RELATÓRIO - PROCESSO',
              'toggleButton' => false,
              'size'=>Modal::SIZE_DEFAULT,
              'options' => [
                // 'id' => 'kartik-modal',
                'tabindex' => false // important for Select2 to work properly
            ],
          ]);?>
    <?php $form = ActiveForm::begin([
        'action' => ['report-pdf'],
        'method' => 'get',
        'options'=>['target'=>'_blank'],
    ]); ?>


    <div class="row">
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
    
        <div class="col-md-6">
          <?= $form->field($model, 'status')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(ProcessoStatus::find()->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => '','multiple' => true],
          'pluginOptions' => [
              'allowClear' => false,
              'tags' => true,
              'tokenSeparators' => [',', ' '],
          ],
      ]);?>
    </div>  
    
    <div class="col-md-6">
          <?= $form->field($model, 'comPl')->widget(Select2::classname(), [
          'data' =>[0=>'Com Pedido de Levantamento',1=>'Com PL e Por Resolver',2=>'Com PL e Resolvido'],
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
              'allowClear' => true,
          ],
      ])->label('Pedido de Levantamento');?>
    </div>  
        </div>
    <div class="col-md-3">
      
    <?= $form->field($model,'porResponsavel')->inline()->checkBox() ?>
  </div>

    <div class="col-md-9">

      <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
         'initValueText' => $initValueText,
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 0,
            'ajax' => [
                'url' => Url::to(['/user/user-list']),
                'dataType' => 'json',
            ],
        ],
        
    ]);?>
    </div>
     <div class="col-md-3">
      
    <?= $form->field($model,'porCliente')->inline()->checkBox() ?>
  </div>
    <div class="col-md-9">

    <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Person::find()->all(), 'id','nome'),
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => '','multiple' => false],
          'pluginOptions' => [
              'allowClear' => false,
              'tags' => true,
              'tokenSeparators' => [',', ' '],
          ],
      ]);?>
    </div>

    
        <?= Html::submitButton('<i class="fas fa-print"></i> Imprimir', ['class' => 'btn btn-primary']) ?>
        
        

    <?php ActiveForm::end(); ?>
      <?php Modal::end();?>


</section>