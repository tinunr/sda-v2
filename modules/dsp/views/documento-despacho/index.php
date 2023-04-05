<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Documento de despachos';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

  <?php  echo Html::a('<i class="fa  fa-plus-square"></i> Novo', ['create'], ['class' => 'btn btn-success']) ?>
  </div></div> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\ActionColumn',
            'visibleButtons' =>
              [
                  'view' => Yii::$app->user->can('dsp/documento-despacho/view'),
                  'update' => Yii::$app->user->can('dsp/documento-despacho/update'),
                  'delete' => Yii::$app->user->can('dsp/documento-despacho/delete')
              ]
            ], 
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descricao',
            [
                'label'=>'Obrigatório',
                'format'=>'raw',
                'encodeLabel' => false,
                //'format' => 'html',
                'headerOptions' => ['style'=>'text-align:center'],'value' => function($model){
                   if ($model->obrigatorio) {
                      return Html::img(Url::to('@web/img/greenBall.png')).' - SIM';  
                    }else{
                    return Html::img(Url::to('@web/img/grayBal.png')).' - NÂO';  

                   }
                }
            ],

        ],
    ]); ?>
</div>
</section>