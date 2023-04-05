<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posição de Tesouraria';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
        <?= Html::a('<i class="fas fa-print"></i> Imprimir', ['posicao-tesouraria-pdf'], ['class'=>'btn btn-warning','target'=>'_blanck','title'=>'Imprimir']) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            // 'banco.descricao',
            [
                'attribute'=>'numero',
                'format'=>'raw',
                'value'=>function($model){
                    return $model->banco->sigla.' - '.$model->numero;
                }
            ],
            // 'banco.sigla',
            // 'numero',
            // 'descoberta:currency',
            'saldo:currency',
            

        ],
    ]); ?>
</div>
</section>