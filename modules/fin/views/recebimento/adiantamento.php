<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use conquer\select2\Select2Widget;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;

use app\modules\dsp\models\Regime;
use app\modules\dsp\models\RegimeItem;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\Nord;
use app\modules\dsp\models\Item;
use app\modules\fin\models\Banco;
use app\modules\fin\models\RecebimentoItem;

use app\modules\fin\models\DocumentoPagamento;




/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Novo Recebimento | ADIANTAMENTO';
$this->registerJsFile(Url::to('@web/js/formaReceber.js'),['position' => \yii\web\View::POS_END]);

?>
<section>

     <?php  echo Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
<?= $form->errorSummary($model); ?>
    <div class="row">
    <div class="row">
    
     <div class="col-md-3">
    <?= $form->field($model, 'numero')->textInput(['id'=>'numero','readonly' => false]) ?>
        
    </div>
     <div class="col-md-3">
     <?php echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);
    ?>
     </div>


    <div class="col-md-6">
 <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_person_id'],
        'pluginOptions' => [
            #'allowClear' => true,
            'desable' => true,
        ],
        
    ]);?>
        
    </div>


</div>

  <div class="row">
       <div class="col-md-4">
        <?= $form->field($model, 'fin_documento_pagamento_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map(DocumentoPagamento::find()->orderBy('descricao')->all(), 'id', 'descricao'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'fin_documento_pagamento_id','onchange'=>'disableFields(this)'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label('Forma de Receber');?>
        
    </div>
    <div class="col-md-4">
         <?=$form->field($model, 'fin_banco_id')->widget(DepDrop::classname(), [
            'type'=>DepDrop::TYPE_SELECT2,
            'options' => ['id'=>'fin_banco_id','onchange'=>'disableFields(this)'],
            'pluginOptions'=>[
                'depends'=>['fin_documento_pagamento_id'],
                'allowClear' => false,
                'placeholder' => 'Select...',
                'url' => Url::to(['/fin/banco/banco-list'])
            ]
        ]);?>         
    </div>
     <div class="col-md-4">
        <?=$form->field($model, 'fin_banco_conta_id')->widget(DepDrop::classname(), [
            'type'=>DepDrop::TYPE_SELECT2,
            'options' => ['id'=>'fin_banco_conta_id','onchange'=>'disableFields(this)'],
            'pluginOptions'=>[
                'depends'=>['fin_banco_id'],
                'allowClear' => true,
                'placeholder' => 'Select...',
                'url' => Url::to(['/fin/banco-conta/conta-list'])
            ]
        ]);?> 
    </div>
</div>
    <div class="row">
    <div class="col-md-6">
    <?= $form->field($model, 'numero_documento')->textInput(['id'=>'numero_documento']) ?>
    </div>
     <div class="col-md-6">
     <?php echo $form->field($model, 'data_documento')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['id'=>'data_documento'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);
    ?>
     </div>
  </div> 
     
    <?= $form->field($model, 'valor')->textInput(['id'=>'valor']) ?>
    <?= $form->field($model, 'descricao')->textArea(['id'=>'descricao']) ?>
 
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>



