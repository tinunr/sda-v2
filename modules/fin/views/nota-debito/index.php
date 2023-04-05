<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Nota de Débito';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?php  echo Html::a('<i class="fas fa-plus"></i>', ['create'], ['class' => 'btn','title'=>'NOVO']) ?>

  </div></div> 
    <?php yii\widgets\Pjax::begin()?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','header'=>'Acções','template'=>'{view}{update}'],
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            [
              'encodeLabel' => false,
              'format' => 'html',
              'headerOptions' => ['style'=>'text-align:center'],'value' => function($model){
                   if ($model->valor ==$model->valor_pago) {
                    return Html::img(Url::to('@web/img/greenBall.png'));  
                   }elseif($model->valor >$model->valor_pago&&$model->valor_pago>0){
                    return Html::img(Url::to('@web/img/gray-greenBall.png'));  
                   }else{
                    return Html::img(Url::to('@web/img/grayBal.png'));  
                    
                   }
                }
            ],     
            'valor:currency',
            'data:date',
            'person.nome',

        ],
    ]); ?>
    <?php yii\widgets\Pjax::end()?>

</div>
</section>