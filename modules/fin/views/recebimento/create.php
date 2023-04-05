<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */

$this->title = 'Recebimento';
?>
<section>
<div class="candidatura-docente-create">



     <?php  echo Html::a('<i class="fa   fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

      <?php  echo $this->render('_form', [
      	'model'=>$model,
      	'modelsReceita'=>$modelsReceita
      ]); ?>

</div>
</section>