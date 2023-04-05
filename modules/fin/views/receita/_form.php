<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;
use kartik\date\DatePicker;

use app\modules\dsp\models\Person;
use app\modules\fin\models\Banco;
use app\modules\fin\models\DocumentoPagamento;



/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin([
        'action' => ['create'],
        'method' => 'get',
    ]); ?>

    <?= $form->errorSummary($model); ?>
    <div class="row">     
     <div class="col-md-2">
    <?= $form->field($model, 'numero')->textInput(['id'=>'numero','readonly' => true]) ?>
        
      </div>
      <div class="col-md-3">
         <?= $form->field($model, 'data')->widget(DatePicker::classname(), [
		    'type' => DatePicker::TYPE_INPUT,
		    'options' => ['placeholder' => 'yyyy-mm-dd'],
		    'pluginOptions' => [
		         'autoclose'=>true,
		         'format' => 'yyyy-mm-dd'
		    ]
		]);?>
      </div>
       
      <div class="col-md-6">
    	 <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ]);?> 
	</div>
      <div class="col-md-1">
      	 <?= Html::submitButton('<i class="fa fa-filter"></i> ', ['class' => 'btn btn-primary create-recebimento']) ?>
      </div>
  </div>

  <div class="row">
  	 <div class="col-md-4">
    	<?=$form->field($model, 'fin_banco_id')->widget(Select2Widget::className(),
		    [
		        //'ajax' => ['/dsp/person/ajax'],
		        'items' => ArrayHelper::map(Banco::find()->orderBy('descricao')->all(), 'id', 'descricao'),
		        'options' => ['placeholder' => 'Select a state ...',
		            'id'=>'fin_banco_id',
		            //'onchange'=>'getNordData(this)'
		        ],

		    ]
		);?> 
	</div>
	 <div class="col-md-4">
    	<?=$form->field($model, 'fin_documento_pagamento_id')->widget(Select2Widget::className(),
		    [
		        //'ajax' => ['/dsp/person/ajax'],
		        'items' => ArrayHelper::map(DocumentoPagamento::find()->orderBy('descricao')->all(), 'id', 'descricao'),
		        'options' => ['placeholder' => 'Select a state ...',
		            'id'=>'fin_documento_pagamento_id',
		            //'onchange'=>'getNordData(this)'
		        ],

		    ]
		);?> 
	</div>
	<div class="col-md-4">
    <?= $form->field($model, 'numero_documento')->textInput(['id'=>'numero_documento']) ?>
		
	</div>
  </div>

 

    <?php ActiveForm::end(); ?>

</div>
</section>