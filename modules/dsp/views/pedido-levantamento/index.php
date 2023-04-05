<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IlhasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pedido de Levantamento';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>




  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?php if(1==1): ?>
  <?php  echo Html::a('<i class="fa  fa-sync-alt"></i> Atualizar', ['import-excel'], ['class' => 'btn btn-success']) ?>
<?php endif;?>

  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
            // ['class' => 'yii\grid\ActionColumn','template'=>'{view}{update}'],
            //['class' => 'yii\grid\SerialColumn'],
            [
              // 'label'=>'Nº PROC.',
              'attribute'=>'id',
                  'encodeLabel' => false,
                  'format' => 'html',
                  'value' => function($model){
                      return $model->id.'/'.$model->desembaraco->code.'/'.$model->bas_ano_id;                  
                  }
            ], 
            'data_registo',
            'data_autorizacao',
            'data_regularizacao:date',
            'data_proragacao',
            'pedidoLevantamentoStatus.descricao',
        ],
    ]); ?>
</div>
