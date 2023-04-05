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

<?php yii\widgets\Pjax::begin(['id' => 'new_country']) ?>
<?php $form = ActiveForm::begin([
        'action' => ['archive-box'],
        'method' => 'get',
    ]); ?>

<div class="row">
    <div class="col-md-2">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Nº'])->label(false) ?>
    </div>
    <div class="col-md-4">

        <?=$form->field($model, 'user_id')->widget(Select2::classname(), [
                 'data' =>ArrayHelper::map(User::find()->where(['status'=>10])->all(), 'id','name'),
                 'options' => ['placeholder' => 'Funcionário'],
                 'pluginOptions' => [
                     'allowClear' => true
                 ],
             ])->label(false);?>
    </div>
    <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
    <div class="row pull-right">


        <?php ActiveForm::end(); ?>
        <?php yii\widgets\Pjax::end() ?>
