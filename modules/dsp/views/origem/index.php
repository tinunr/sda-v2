<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Origem';
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
                  'view' => Yii::$app->user->can('dsp/origem/view'),
                  'update' => Yii::$app->user->can('dsp/origem/update'),
                  'delete' => Yii::$app->user->can('dsp/origem/delete')
              ]
            ], 
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descricao',
        ],
    ]); ?>
</div>
</section>