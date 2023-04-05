<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use conquer\select2\Select2Widget;
use app\modules\dsp\models\Person;
use app\models\Ano;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['create'],
        'method' => 'get',
    ]); ?>

    <div class="row">
     <div class="col-md-1">

    <?=$form->field($model, 'bas_ano_id')->widget(Select2Widget::className(),
            [
                //'ajax' => ['/dsp/person/ajax'],
                'items' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
                'options' => ['placeholder' => 'Select a state ...',
                    'id'=>'ano',
                    //'onchange'=>'getNordData(this)'
                ],

            ]
        )->label(false);?> 
    </div>
    <div class="col-md-3">
        <?=$form->field($model, 'dsp_person_id')->widget(Select2Widget::className(),
            [
                'ajax' => ['/dsp/person/ajax'],
                'items' => ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
                'options' => ['placeholder' => 'Select a state ...',
                    'id'=>'dsp_person_id',
                    //'onchange'=>'getNordData(this)'
                ],

            ]
        )->label(false);?> 

    </div>
   
        <?= Html::submitButton('<i class="fa fa-filter"></i> ', ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

