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
use app\modules\fin\models\Recebimento;
use app\modules\fin\models\RecebimentoItem;

use app\modules\fin\models\DocumentoPagamento;




/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'REEMBOLSO';
$this->registerJsFile(Url::to('@web/js/formaReceber.js'),['position' => \yii\web\View::POS_END]);
$this->registerJsFile(Url::to('@web/js/fin_recebimento_reembolso_form.js'),['position' => \yii\web\View::POS_END]);

?>
<section>

     <?php  echo Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
<?= $form->errorSummary($model); ?>
    <div class="row">
    <div class="row">
    
     <div class="col-md-2">
    <?= $form->field($model, 'numero')->textInput(['id'=>'numero','readonly' => false]) ?>
        
    </div>
     <div class="col-md-2">
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
     <div class="col-md-2"> 
         <?= $form->field($model, 'tipo_reembolco')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>[Recebimento::REEMBOLCO_AFAVOR_CLIENTE=>'Reeembolço a favor do cliente', Recebimento::REEMBOLCO_AFAVOR_AGENCIA=>'Reeembolço a favor da agência'],
        'options' => ['placeholder' => ' ' ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label('Tipo e reembolso');?> 
     </div>
     <div class="col-md-2">
    <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new yii\db\Query)->from('dsp_processo')->select(['id', new \yii\db\Expression("CONCAT(`numero`, '/', `bas_ano_id`) as nome")])->orderBy('numero')->all(), 'id','nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_processo_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Processo');?> 
        
    </div>
    <div class="col-md-2">
        <?=$form->field($model, 'dsp_fatura_provisoria_id')->widget(DepDrop::classname(), [
            'type'=>DepDrop::TYPE_SELECT2,
            'options' => ['id'=>'fin_fatura_defenitiva_id'],
            'pluginOptions'=>[
                'depends'=>['dsp_processo_id'],
                'initialize' => true,
                'allowClear' => true,
                'placeholder' => 'Select...',
                'url' => Url::to(['/fin/fatura-provisoria/list-processo'])
            ]
        ])->label('Fatura Provisória');?> 
    </div>


    <div class="col-md-12">
 <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
            'desable' => false,
        ],
        
    ])->label('Entidade que Devolve');?>
        
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
                'allowClear' => true,
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
    <?= $form->field($model, 'descricao')->textArea(['id'=>'descricao']) ?>
 
    </div>


     <div class="titulo-principal"> <h5 class="titulo">Itens</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<div class="row">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 25, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsRecebimentoItem[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'descricao_item',
                    'valor',
                ],
            ]); ?>

            <input id ="item-control" type="hidden" name="_method" value=0 />

                <table class="container-items table table-hover">
                 <?php foreach ($modelsRecebimentoItem as $i => $item): ?>

                        <tr class="item" >
                            
                          <td style="width: 50px">
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            <?php
                            // necessary for update action.
                            if (! $item->isNewRecord) {
                                echo Html::activeHiddenInput($item, "[{$i}]id");
                            }
                        ?>
                          </td>                        
                          <td>
                          <?=$form->field($item, "[{$i}]dsp_item_id")->dropDownList(
                            ArrayHelper::map(Item::find()->where(['dsp_item_type_id'=>1])->orderBy('descricao')->all(), 'id', 'descricao'),
                        ['prompt'=>' ']
                        )->label(false);?>
                          </td>
                          <td style="width: 250px" > <?= $form->field($item, "[{$i}]valor")->textInput(['maxlength' => true])->label(false) ?></td>
                        </tr>
                
            <?php endforeach; ?>
            
            
            </table>
             <table class="table table-hover">
                <tr>
                    <th >Total</th>
                    <th style="width: 72%"></th>
                    <th>
                        <?= $form->field($model, 'valor')->textInput(['id'=>'valor','readonly' => true])->label(false) ?>
                    </th>
            </tr>
        </table>
            <button id="add-item" value=<?=$i?> type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>

            <?php DynamicFormWidget::end(); ?>
    </div>
 
  





    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>



<script type="text/javascript">
    function updateTotal() {
        var valor = 0
        for (var i = 0; i <= 25; i++) {
            if ($("#recebimentoitem-"+i+"-valor").val()>0) {
                var valor = parseFloat(valor) + parseFloat($("#recebimentoitem-"+i+"-valor").val());
            }
        }
        $("#valor").val(valor);
    }
    window.setInterval(updateTotal,1000);
</script>



