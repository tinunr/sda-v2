<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */

$this->title = Yii::t('app', 'Create Auth Assignment');
?>
<div class="auth-assignment-create">

    <?= Html::a('<i class="fa fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-primary']) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
