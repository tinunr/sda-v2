<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\modules\dsp\models\Person;
use app\models\Ano;
use app\models\AuthItem;
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

<div class="row">

    <?php $form = ActiveForm::begin([
        'action' => ['processo'],
        'method' => 'get',
    ]); ?>

    <div class="col-md-1">
        <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
            'hideSearch' => false,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'ANO'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false); ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder' => 'NUMERO / MERCADORIA / DIVERSOS'])->label(false) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(User::find()->orderBy('name')->all(), 'id', 'name'),
            'hideSearch' => false,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'RESPONSAVEL', 'multiple' => false],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
            'initValueText' => $initDspPersonId,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'CLIENTE'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => [
                    'url' => Url::to(['/dsp/person/person-list']),
                    'dataType' => 'json',
                ],
            ],
        ])->label(false); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'status')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(ProcessoStatus::find()->where(['dsp_processo_status_tipo_id' => 1])->all(), 'id', 'descricao'),
            'hideSearch' => true,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'ESTADO OERACIONAL', 'multiple' => true],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'status_financeiro_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(ProcessoStatus::find()->where(['dsp_processo_status_tipo_id' => 2])->all(), 'id', 'descricao'),
            'hideSearch' => true,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'ESTADO FINANCEIRO', 'multiple' => true],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'filter_opctions')->widget(Select2::classname(), [
            'data' => $model->filtersOpctions(),
            'hideSearch' => true,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'ESTADO ALFANDIGA', 'multiple' => true],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false); ?>
    </div>
    <div class="col-md-1">

        <?= Html::submitButton('<i class="fas fa-filter"></i> Filtar', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
