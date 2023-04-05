<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\db\Query;
use app\models\User;
use app\models\Ano;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\ProcessoWorkflowStatus;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin([
        'action' => ['undo-archive-box'],
        'method' => 'get',
    ]); ?>

<div class="row">
    <div class="col-md-2">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Nº'])->label(false) ?>
    </div>
    <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
   


<?php ActiveForm::end(); ?>
