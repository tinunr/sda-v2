<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = $model->descricao;
$this->params['breadcrumbs'][] = ['label' => 'Unidades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<section>
<div class="row">


    <p>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'title' => 'Are you sure you want to delete this item?',
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descricao',
        ],
    ]) ?>

    </div>


    </section>

