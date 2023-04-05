<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */

$this->title = 'Documentos';

?>
<section>
<div class="row">

    <?= Html::a('<i class="fa fa-chevron-left"></i> Voltal', ['index'], ['class' => 'btn btn-warning']) ?>
   
    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</section>