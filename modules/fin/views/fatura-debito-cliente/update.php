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

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>

<section>
     <?php  echo Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['view','id'=>$model->id], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>

<div class="row">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
 <div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'numero')->textInput(['readonly' => false]) ?> 
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
         'data' =>ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
        'options' => ['placeholder' => 'Selecione cliente ...','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => false,
            /*'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/dsp/person/person-list']),
                'dataType' => 'json',
            ],*/
        ],
        
    ]);?>
    </div>
   
 
    
     <div class="col-md-3" id="div-n_receita">
        <?= $form->field($model, 'n_receita')->textInput(['id'=>'n_receita','readonly'=>false]) ?> 
    </div>

    <div class="col-md-3" id="div-data_receita">
    <?php echo $form->field($model, 'data_receita')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options'=>['id'=>'data_receita'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);
    ?>
    </div>

    <div class="col-md-3" id="div-impresso_principal">
        <?= $form->field($model, 'impresso_principal')->textInput(['id'=>'impresso_principal','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-impresso_intercalar">
        <?= $form->field($model, 'impresso_intercalar')->textInput(['id'=>'impresso_intercalar','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-pl">
        <?= $form->field($model, 'pl')->textInput(['id'=>'pl','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-gti">
        <?= $form->field($model, 'gti')->textInput(['id'=>'gti','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-tce">
        <?= $form->field($model, 'tce')->textInput(['id'=>'tce','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-tn">
        <?= $form->field($model, 'tn')->textInput(['id'=>'tn','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-form">
        <?= $form->field($model, 'form')->textInput(['id'=>'form','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-regime_normal">
        <?= $form->field($model, 'regime_normal')->textInput(['id'=>'regime_normal','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-regime_especial">
        <?= $form->field($model, 'regime_especial')->textInput(['id'=>'regime_especial','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-exprevio_comercial">
        <?= $form->field($model, 'exprevio_comercial')->textInput(['id'=>'exprevio_comercial','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-expedente_matricula">
        <?= $form->field($model, 'expedente_matricula')->textInput(['id'=>'expedente_matricula','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-taxa_comunicaco">
        <?= $form->field($model, 'taxa_comunicaco')->textInput(['id'=>'taxa_comunicaco','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-dv">
        <?= $form->field($model, 'dv')->textInput(['id'=>'dv','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-fotocopias">
        <?= $form->field($model, 'fotocopias')->textInput(['id'=>'fotocopias','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-qt_estampilhas">
        <?= $form->field($model, 'qt_estampilhas')->textInput(['id'=>'qt_estampilhas','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3">
    <?= $form->field($model, 'acrescimo')->textInput(['id'=>'acrescimo']) ?>
</div>
<div class="col-md-3" id="div-posicao_tabela">
        <?= $form->field($model, 'posicao_tabela')->textInput(['id'=>'posicao_tabela','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-dsp_regime_item_valor">
        <?= $form->field($model, 'dsp_regime_item_valor')->textInput(['id'=>'dsp_regime_item_valor','readonly'=>false]) ?> 
    </div>
    <div class="col-md-3" id="div-dsp_regime_item_tabela_anexa_valor">
        <?= $form->field($model, 'dsp_regime_item_tabela_anexa_valor')->textInput(['id'=>'dsp_regime_item_tabela_anexa_valor','readonly'=>false]) ?> 
    </div>

    </div>


    
    <?= $form->field($model, 'descricao')->textArea(['id'=>'descricao']) ?> 
 
 




   



    <div class="form-group">
    <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','title'=>'SALVAR','id'=>'save']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>