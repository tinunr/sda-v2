<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Nova Item da Fatura Provisória';
?>
<section>
<div class="Curso-index">
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</section>