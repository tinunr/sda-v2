<?php

use yii\helpers\Html;
$this->title = 'Novo Item';

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */


?>
<section>
<div class="candidatura-docente-create">

     <?php  echo Html::a('<i class="fa   fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>

    <?= $this->render('_form', [
        'model' => $model,
         'modelCliente'=>$modelCliente,
                'readonly'=>false,



    ]) ?>

</div>
</section>