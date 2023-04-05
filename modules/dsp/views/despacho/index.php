<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IlhasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DESPACHO';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>




  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?php  echo Html::a(Yii::$app->ImgButton->Img('update').' SINCRONIZAR', ['import-excel'], ['class' => 'btn btn-success','id'=>'block-ui']) ?>

  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}'],
            // ['class' => 'yii\grid\ActionColumn','template'=>'{view}'],
        
        
        [
            'attribute' => 'numero',
            'encodeLabel' => false,
            'format' => 'html',
            'value' => function ($model) {
                return yii::$app->ImgButton->statusDesp($model->anulado) . $model->id . '/' . $model->bas_ano_id;
            }
        ],
              'nord',
			  'desembaraco.descricao',
            'data_registo:date',
			'numero_liquidade',
            'data_liquidacao:date',
            'verificador',
            'reverificador',
            'valor:currency',
            'n_receita',
            'data_receita:date', 
            
        ],
    ]); ?>
</div>
