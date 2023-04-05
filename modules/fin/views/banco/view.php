<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
$formatter = \Yii::$app->formatter;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title ='Meio Financeiro'.$model->descricao;
?>

<section>
<div class="row">


    <p>
        <?= Html::a(Yii::$app->ImgButton->img('left'), ['index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Yii::$app->ImgButton->img('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(1>2):?>
            <?= Html::a(Yii::$app->ImgButton->img('delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    <?php endif;?>
    </p>
    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descricao',
            [
                'label'=>'Pagamento / Recebimento',
                'attribute'=>'fin_documento_pagamento_id',
                'value' => implode(' ; ',\yii\helpers\ArrayHelper::map($model->bancoDocumentoPagamento, 'documentoPagamento.id', 'documentoPagamento.descricao')),
            ],
        ]
    ]) ?>


    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nº DE CONTA</th>
                <th>DESCOBERTA</th>
                <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($model->bancoConta as $key => $bancoConta):?>
            <tr>
                <td>#</td>
                <td><?=Yii::$app->ImgButton->Status($bancoConta->status).$bancoConta->numero?></td>
                <td><?=$formatter->asCurrency($bancoConta->descoberta)?></td>
                <td><?=$formatter->asCurrency($bancoConta->saldo)?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
        
    </table>

    </div>


    </section>

