<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;
use app\modules\dsp\models\Desembaraco;
use app\modules\dsp\models\Processo;
use app\models\Ano;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ilhas-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="col-md-6">

        <?= $form->field($model, 'numero')->textInput(['placeholder' => 'N.O.R.D', 'readonly' => true]) ?>

    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'dsp_processo_id')->widget(Select2::classname(), [
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => ' ', 'id' => 'dsp_processo_id'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::to(['/dsp/processo/dropdow-list']),
                    'dataType' => 'json',
                ],
            ],
        ]); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
            'hideSearch' => true,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'Ano ...'],
            'pluginOptions' => [
                'disabled' => false,
                'allowClear' => false,
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'dsp_desembaraco_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Desembaraco::find()->orderBy('descricao')->all(), 'id', 'descricao'),
            'hideSearch' => true,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'Ano ...'],
            'pluginOptions' => [
                'disabled' => true,
                'allowClear' => false,
            ],
        ])
        ?>
    </div>



    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
