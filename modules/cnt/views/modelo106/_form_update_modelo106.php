<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>
<?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['view','id'=>$model->id], ['class'=>'btn btn-warning']) ?>
<div class="titulo-principal"> <h5 class="titulo">IDENTIFICAÇÂO</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'nif')->textInput(['placeholder'=>''])?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'cd_af')->textInput(['placeholder'=>''])?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'designacao_social')->textInput(['placeholder'=>''])?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'reparticao_financa')->textInput(['placeholder'=>''])?>
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
            <?= $form->field($model, 'nif_representante_legal')->textInput(['placeholder'=>''])?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'representante_legal')->textInput(['placeholder'=>''])?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'tecnico_conta_nif')->textInput(['placeholder'=>''])?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'tecnico_conta_nome')->textInput(['placeholder'=>''])?>
        </div>
        
        
    </div>



    <div class="row">
        <div class="col-md-4">
          <?= $form->field($model, 'tipo_entrega')->widget(Select2::classname(), [
            'theme' => Select2::THEME_BOOTSTRAP,
            'data' =>[
                '1'=>'No Prazo',
                '2'=>'Fora do Prazo',
                '3'=>'Subistituição',
            ],
            'options' => ['placeholder' => ''],
            'pluginOptions' => [
                'allowClear' => true,
                ],        
            ]);
            ?>             
        </div>   
        <div class="col-md-2">
            <?= $form->field($model,'doc_cliente')->inline()->radioList([ 1=>'Entregue','0'=>'Não Entregue',]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model,'doc_fornecedor')->inline()->radioList([ 1=>'Entregue','0'=>'Não Entregue',]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'doc_reg_cliente')->inline()->radioList([ 1=>'Entregue','0'=>'Não Entregue']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'doc_reg_fornecedor')->inline()->radioList([ 1=>'Entregue','0'=>'Não Entregue']) ?>
        </div>               

    </div>





    <div class="titulo-principal"> <h5 class="titulo">VALORES</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'valor_1')->textInput(['placeholder'=>''])?>
        </div>
            <div class="col-md-2">
            <?= $form->field($model, 'valor_2')->textInput(['placeholder'=>''])?>
        </div>
            <div class="col-md-2">
            <?= $form->field($model, 'valor_3')->textInput(['placeholder'=>''])?>
        </div>
            <div class="col-md-2">
            <?= $form->field($model, 'valor_4')->textInput(['placeholder'=>''])?>
        </div>
            <div class="col-md-2">
            <?= $form->field($model, 'valor_5')->textInput(['placeholder'=>''])?>
        </div>
            <div class="col-md-2">
            <?= $form->field($model, 'valor_6')->textInput(['placeholder'=>''])?>
        </div>
            <div class="col-md-2">
            <?= $form->field($model, 'valor_7')->textInput(['placeholder'=>''])?>
        </div>
            <div class="col-md-2">
            <?= $form->field($model, 'valor_8')->textInput(['placeholder'=>''])?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'valor_9')->textInput(['placeholder'=>''])?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'valor_10')->textInput(['placeholder'=>''])?>
        </div>
    </div>




    





    <div class="col-md-2">
        <?= $form->field($model, 'valor_11')->textInput(['placeholder'=>''])?>
        <?= $form->field($model, 'valor_11')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_12')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_13')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_14')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_15')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_16')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_17')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_18')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'valor_19')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'valor_20')->textInput(['placeholder'=>''])?>
    </div>





    <div class="col-md-2">
        <?= $form->field($model, 'valor_21')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_22')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_23')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_24')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_25')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_26')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_27')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_28')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'valor_29')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'valor_30')->textInput(['placeholder'=>''])?>
    </div>




    <div class="col-md-2">
        <?= $form->field($model, 'valor_31')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_32')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_33')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_34')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_35')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_36')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_37')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_38')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'valor_39')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'valor_40')->textInput(['placeholder'=>''])?>
    </div>





<div class="col-md-2">
        <?= $form->field($model, 'valor_41')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_42')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_43')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_44')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_45')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_46')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_47')->textInput(['placeholder'=>''])?>
    </div>
        <div class="col-md-2">
        <?= $form->field($model, 'valor_48')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'valor_49')->textInput(['placeholder'=>''])?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'valor_50')->textInput(['placeholder'=>''])?>
    </div>
     
        
        
 </div>
          


    <div class="row">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
</section>