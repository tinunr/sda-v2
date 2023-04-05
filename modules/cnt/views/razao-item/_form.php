<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\web\JsExpression;

use app\modules\cnt\models\Diario;
use app\modules\cnt\models\Natureza;
use app\modules\cnt\models\PlanoTerceiro;
use app\modules\cnt\models\PlanoIva;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\cnt\models\Documento;
use app\modules\dsp\models\Person;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="col-md-2">
            <?= $form->field($model, 'numero')->textInput(); ?> 
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
    <div class="col-md-3">
    	<?= $form->field($model, 'cnt_diario_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new yii\db\Query)->from('cnt_diario')->select(['id', new \yii\db\Expression("CONCAT(`id`, '-', `descricao`) as nome")])->orderBy('descricao')->all(), 'id','nome'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ]);?>
    </div>
    <div class="col-md-3">
    	<?= $form->field($model, 'cnt_documento_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Documento::find()->orderBy('descricao')->all(), 'id','descricao'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ]);?>
    </div>
    <div class="col-md-2">
            <?= $form->field($model, 'valor')->textInput(); ?> 
    </div>
	<div class="col-md-12">
		<?= $form->field($model, 'descricao')->textArea(); ?> 
    </div>



 <div class="titulo-principal"> <h5 class="titulo">DETALHES DA FATURA</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<div class="row">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 25, // the maximum times, an element can be cloned (default 999)
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
                    <th >Documento</th>
                    <th style="width: 100px">Natureza</th>
                    <th >Plano Conta</th>
                    <th >Plano de Terceiro</th>
                    <th >Plano de Iva</th>
                    <th >Fluxo de Caixa</th>
                    <th style="width: 100px">Valor</th>
                </tr>          
                 <?php foreach ($modelsRazaoItem as $i => $item): ?>               
                    <?php
                            // necessary for update action.
                            if (! $item->isNewRecord) {
                                echo Html::activeHiddenInput($item, "[{$i}]id");
                            }
                        ?>

                        <tr class="item" >
                            
                         <td>
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                          </td>                        
                          <td>
                            <?=$form->field($item, "[{$i}]cnt_documento_id")->dropDownList(
                                ArrayHelper::map(Documento::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                            ['prompt'=>'']
                            )->label(false);?>
                          </td>
                           <td style="width: 100px">
                            <?=$form->field($item, "[{$i}]cnt_natureza_id")->dropDownList(
                                ArrayHelper::map(Natureza::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                            ['prompt'=>'']
                            )->label(false);?>                          
                          </td>
                          <td>
                            <?=$form->field($item, "[{$i}]cnt_plano_conta_id")->dropDownList(
                                ArrayHelper::map(PlanoConta::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                            ['prompt'=>'']
                            )->label(false);?>                          
                          </td>
                          <td>
                            <?=$form->field($item, "[{$i}]cnt_plano_terceiro_id")->dropDownList(
                                ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
                            ['prompt'=>'']
                            )->label(false);?>                          
                          </td>
                           <td>
                            <?=$form->field($item, "[{$i}]cnt_plano_iva_id")->dropDownList(
                                ArrayHelper::map(PlanoIva::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                            ['prompt'=>'']
                            )->label(false);?>                          
                          </td>
                          <td>
                            <?=$form->field($item, "[{$i}]cnt_plano_fluxo_caixa_id")->dropDownList(
                                ArrayHelper::map(PlanoFluxoCaixa::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                            ['prompt'=>'']
                            )->label(false);?>                          
                          </td>
                          <td style="width: 100px" > <?= $form->field($item, "[{$i}]valor")->textInput(['readonly'=>false])->label(false) ?>
                          </td>
                      </tr>
                
            <?php endforeach; ?>
            </table>

           <button id="add-item" type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
            <?php DynamicFormWidget::end(); ?>
    </div>

            





   



    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' SALVAR', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
