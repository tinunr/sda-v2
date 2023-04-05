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

            //'id',
            ['label'=>'Nº de conta','value'=>function($model){
                 return $model->bancoConta->banco->sigla.' - '.$model->bancoConta->numero;                      # code...
                }],
            'bancoTransacaoTipo.descricao',
            [
                //'label'=>'Image',
                //'format'=>'raw',
                'encodeLabel' => false,
                               'format' => 'html',
               

                'headerOptions' => ['style'=>'text-align:center'],'value' => function($model){
                   if ($model->fin_banco_transacao_tipo_id ==1) {
                    return Html::img(Url::to('@web/img/greenBall.png'));                      # code...
                   }elseif($model->fin_banco_transacao_tipo_id ==2||$model->fin_banco_transacao_tipo_id ==3){
                    return Html::img(Url::to('@web/img/yellowBal.png'));  
                   }else{
                    return Html::img(Url::to('@web/img/redBal.png'));  

                   }
                }
            ],
            'valor:currency',
            'saldo:currency',
            'documentoPagamento.descricao',
            'descricao',
            'data:date',

        ],
    ]); ?>
</div>
</section>