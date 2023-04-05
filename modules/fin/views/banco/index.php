<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Meio Financeiro';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

  <?php  echo Html::a(Yii::$app->ImgButton->img('plus'), ['create'], ['class' => 'btn btn-success']) ?>
  </div></div> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'sigla',
            'descricao',
            [
                'label'=>'Pagamento / Recebimento',
                'attribute'=>'fin_documento_pagamento_id',
                'value' =>function ($model){
                  return implode(' ; ',\yii\helpers\ArrayHelper::map($model->bancoDocumentoPagamento, 'documentoPagamento.id', 'documentoPagamento.descricao'));
                } 
            ],

        ],
    ]); ?>
</div>
</section>