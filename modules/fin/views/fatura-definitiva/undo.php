<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm; 
 

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

    <?php $form = ActiveForm::begin(); ?>
   
    <p>PRETENDE TAMBEM ANULAR OS SEGUINTES DOCUMENTO ?</p>

    <?php if(!empty( $model->faturaEletronica)):?>
    <?= $form->field($model, 'undo_fatura')->inline()->radioList(['1' => 'Sim', '0' => 'Não'])->label('Eliminar fatura número' . $model->faturaEletronica->getNumber()); ?> 
    <?php endif;?>
   <?php if(!empty( $model->faturaDebitoCliente)):?>
    <?= $form->field($model, 'undo_nota_debito')->inline()->radioList(['1' => 'Sim', '0' => 'Não'])->label('Eliminar nota de debito numero' . $model->faturaDebitoCliente->getNumber()); ?> 

    <?php endif;?>

    <div class="form-group">
        <?= Html::submitButton('Anular', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'title' => 'ANULAR', 'id' => 'save']) ?>
    </div>

    <?php ActiveForm::end(); ?>

<section>



