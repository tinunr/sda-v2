<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */

$this->title =  'Atualizar ';
?>
<section>
<div class="auth-assignment-update">
    <?= Html::a('<i class="fa fa-chevron-left"></i> Voltal', ['index'], ['class' => 'btn btn-warning']) ?>
 <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</section>