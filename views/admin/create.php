<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Desciplina */
?>
<section>
<div class="desciplina-create">

  <div class="titulo-principal"> <h5 class="titulo">novo Utilizadores</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= Html::a('<i class="fa fa-chevron-left"></i> Voltar', ['index'], ['class'=>'btn btn-warning']) ?>
    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</section>