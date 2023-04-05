<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Nova fatura definitiva normal';
?>
<section>
<div class="Curso-index">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>
        <div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsFaturaDefinitivaItem'=>$modelsFaturaDefinitivaItem,
    ]) ?>

</div>
</section>