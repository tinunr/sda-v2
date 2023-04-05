<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */

$this->title = Yii::t('app', 'Update Auth Assignment: ') . ' ' . $model->item_name;
?>
<section>
<div class="auth-assignment-update">
    <?= Html::a('<i class="fa fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-primary']) ?>


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</section>