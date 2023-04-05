<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Estrato de contas  - Contas Bancárias';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

  </div></div> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\ActionColumn','template'=>'{view}'],
            ['class' => 'yii\grid\SerialColumn'],
            [
              'label'=>'Nº de conta','value'=>function($model){
                 return $model->caixa->bancoConta->banco->sigla.' - '.$model->caixa->bancoConta->numero;  
                }
            ],
            
            [
                'attribute'=>'caixaOperacao.descricao',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->ImgCaixaOpercacao($model->fin_caixa_operacao_id).' '.$model->caixaOperacao->descricao;
                  
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
            'numero_documento',
            'data_documento',
            'valor_entrada:currency',
            'valor_saida:currency',
            'saldo:currency',
            'descricao',
            'data:date',

        ],
    ]); ?>
</div>
</section>