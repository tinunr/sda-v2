<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Nova despesas';
?>
<section>
<div class="Curso-index">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= $this->render('_form_agencia', [
        'model' => $model,
        'modelsDespesaItem'=>$modelsDespesaItem
    ]) ?>

</div>
