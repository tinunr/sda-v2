<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\ProcessoStatus;
use app\models\User;
use kartik\select2\Select2;
use conquer\select2\Select2Widget;
use app\modules\dsp\models\Processo;
$initValueText = empty($model->dsp_processo_id) ? '' : Processo::findOne($model->dsp_processo_id)->numero;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(); ?>

 <div class="row">
  
    <?=$form->field($model, 'status')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map(ProcessoStatus::find()->all(), 'id','descricao'),
          'options' => ['placeholder' => 'Selecione ...'],
          'pluginOptions' => [
              'allowClear' => true,
              'disabled' => true,
          ],
      ]);

      ?>
    </div>

     


    

     

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
