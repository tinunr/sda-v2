<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transferências Bancárias';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

  <?php  echo Html::a('<i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success','title'=>'NOVO']) ?>
  </div></div> 
    <?php yii\widgets\Pjax::begin()?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}'],
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            'referencia',
            ['label'=>'Conta de Origem','value'=>function ($model){
                return $model->bancoContaOrigem->banco->sigla.' - '.$model->bancoContaOrigem->numero;
            }],
            ['label'=>'Conta de Destino','value'=>function ($model){
                return $model->bancoContaDestino->banco->sigla.' - '.$model->bancoContaDestino->numero;
            }],
            
            'valor:currency',
            'data:date'

        ],
    ]); ?>
    <?php Pjax::end()?>

</div>
</section>