<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\dsp\models\Person;
use app\modules\fin\models\DespesaTipo;
use app\models\Ano;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\db\Query;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
     <div class="col-md-2">
     <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
        'options' => ['placeholder' => 'Selecione ...'],
        'pluginOptions' => [
            'allowClear' => true,
            
        ],        
    ])->label(false);?> 
    </div>
    <div class="col-md-2">
     <?= $form->field($model, 'fin_despesa_tipo_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' => ArrayHelper::map(DespesaTipo::find()->orderBy('descricao')->all(), 'id', 'descricao'),
        'options' => ['placeholder' => 'TIPO'],
        'pluginOptions' => [
            'allowClear' => true,
            
        ],        
    ])->label(false);?> 
    </div>
    
    <div class="col-md-4">
     <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map((new Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_despesa A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => 'FORNECEDOR','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label(false);?>

    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'PR / FP / RCB'])->label(false) ?>
    </div>
    <div class="col-md-2">
      <?php echo $form->field($model, 'beginDate')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);?>
      </div>
      <div class="col-md-2">
      <?php echo $form->field($model, 'endDate')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);
    ?>
      </div>
    
   
        <?= Html::submitButton('<i class="fas fa-filter"></i>', ['class' => 'btn' ,'title'=>'FILTAR']) ?>
       
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

