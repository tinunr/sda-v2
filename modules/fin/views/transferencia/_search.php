<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Ano;
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
        ])->label(false); ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder' => 'Procurar ...'])->label(false) ?>
    </div>
    <?= Html::submitButton('<i class="fas fa-filter"></i>', ['class' => 'btn', 'title' => 'FILTAR']) ?>

    <div class="row pull-right">


        <?php ActiveForm::end(); ?>
