<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */

?>
<section>
<div class="desciplina-perfil-update">

    <?= Html::a('<i class="fa fa-chevron-left"></i> Voltar', ['index'], ['class'=>'btn btn-warning']) ?>

    <?= $this->render('_form_update', [
        'model' => $model,
        'modelCliente'=>$modelCliente,
        'readonly'=>$readonly,
    ]) ?>

</div>
</section>