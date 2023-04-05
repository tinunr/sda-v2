<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Ano;
use app\modules\dsp\models\Person;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\db\Query;

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
     <?= $form->field($model, 'fin_fatura_definitiva_serie')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' => [
		 1=>'FD Normal',
		 2=>'FD Especial'],
        'options' => ['placeholder' => 'Serie'],
        'pluginOptions' => [
            'allowClear' => true,
            
        ],
        
    ])->label(false);?> 
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'send')->widget(Select2::classname(), [
            'theme' => Select2::THEME_BOOTSTRAP,
            'data' => ['0' => 'Por Validar', '1' => 'Validado'],
            'options' => ['placeholder' => 'Validado'],
            'pluginOptions' => [
                'allowClear' => true,

            ],

        ])->label(false); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'FD / PR / NORD'])->label(false) ?>
    </div>
     <div class="col-md-5">
        <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map((new Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_fatura_provisoria A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => 'CLIENTE','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label(false);?>

    </div>
    
        <?= Html::submitButton('<i class="fas fa-filter"></i>', ['class' => 'btn' ,'title'=>'FILTAR']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

