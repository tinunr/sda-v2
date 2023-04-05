<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\web\JsExpression;

use app\models\Ano;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\Natureza;
use app\modules\cnt\models\PlanoTerceiro;
use app\modules\cnt\models\PlanoIva;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\cnt\models\Documento;
use app\modules\dsp\models\Person;
use app\modules\cnt\models\PlanoContaTipo;
$this->registerJsFile(Url::to('@web/js/cnt_razao_create.js'),['position' => \yii\web\View::POS_END]);
$this->registerCss("
.select2-container--open .select2-dropdown--below {
    border-top: solid 1px #66afe9;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    position: fixed;
    top: 30%;
    left: 30%;
    width: 600px !important;
    min-height:250px !important;
    background: #3c5462 !important;
    color: #FFF;
}
.select2-container--bootstrap .select2-dropdown--above{
    border-top: solid 1px #66afe9;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    position: fixed;
    top: 30%;
    left: 30%;
    width: 600px !important;
    min-height:250px !important;
    background: #3c5462 !important;
    color: #FFF;
}");
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="row">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => true,
        'validateOnBlur' => false,
        'options' => [
            'enctype' => 'multipart/form-data',
            'id' => 'dynamic-form'
        ]
    ]); ?>
    
    
     <div class="col-md-3">
        <?= $form->field($model, 'cnt_documento_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Documento::find()->where(['cnt_documento_tipo_id'=>1])->orderBy('descricao')->all(), 'id','descricao'),
        'options' => ['placeholder' => '','id'=>'cnt_documento_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ]);?>
    </div>

    <div class="col-md-3">
         <?=$form->field($model, 'documento_origem_id')->widget(DepDrop::classname(), [
            'type'=>DepDrop::TYPE_SELECT2,
            'options' => ['id'=>'documento_origem_id','onchange'=>'updateForm(this)'],
            'pluginOptions'=>[
                'depends'=>['cnt_documento_id'],
                'allowClear' => true,
                'initialize' => $model->isNewRecord ? false : true,
                'placeholder' => 'Select...',
                'url' => Url::to(['/cnt/documento/origem'])
            ]
        ]);?>   
    </div>
    <div class="col-md-2">
     <?php echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['id'=>'data','onchange'=>'getNumeroLancamento(this)'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);
    ?>
     </div>
     <div class="col-md-2">
        <?= $form->field($model, 'bas_mes_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new yii\db\Query)->from('bas_mes')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as nome")])->orderBy('id')->all(), 'id','nome'),
        'options' => ['placeholder' => '','id'=>'bas_mes_id','onchange'=>'getNumeroLancamento(this)'],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ]);?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Ano::find()->orderBy('id')->all(), 'id','ano'),
        'options' => ['placeholder' => '','id'=>'bas_ano_id','onchange'=>'getNumeroLancamento(this)'],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ]);?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'cnt_diario_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new yii\db\Query)->from('cnt_diario')->select(['id', new \yii\db\Expression("CONCAT(`id`, '-', `descricao`) as nome")])->orderBy('descricao')->all(), 'id','nome'),
        'options' => ['placeholder' => '','id'=>'cnt_diario_id','onchange'=>'getNumeroLancamento(this)'],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ]);?>
    </div>
     <div class="col-md-3">
            <?= $form->field($model, 'numero')->textInput(['id'=>'numero','readonly'=>true]); ?> 
        
    </div>


   
    <div class="col-md-3">
            <?= $form->field($model, 'valor_debito')->textInput(['id'=>'valor_debito','readonly'=>true]); ?> 
    </div>
    <div class="col-md-3">
            <?= $form->field($model, 'valor_credito')->textInput(['id'=>'valor_credito','readonly'=>true]); ?> 
    </div>
    <div class="row">
        <?= $form->field($model, 'descricao')->textArea(['id'=>'descricao']); ?> 
    </div>



 <div class="titulo-principal"> <h5 class="titulo">DETELHES</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<div class="row">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 100, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsRazaoItem[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'dsp_item_id',
                    'valor',
                    'ab',
                ],
            ]); ?>
            <input id ="item-control" type="hidden" name="_method" value=0 />


                <table class="container-items table table-hover">   
                 <tr>
                    <th >#</th>
                    <th >Descrição</th>
                    <th style="width: 50px">D/C</th>
                    <th style="width: 120px">Conta</th>
                    <th style="width: 120px">Terceiro</th>
                    <th style="width: 120px">IVA</th>
                    <th style="width: 120px">Caixa</th>
                    <th style="width: 150px">Valor</th>
                </tr>           
                 <?php foreach ($modelsRazaoItem as $i => $item): ?>               
                    

                        <tr class="item" >
                            
                         <td>
                            <button type="button"  id="removeitem" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            <?php
                                // necessary for update action.
                                if (! $item->isNewRecord) {
                                    echo Html::activeHiddenInput($item, "[{$i}]id");
                                }
                            ?>
                          </td>                        
                           
                          <td>
                            <?=$form->field($item, "[{$i}]descricao")->textInput()->label(false);?>                          
                          </td>
                          <td style="width: 50px">
                            <?=$form->field($item, "[{$i}]cnt_natureza_id")->textInput()->label(false);?>                         
                          </td>
                          <td>
                            <?= $form->field($item, "[{$i}]cnt_plano_conta_id")->widget(Select2::classname(), [
                                 'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => ['placeholder' => ''],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' =>1,
                                    'ajax' => [
                                        'url' => Url::to(['/cnt/plano-conta/json-list']),
                                        'dataType' => 'json',
                                    ],
                                    'templateResult' => new JsExpression('function(cnt_plano_conta_id) { return cnt_plano_conta_id.text; }'),
                                    'templateSelection' => new JsExpression('function (cnt_plano_conta_id) { return cnt_plano_conta_id.id; }'),
                                ],  
                            ])->label(false);?>                          
                          </td>
                          <td>
                            <?= $form->field($item, "[{$i}]cnt_plano_terceiro_id")->widget(Select2::classname(), [
                                 'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => ['placeholder' => ''],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' =>1,
                                    'ajax' => [
                                        'url' => Url::to(['/cnt/plano-terceiro/json-list']),
                                        'dataType' => 'json',
                                    ],
                                    'templateResult' => new JsExpression('function(cnt_plano_terceiro_id) { return cnt_plano_terceiro_id.text; }'),
                                    'templateSelection' => new JsExpression('function (cnt_plano_terceiro_id) { return cnt_plano_terceiro_id.id; }'),
                                ],  
                            ])->label(false);?>                          
                          </td>
                           <td>
                      
                            <?= $form->field($item, "[{$i}]cnt_plano_iva_id")->widget(Select2::classname(), [
                                 'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => ['placeholder' => ''],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' =>1,
                                    'ajax' => [
                                        'url' => Url::to(['/cnt/plano-iva/json-list']),
                                        'dataType' => 'json',
                                    ],
                                    'templateResult' => new JsExpression('function(cnt_plano_iva_id) { return cnt_plano_iva_id.text; }'),
                                    'templateSelection' => new JsExpression('function (cnt_plano_iva_id) { return cnt_plano_iva_id.id; }'),
                                ],  
                            ])->label(false);?>                            
                          </td>
                          <td> 
                             <?= $form->field($item, "[{$i}]cnt_plano_fluxo_caixa_id")->widget(Select2::classname(), [
                                 'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => ['placeholder' => ''],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' =>1,
                                    'ajax' => [
                                        'url' => Url::to(['/cnt/plano-fluxo-caixa/json-list']),
                                        'dataType' => 'json',
                                    ],
                                    'templateResult' => new JsExpression('function(cnt_plano_fluxo_caixa_id) { return cnt_plano_fluxo_caixa_id.text; }'),
                                    'templateSelection' => new JsExpression('function (cnt_plano_fluxo_caixa_id) { return cnt_plano_fluxo_caixa_id.id; }'),
                                ],  
                            ])->label(false);?>                          
                          </td>
                          <td > <?= $form->field($item, "[{$i}]valor")->textInput(['readonly'=>false])->label(false) ?>
                          </td>
                      </tr>
                
            <?php endforeach; ?>
            </table>

          <button id="additem" value="<?=$i?>" type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
          
          <?=Html::hiddenInput('hidemCount', $i,['id'=>'hidemCount','class' => 'form-control']);?>
            <?php DynamicFormWidget::end(); ?>
    </div>

            





   



    <div class="form-group row">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' SALVAR', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<script type="text/javascript">
    function updateTotal() {
        var total_debito = 0;
        var total_credito = 0;
        for (var i = 0; i <= 1000; i++) {
            if ($("#razaoitem-"+i+"-cnt_natureza_id").val()=='C') {
                var total_credito = parseFloat(total_credito) + parseFloat($("#razaoitem-"+i+"-valor").val());
            }
            if ($("#razaoitem-"+i+"-cnt_natureza_id").val()=='D') {
                var total_debito = parseFloat(total_debito) + parseFloat($("#razaoitem-"+i+"-valor").val());
            }
        }
        $("#valor_debito").val(total_debito);
        $("#valor_credito").val(total_credito);
    }
    window.setInterval(updateTotal,1000);
</script>