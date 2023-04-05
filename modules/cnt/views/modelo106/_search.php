<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Ano;
use app\models\Mes;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'ano')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Ano::find()->orderBy('id')->all(), 'id','ano'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])->label(false);
        ?>
    </div>
    <div class="col-md-3">


        <?= $form->field($model, 'mes')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])->label(false)
        ?>
    </div>
        <?= Html::submitButton(Yii::$app->ImgButton->Img('filter'), ['class' => 'btn btn-primary']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

