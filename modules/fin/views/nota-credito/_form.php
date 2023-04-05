<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\Person;


/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model); ?>
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
	    'data' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
	    'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_person_id'],
	    'pluginOptions' => [
	        #'allowClear' => true,
	        #'disabled' => true,
	    ],        
    ]);?>
</div>
    <div class="col-md-4">
    <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new yii\db\Query)->from('dsp_processo')->select(['id', new \yii\db\Expression("CONCAT(`numero`, '/', `bas_ano_id`) as nome")])->orderBy('numero')->all(), 'id','nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_processo_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ]);?> 
        
    </div>
    <div class="col-md-4">
        <?=$form->field($model, 'fin_fatura_defenitiva_id')->widget(DepDrop::classname(), [
            'type'=>DepDrop::TYPE_SELECT2,
            'options' => ['id'=>'fin_fatura_defenitiva_id'],
            'pluginOptions'=>[
                'depends'=>['dsp_processo_id'],
                'initialize' => true,
                'allowClear' => true,
                'placeholder' => 'Select...',
                'url' => Url::to(['/fin/fatura-definitiva/list-processo'])
            ]
        ]);?> 
    </div>

    <div class="col-md-4">
    <?= $form->field($model, 'valor')->textInput() ?>
    </div>
   
	    <?= $form->field($model, 'descricao')->textArea() ?>
	
       

     
   
        



    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>