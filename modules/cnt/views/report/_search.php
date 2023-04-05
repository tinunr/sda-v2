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
$initValueText = empty($model->user_id) ? '' : User::findOne($model->user_id)->name;
$initDspPersonId = empty($model->dsp_person_id) ? '' : Person::findOne($model->dsp_person_id)->nome;

$this->title = 'Relatório filtors';


/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

    <?php $form = ActiveForm::begin([
        'action' => ['report-pdf'],
        'method' => 'get',
    ]); ?>


    <div class="row">
    <div class="row">
     <div class="col-md-2">
      <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Ano ...'],
          'pluginOptions' => [
              'allowClear' => false
          ],
      ]);?> 
    </div>
      <div class="col-md-5">
      <?php echo $form->field($model, 'dataInicio')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => 'Data de Inicio'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);?>
      </div>
      <div class="col-md-5">
      <?php echo $form->field($model, 'dataFim')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => 'Data de Fim'],
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
          'options' => ['placeholder' => 'Estado ...','multiple' => true],
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
          'options' => ['placeholder' => 'Pedidos de levantamentos'],
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
        'options' => ['placeholder' => 'Responsavel'],
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
         'initValueText' => $initDspPersonId,
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => 'Cliente ...',],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' =>3,
            'ajax' => [
                'url' => Url::to(['/dsp/person/person-list']),
                'dataType' => 'json',
            ],
        ],
        
    ]);?>
    </div>

    
        <?= Html::submitButton('<i class="fa fa-filter"></i> Gerar Relatório', ['class' => 'btn btn-primary']) ?>
        
        

    <?php ActiveForm::end(); ?>

</section>