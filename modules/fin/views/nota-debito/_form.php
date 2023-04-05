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
    <div class="col-md-2">
        <?= $form->field($model, 'numero')->textInput(['readonly' => true]) ?> 
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

    <div class="col-md-2">
    <?= $form->field($model, 'valor')->textInput() ?>
    </div>
   
	    <?= $form->field($model, 'descricao')->textArea() ?>
	
       

     
   
        



    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>