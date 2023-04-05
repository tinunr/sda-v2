<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;

use app\modules\dsp\models\Person;
use app\modules\fin\models\Banco;
use app\modules\fin\models\BancoConta;
use app\modules\fin\models\DocumentoPagamento;
$this->registerJsFile(Url::to('@web/js/formaPagar.js'),['position' => \yii\web\View::POS_END]);
$this->registerJsFile(Url::to('@web/js/pagamento.js'),['position' => \yii\web\View::POS_END]);
$total_valor = 0;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
   
    <?= $form->errorSummary($model); ?> 
    <div class="row">     
     <div class="col-md-2">
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
       
      <div class="col-md-7">
    	<?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_person_id'],
        'pluginOptions' => [
            #'allowClear' => true,
            #'disabled' => true,
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
        
    ])->label('Forma de Pagar');?>
        
    </div>
    <div class="col-md-4">
         <?=$form->field($model, 'fin_banco_id')->widget(DepDrop::classname(), [
        // 'data' => ArrayHelper::map(Banco::find()->orderBy('descricao')->all(), 'id', 'descricao'),
            'type'=>DepDrop::TYPE_SELECT2,
            'options' => ['id'=>'fin_banco_id','onchange'=>'disableFields(this)'],
            'pluginOptions'=>[
                'depends'=>['fin_documento_pagamento_id'],
                'allowClear' => true,
                'placeholder' => 'Select...',
                'url' => Url::to(['/fin/banco/banco-list'])
            ]
        ]);?>         
    </div>
     <div class="col-md-4">
        <?=$form->field($model, 'fin_banco_conta_id')->widget(DepDrop::classname(), [
        // 'data' => ArrayHelper::map(BancoConta::find()->orderBy('numero')->all(), 'id', 'numero'),
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
 
 

    <?= $form->field($model, 'descricao')->textArea() ?>



<div class="row">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 300, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsDespesa[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'dsp_item_id',
                    'valor',
                    'ab',
                ],
            ]); ?>

            <input id ="item-control" type="hidden" name="_method" value=0 />

                <table class="container-items table table-hover">
                  <tr  >
                                                 
                          <th style="width: 50%" >Item</th>
                          <th >VALOR A PAGAR</th>
                          <th >VALOR</th>
                          <th >SALDO</th>
                        </tr>
                 <?php foreach ($modelsDespesa as $i => $item): ?>
               
                     <?php
                                echo Html::activeHiddenInput($item, "[{$i}]id");
                                $valor_pago_old =  $item->valor_pago;
                                $saldo_old =  $item->saldo;
                                $item->valor_pago = $item->saldo;
                                $item->valor = $item->saldo;
                                $item->saldo = $item->saldo - $item->valor_pago;
                                $total_valor = $item->valor + $total_valor;
                                $item->descricao = $item->numero.' - '.$item->descricao;
                        ?>

                        <tr class="item" >
                                     
                          <td style="width: 50%" ><?= $form->field($item, "[{$i}]descricao")->textInput(['id'=>'descricao','readonly'=> true])->label(false) ?></td>
                          <td ><?= $form->field($item, "[{$i}]valor")->textInput(['readonly'=> true])->label(false) ?></td>
                          <td >
                            <?= $form->field($item, "[{$i}]valor_pago")->textInput(['onchange'=>'updateData(this)'])->label(false) ?>
                            <?=Html::hiddenInput('valor_pago_old', $valor_pago_old,['id'=>'valor_pago_old','class' => 'form-control']);?>

                          </td>
                          <td >
                            <?= $form->field($item, "[{$i}]saldo")->textInput(['readonly'=> true])->label(false) ?>
                            <?=Html::hiddenInput('saldo_old', $saldo_old,['id'=>'saldo_old','class' => 'form-control']);?>

                         </td>
                        </tr>
                
            <?php endforeach; ?>
            <tr>
              <th>Total</th>
              <th><?= Html::textInput('total_valor',  $total_valor,['id'=>'total_valor','class' => 'form-control','readonly'=> true]) ?></th>

                                
              <th>
                <?= Html::textInput('total_valor_pagar',  $total_valor,['id'=>'total_valor_pagar','class' => 'form-control','readonly'=> true]) ?>
                <?=Html::hiddenInput('hidemCount', $i,['id'=>'hidemCount','class' => 'form-control']);?>
              </th>
                
              <th><?= Html::textInput('total_saldo',  0,['id'=>'total_saldo','class' => 'form-control','readonly'=> true]) ?></th>

            </tr>
            
            </table>


            <?php DynamicFormWidget::end(); ?>
    </div>


      	 <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' SALVAR', ['class' => 'btn btn-primary create-recebimento']) ?>
 

    <?php ActiveForm::end(); ?>

</div>
</section>