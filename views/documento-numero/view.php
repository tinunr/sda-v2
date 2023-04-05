<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */

$this->title = 'Documentos';
?>
<section>
<div class="auth-assignment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Voltar'), ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('ATULAIZAR', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('ELIMINAR', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'descricao',
        ],
    ]) ?>

</div>
</section>