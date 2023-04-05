<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\dsp\models\Person;
use app\models\Ano;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\db\Query;
$initDspPersonId = empty($model->dsp_person_id) ? '' : Person::findOne($model->dsp_person_id)->nome;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
        'action' => ['receita'],
        'method' => 'get',
    ]); ?>

<div class="row">

    <div class="col-md-6">
        <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'initValueText' => $initDspPersonId,
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_person_id','onchange'=>'this.form.submit()'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' =>3,
            'ajax' => [
                'url' => Url::to(['/dsp/person/person-list']),
                'dataType' => 'json',
            ],
        ],
        
        
    ])->label(false);?>
    </div>

    <div class="row pull-right">


        <?php ActiveForm::end(); ?>
