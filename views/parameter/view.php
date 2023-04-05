<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */

$this->title = 'Detalhe do Recurso';
?>
<section>
<div class="auth-assignment-view">
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
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label'=>'URL',
                'value'=>function($model){
                    return $model->module->baseUrl->descricao.$model->module->url.$model->resource;
                }
            ],
            'module.descricao',
            'resource',       
            'request',
            
            'descricao',

        ],
    ]) ?>

</div>
</section>