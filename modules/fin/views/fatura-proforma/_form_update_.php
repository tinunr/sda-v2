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
use yii\web\JsExpression;
use app\modules\dsp\models\Regime;
use app\modules\dsp\models\RegimeItem;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\Nord;
use app\modules\dsp\models\Item;

$this->registerJsFile(Url::to('@web/js/nordDataUpdate.js'),['position' => \yii\web\View::POS_END]);

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */


?>
<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
<?= $form->errorSummary($model); ?>
<?= $form->errorSummary($modelsFaturaProvisoriaItem); ?>
    <div class="row">
    <div class="row">
    
     <div class="col-md-4">
    <?= $form->field($model, 'numero')->textInput(['id'=>'numero','readonly' => false]) ?>
        
    </div>
     <div class="col-md-4">
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
    <div class="col-md-4">  
    <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new yii\db\Query)->from('dsp_processo')->select(['id', new \yii\db\Expression("CONCAT(`numero`, '/', `bas_ano_id`) as nome")])->orderBy('numero')->all(), 'id','nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_processo_id','onchange'=>'getNordData(this)'],
        'pluginOptions' => [
            'allowClear' => true,
            /*'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/dsp/processo/ajax']),
                'dataType' => 'json',
            ],*/
        ],
        
    ]);?> 
    
        
    </div>
</div>
     
    
    </div>
    <div class="row">
     <div class="col-md-6">
     <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Person::find()->where(['is_cliente'=>1])->orderBy('nome')->all(), 'id', 'nome'),
        'options' => ['placeholder' => 'Selecione cliente ...','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
            /*'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/dsp/person/person-list']),
                'dataType' => 'json',
            ],*/
        ],
        
    ]);?>
 </div>
<div class="col-md-6">
<?= $form->field($model, 'nord')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => 'Selecione N.O.R.D ...','id'=>'nord'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/dsp/nord/ajax']),
                'dataType' => 'json',
            ],
        ],
        
    ]);?> 
</div>
</div>

 
    <?= $form->field($model, 'mercadoria')->textArea(['id'=>'mercadoria','placeholder' => 'Descrição / Mrecadoria','maxlength' => true]) ?>

    <div class="row">
<div class="titulo-principal"> <h5 class="titulo">Regime</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

  <div class="col-md-2">
    <?=$form->field($model, 'dsp_regime_id')->textInput(['id'=>'dsp_regime_id']) ?>
   
  </div>
  <div class="col-md-10">
    <?= $form->field($model, 'dsp_regime_descricao')->textInput(['id'=>'dsp_regime_descricao']) ?>
  
  </div>
  </div>  



<div class="row">
<div class="row">
    <div class="col-md-6">
        <?=$form->field($model, 'dsp_regime_item_id')->widget(DepDrop::classname(), [
         'type'=>DepDrop::TYPE_SELECT2,
            'options'=>['id'=>'dsp_regime_item_id','onchange'=>'getRegimeData(this)'],
            'data'=>ArrayHelper::map(RegimeItem::find()->where(['id'=>$model->dsp_regime_item_id])->all(), 'id', 'descricao'),
            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
            'pluginOptions'=>[
                'depends'=>['dsp_processo_id'],
                'placeholder'=>'Selecione ...',
                'url'=>Url::to(['/dsp/regime/regime-item']),
                 'initialize'=>true,

            ]
        ]);
        ?>   
 
    </div>
    <div class="col-md-6">
    <?= $form->field($model, 'dsp_regime_item_valor')->textInput(['id'=>'dsp_regime_item_valor']) ?>
    
    </div>
    </div>
<div class="row">

     <div class="col-md-6">
     <?=$form->field($model, 'dsp_regime_item_tabela_anexa')->widget(DepDrop::classname(), [
         'type'=>DepDrop::TYPE_SELECT2,
            'options'=>['id'=>'dsp_regime_item_tabela_anexa','onchange'=>'getRegimeData(this)'],
            'data'=>ArrayHelper::map(RegimeItem::find()->where(['id'=>$model->dsp_regime_item_tabela_anexa])->all(), 'id', 'descricao'),
            'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
            'pluginOptions'=>[
                'depends'=>['dsp_regime_item_id'],
                'placeholder'=>'Selecione ...',
                'url'=>Url::to(['/dsp/regime/regime-item-table']),
                 'initialize'=>true,

            ]
        ]);
        ?> 

    </div>
    <div class="col-md-6">
    <?= $form->field($model, 'dsp_regime_item_tabela_anexa_valor')->textInput(['id'=>'dsp_regime_item_tabela_anexa_valor']) ?>
    
    </div>
   </div> 
