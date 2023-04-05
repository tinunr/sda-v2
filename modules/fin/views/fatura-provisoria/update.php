<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */

?>
<section>
    <div class="desciplina-perfil-update">
        <?= Html::a(Yii::$app->ImgButton->img('left'), ['view', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>

        <?= $this->render('_form_update', [
            'model' => $model,
            'modelsFaturaProvisoriaItem' => $modelsFaturaProvisoriaItem,
        ]) ?>

    </div>
</section>
