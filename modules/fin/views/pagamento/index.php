<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pagamentos';
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?=Html::encode($this->title)?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php   Html::a('<i class="fas fa-plus"></i>', ['create-a'], ['class' => 'btn','title'=>'NOVO']) ?>

    </div>
    </div>
    <?php yii\widgets\Pjax::begin() ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}'],
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],           
            [
                'attribute'=>'documentoPagamento.descricao',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->ImgDocumentoPagamento($model->fin_documento_pagamento_id).' '.$model->documentoPagamento->descricao;                  
                }
            ],
            'valor:currency',
            'data:date',
            [
              'label'=>'Fornecedor',
              'value'=>'person.nome'
            ],
            // 'descricao',

        ],
    ]); ?>
    <?php yii\widgets\Pjax::end() ?>

    </div>
</section>
