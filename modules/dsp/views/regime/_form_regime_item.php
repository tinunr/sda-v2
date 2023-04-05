<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use app\modules\dsp\models\Unidade;
use app\modules\dsp\models\Regime;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>
        <?= Html::a('Voltar', ['view','id'=>$model->dsp_regime_id], ['class' => 'btn btn-warning']) ?>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

     
        <?= $form->field($model, 'dsp_regime_id')->textInput() ?>

        <?= $form->field($model, 'descricao')->textInput() ?>


        <?= $form->field($model, 'dsp_regime_parent_id')->widget(Select2::classname(), [
        'data'=>ArrayHelper::map(Regime::find()->all(), 'id', 'descricao'),
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => ' '],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label('Tabela Anexa');?>

        <?= $form->field($model, 'valor')->textInput() ?>

        <?= $form->field($model, 'dsp_item_unidade')->widget(Select2::classname(), [
        'data'=>ArrayHelper::map(Unidade::find()->all(), 'id', 'descricao'),
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => 'Unidade'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label('Unidade');?> 

        <?= $form->field($model, 'forma')->textInput() ?>

        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
  
    </div>
</section>
        

       

     
   
        



    <div class="form-group">
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>