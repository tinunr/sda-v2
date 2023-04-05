<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;
use app\models\AuthItem;
use app\models\User;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */
/* @var $form yii\widgets\ActiveForm */
?>
<section>
<div class="auth-assignment-form">

    <?php $form = ActiveForm::begin(); ?>


    <?php
      echo $form->field($model, 'item_name')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map(AuthItem::find()->orderBy('name')->all(), 'name','name','type'),
          'options' => ['placeholder' => 'Selecione item name ...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);

      ?>


    <?php
      echo $form->field($model, 'user_id')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map(User::find()->orderBy('name')->all(), 'id','name'),
          'options' => ['placeholder' => 'Selecione user ...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);

      ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>