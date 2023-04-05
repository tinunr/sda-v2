<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IlhasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'N.O.R.D';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>




  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?= ButtonDropdown::widget([
    'label' => '<i class="fas fa-list"></i>'.' Novo',
    'options'=>['class'=>'dropdown-btn dropdown-new'],
    'encodeLabel' => false,
    'dropdown' => [
        'encodeLabels' => false,
        'options'=>['class'=>'dropdown-new'],
        'items' => [
            ['label' => '<i class="fas fa-plus"></i>'.' CVST1', 'url' => ['create']],
            ['label' => '<i class="fas fa-plus"></i>'.' CVST2', 'url' => 'create-2'],
        ],
    ],
]);?>

  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
            // ['class' => 'yii\grid\ActionColumn','template'=>'{view}{update}'],
            //['class' => 'yii\grid\SerialColumn'],
            #'id',
            // 'numero',
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->getNumber() ;                  
                }
          ], 
          [
            'label'=>'Nº Processo',
            'format' => 'raw',
            'value'=>function($model){
                return $model->processo->getNumber() ;    

            }
          ],
            // 'processo.numero',
            'desembaraco.code',
            'desembaraco.descricao',
             [
                'label'=>'XML',
                //'format'=>'raw',
                'encodeLabel' => false,
                'format' => 'raw',
                'headerOptions' => ['style'=>'text-align:center'],'value' => function($model){
                   if (file_exists(Yii::getAlias('@nords/').$model->id.'.xml')) {
                      return Html::img(Url::to('@web/img/greenBall.png')).' - '.$model->id.'.xml';  
                    }
                }
            ],
            
            //'processo.descricao',
            
        ],
    ]); ?>
</div>
