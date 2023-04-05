<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\fin\models\DocumentoPagamento;


/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

        <?= $form->field($model, 'descricao')->textInput() ?>
        <?= $form->field($model, 'sigla')->textInput() ?>

        <?= $form->field($model, 'fin_documento_pagamento_id')->widget(Select2::classname(), [
		    'data' => ArrayHelper::map(DocumentoPagamento::find()->orderBy('descricao')->all(), 'id','descricao'),
		    'options' => ['placeholder' => 'Selecione o(s) ...', 'multiple' => true],
		    'pluginOptions' => [
		        'tags' => true,
		        'tokenSeparators' => [',', ' '],
		        'maximumInputLength' => 10
		    ],
		]);?>


    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->img('save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>