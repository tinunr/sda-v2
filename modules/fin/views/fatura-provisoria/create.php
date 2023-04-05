<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */

$this->title = 'Novo fatura provisória';
?>
<section>
<div class="candidatura-docente-create">



     <?php  echo Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsFaturaProvisoriaItem'=>$modelsFaturaProvisoriaItem,
    ]) ?>

</div>
</section>