</div>


 <div class="row">
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
 <div class="row">

    <div class="col-md-2">

    <?= $form->field($model, 'impresso_principal')->textInput(['id'=>'impresso_principal','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'impresso_intercalar')->textInput(['id'=>'impresso_intercalar','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'pl')->textInput(['id'=>'pl','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'gti')->textInput(['id'=>'gti','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'dv')->textInput(['id'=>'dv','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'tce')->textInput(['id'=>'tce','onchange'=>'getRegimeData(this)']) ?>
</div>
</div>
<div class="row">
 <div class="col-md-2">

    <?= $form->field($model, 'tn')->textInput(['id'=>'tn','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'fotocopias')->textInput(['id'=>'fotocopias','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'form')->textInput(['id'=>'form','onchange'=>'getRegimeData(this)']) ?>
</div>

    <div class="col-md-2">
    <?= $form->field($model, 'regime_normal')->textInput(['id'=>'regime_normal','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'regime_especial')->textInput(['id'=>'regime_especial','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-2">
    <?= $form->field($model, 'impresso_intercalar')->textInput(['id'=>'impresso_intercalar','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-3">
    <?= $form->field($model, 'exprevio_comercial')->textInput(['id'=>'exprevio_comercial','onchange'=>'getRegimeData(this)']) ?>
</div>
    <div class="col-md-3">
    <?= $form->field($model, 'expedente_matricula')->textInput(['id'=>'expedente_matricula','onchange'=>'getRegimeData(this)']) ?>
</div>
<div class="col-md-2">
    <?= $form->field($model, 'taxa_comunicaco')->textInput(['id'=>'taxa_comunicaco','onchange'=>'getRegimeData(this)']) ?>
</div>
</div>
 </div>




<div class="titulo-principal"> <h5 class="titulo">Taxas</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
<table class="container-items table table-hover">
  <thead>
    <tr>
      <th scope="col" style="width: 40px">#</th>
      <th scope="col" style="text-align: left;">ITEM</th>
      <th  style="width: 250px" scope="col">VALOR</th>
      <th  style="width: 50px" scope="col">OR.</th>
    </tr>
  </thead> 
</table>

<div class="row">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 50, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsFaturaProvisoriaItem[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                    'dsp_item_id',
                    'valor',
                    'item_origem_id',
                ],
            ]); ?>

                <table class="container-items table table-hover">

                
                 <?php foreach ($modelsFaturaProvisoriaItem as $i => $item): ?>                    
                <?php if(empty($item->item_origem_id)){
                    $item->item_origem_id ='M';
                 }?> 
                    

                        <tr class="item" >
                            
                          <td>
                            <button type="button"  id="removeitem" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            <?php
                                if (! $item->isNewRecord) {
                                    echo Html::activeHiddenInput($item, "[{$i}]id");
                                }
                            ?>
                          </td>                        
                          <td>
                            <?=$form->field($item, "[{$i}]dsp_item_id")->dropDownList(
                                ArrayHelper::map(Item::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                            ['prompt'=>'Selecione ...']
                            )->label(false);?>

                          
                          </td>
                          <td style="width: 250px" > <?= $form->field($item, "[{$i}]valor")->textInput(['maxlength' => true])->label(false) ?> </td>
                          <td style="width: 50px" > <?= $form->field($item, "[{$i}]item_origem_id")->textInput(['readonly'=>false])->label(false) ?> </td>
                        </tr>
            <?php endforeach; ?>

            </table>
            <button id="add-item" onclick="addNullItem();" value=<?=$i?> type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
            <?=Html::hiddenInput('hidemCount', $i,['id'=>'hidemCount','class' => 'form-control']);?>
            <?php DynamicFormWidget::end(); ?>
    </div>


  <table class="container-items table table-hover">
  <thead>
    <tr>
      <th scope="col" style="width: 40px">#</th>
      <th scope="col" >TOTAL</th>
      <th  style="width: 250px" scope="col"><?= $form->field($model, 'valor')->textInput(['id' => 'total','readonly'=>true])->label(false) ?></th>
      <th  style="width: 50px" scope="col">##</th>
    </tr>
  </thead> 
</table>


    <div class="form-group">
       <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
            'title'=>'SALVAR',
            'data' => [
                'confirm' => 'PRETENDE REALENTE SUBMETER O FORMULÁRIO?',
               ]
               ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>




<script type="text/javascript">
    function updateTotal() {
        var total = 0;
        var honorario = 0;
       
        
        if ($("#honorario").val()>0) {
            total = parseFloat(total) + parseFloat($("#honorario").val());
            honorario = $("#honorario").val();
        }
        if ($("#iva_honorario").val()>0) {
            total = parseFloat(total) + parseFloat($("#iva_honorario").val());
        }

        for (var i = 0; i <= $('#hidemCount').val(); i++) {
        //alert($('#hidemCount').val()); faturaprovisoriaitem-8-valor
            if ($("#faturaprovisoriaitem-"+i+"-valor").val()>0) {
                var total = parseFloat(total) + parseFloat($("#faturaprovisoriaitem-"+i+"-valor").val());
            }
        }
        


            $("#total").val(total);



    }
    window.setInterval(updateTotal,10);

       

</script>








