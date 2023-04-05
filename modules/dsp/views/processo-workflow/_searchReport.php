<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\ProcessoWorkflowStatus;
use app\modules\dsp\models\Setor;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
    'action' => ['report'],
    'method' => 'get',
]); ?>

<div class="row">
    <div class="col-md-2">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder' => 'NUMERO'])->label(false) ?>
    </div>
    <div class="col-md-10">
        <?= $form->field($model, 'status')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(ProcessoWorkflowStatus::find()->all(), 'id', 'descricao'),
            'hideSearch' => true,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => 'ESTADOS', 'multiple' => true],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false); ?>
    </div>
    <div class="col-md-4">

        <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(User::find()->where(['status' => 10])->all(), 'id', 'name'),
            'options' => ['placeholder' => 'FUNCIONÁRIO'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'dsp_setor_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Setor::find()->joinWith('setorUser')->where(['arquivo' => 0])->orderBy('descricao')->all(), 'id', 'descricao'),
            'options' => ['placeholder' => 'SETOR'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(FALSE); ?>
    </div>
    <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
