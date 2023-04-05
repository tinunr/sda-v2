<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

use app\models\Ano;
use app\models\Mes;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\Documento;

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
    <?= $form->field($model, 'bas_mes_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' => ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id', 'descricao'),
        'options' => ['placeholder' => 'Selecione ...'],
        'pluginOptions' => [
            'allowClear' => true, 
        ], 
    ])->label(false);?> 
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'cnt_diario_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new yii\db\Query)->from('cnt_diario')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as nome")])->orderBy('id')->all(), 'id','nome'),
        'options' => ['placeholder' => 'Diário'],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ])->label(false);?>
    </div>
     <div class="col-md-4">
        <?= $form->field($model, 'cnt_documento_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Documento::find()->orderBy('descricao')->all(), 'id','descricao'),
        'options' => ['placeholder' => 'Documento'],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ])->label(false);?>
    </div>
    <div class="col-md-5">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Procurar ...'])->label(false) ?>
    </div>
        <?= Html::submitButton('<i class="fas fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

