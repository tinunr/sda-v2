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
$this->registerJsFile(Url::to('@web/src/fin/of_accounts_c.js'),['position' => \yii\web\View::POS_END]);
$total_valor = 0;
$this->title = 'Encontro de Conta - A PARTIR DA DESPESA';

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>
<?php  echo Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['create-b'], ['class' => 'btn btn-warning']) ?>

<div class="row">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= $form->errorSummary($model); ?>
    <div class="row">     
     <div class="col-md-2">
    <?= $form->field($model, 'numero')->textInput(['id'=>'numero','readonly' => false]) ?>
     </div>
     <div class="col-md-1">
    <?= $form->field($model, 'fin_despesa_id')->textInput(['readonly' => true]) ?>
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
            #'disabled' => true,
        ],
        
    ]);?>
	</div>
    
  </div>


    <?= $form->field($model, 'descricao')->textArea() ?>



<div class="row">
  <table class="table table-hover">
    <tr>
      <td>VALOR</td>
      <td>VALOR A RECEBER</td>
      <td>SALDO</td>
    </tr>
    <tr>
      <td>
        <?= Html::textInput('valor_old',  $model->valor,['id'=>'valor_old','class' => 'form-control','readonly'=> true]) ?>
        </td>
      <td>    
        <?= $form->field($model, 'valor')->textInput(['id'=>'valor','readonly'=> true])->label(false) ?>
</td>
      <td><?= Html::textInput('valor_saldo',  $model->valor,['id'=>'valor_saldo','class' => 'form-control','readonly'=> true]) ?></td>
    </tr>
  </table>
     
</div>
 
</div>
     


<div class="row">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 25, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsNotaDebito[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                    'valor',
                    'valor_pago',
                    'saldo',
                ],
            ]); ?>

            <input id ="item-control" type="hidden" name="_method" value=0 />

                <table class="container-items table table-hover">
                  <tr  >
                                                 
                          <th style="width: 50%" >NOTA DE DEBITO</th>
                          <th >#</th>
                          <th >VALOR</th>
                          <th >VALOR A PAGAR</th>
                          <th >SALDO</th>
                        </tr>
                 <?php foreach ($modelsNotaDebito as $i => $item): ?>
               
                     

                        <tr class="item" >
                        <?php
                                echo Html::activeHiddenInput($item, "[{$i}]id");
                                $valor_pago_old =  $item->valor_pago;
                                $saldo_old =  $item->saldo;
                                $item->valor_pago = 0;
                                $item->valor = $item->saldo;
                                $item->saldo = $item->saldo - $item->valor_pago;
                                $total_valor = $item->valor + $total_valor;
                        ?>
                                     
                          <td style="width: 50%" ><?= $form->field($item, "[{$i}]descricao")->textInput(['id'=>'descricao','readonly'=> true])->label(false) ?></td>
                          <td style="padding: 5px">
                           <?= $form->field($item, "[{$i}]selecionado")->checkbox(['onchange'=>'updateData(this)'])->label(''); ?>
                          </td>
                          <td ><?= $form->field($item, "[{$i}]valor")->textInput(['readonly'=> true])->label(false) ?></td>
                          <td >
                            <?= $form->field($item, "[{$i}]valor_pago")->textInput(['onchange'=>'updateData(this)','disabled'=>true])->label(false) ?>
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
              <th></th>
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


      	 <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' SALVAR', ['class' => 'btn btn-primary create-recebimento','id'=>'save','style' => ['display' => 'none']]) ?>
 

    <?php ActiveForm::end(); ?>

</div>
</section>