<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\cnt\models\Tipologia;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ilhas-form">

    <?php $form = ActiveForm::begin(); ?>
    


        <?= $form->field($model, 'descricao')->textInput(['placeholder'=>'']) ?>
        <?= $form->field($model, 'taxa')->textInput(['placeholder'=>'']) ?>
        <?= $form->field($model, 'deducao')->textInput(['placeholder'=>'']) ?>


         <?= $form->field($model, 'cnt_tipologia_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Tipologia::find()->orderBy('descricao')->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' => false,
              ],
          ])
        ?>

        <?= $form->field($model, 'cnt_plano_conta_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->where('cnt_plano_conta_tipo_id=2')->orderBy('id')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>

   

    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
