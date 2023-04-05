<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = 'Utilizador';
?>

<section>
<div class="user-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</section>