<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\cnt\models\PlanoFluxoCaixaTipo;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ilhas-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->errorSummary($model); ?>


        <?= $form->field($model, 'id')->textInput(['placeholder'=>'']) ?>
        <?= $form->field($model, 'descricao')->textInput(['placeholder'=>'']) ?>

        <?= $form->field($model, 'cnt_plano_fluxo_caixa_tipo_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(PlanoFluxoCaixaTipo::find()->orderBy('id')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' => true,
              ],
          ])
        ?>

        <?= $form->field($model, 'cnt_plano_fluxo_caixa_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_fluxo_caixa')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->orderBy('codigo')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' => true,
              ],
          ])
        ?>

   

    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
