<?php

use yii\helpers\Html;
use yii\helpers\url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>

<section>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->errorSummary($model); ?>

   <div class="row">
    
    <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new yii\db\Query)->from('dsp_processo')->select(['id', new \yii\db\Expression("CONCAT(`numero`, '/', `bas_ano_id`) as nome")])->orderBy('numero')->all(), 'id','nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_processo_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ]);?> 
        
        <?=$form->field($model, 'dsp_fatura_provisoria_id')->widget(DepDrop::classname(), [
            'type'=>DepDrop::TYPE_SELECT2,
            'options' => ['id'=>'dsp_fatura_provisoria_id'],
            'pluginOptions'=>[
                'depends'=>['dsp_processo_id'],
                'initialize' => true,
                'allowClear' => true,
                'placeholder' => 'Select...',
                'url' => Url::to(['/fin/fatura-provisoria/list-processo'])
            ]
        ]);?> 
    </div>
     
    

    
 
   
        



    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>









