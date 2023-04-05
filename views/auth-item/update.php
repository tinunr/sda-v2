<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Desciplina */
?>
<section>

<div class="desciplina-update">

 
    
    <?= Html::a('<i class="fa fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-primary']) ?>

    <?= $this->render('_update_form', [
        'model' => $model,
    ]) ?>

</div>
</section>
