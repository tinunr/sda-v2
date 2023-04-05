<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\dsp\models\Person;
use app\models\Ano;
use app\models\User;
use app\modules\dsp\models\ProcessoStatus;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
$initValueText = empty($model->user_id) ? '' : User::findOne($model->user_id)->name;
$initDspPersonId = empty($model->dsp_person_id) ? '' : Person::findOne($model->dsp_person_id)->nome;



/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="row">
     <div class="col-md-2">
      <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Ano'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?> 
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Nº Processo / NORD '])->label(false) ?>
        </div>
    <div class="col-md-3">
        <?= $form->field($model, 'descricao')->textInput(['placeholder'=>'Mercadoria'])->label(false) ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'status')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(ProcessoStatus::find()->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Estado','multiple' => true],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?>
    </div> 
     
    
        </div>
      

    <div class="col-md-5">
    <?=$form->field($model, 'user_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(User::find()->orderBy('name')->all(), 'id','name'),
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Responsavel','multiple' => false],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false)?>
    </div>
    <div class="col-md-5">

    <?php
     echo $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Person::find()->all(), 'id','nome'),
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Cliente','multiple' => false],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false); /* $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'initValueText' => $initDspPersonId,
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => 'Cliente'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' =>3,
            'ajax' => [
                'url' => Url::to(['/dsp/person/person-list']),
                'dataType' => 'json',
            ],
        ],
        
    ])->label(false);*/?>
    </div>

    
        <?= Html::submitButton('<i class="fas fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

