<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Novo Edificios';
?>
<section>
<div class="Curso-index">
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</section>