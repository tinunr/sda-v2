<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */

$this->title = 'Pagamento';
?>
<section>
<div class="candidatura-docente-create">



     <?php  echo Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['create-a'], ['class' => 'btn btn-warning']) ?>
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

      <?php  echo $this->render('_form', [
        'model'=>$model,
        'modelsDespesa'=>$modelsDespesa
      ]); ?>

</div>
</section>