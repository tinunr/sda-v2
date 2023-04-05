<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DesciplinaPerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Processos';

?>
<section>
<div class="desciplina-perfil-index">
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
 
  <?php  echo $this->render('_searchRelatorio', ['model' => $searchModel]); ?>


 </div>
 <div class="row">
   

     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'filterPosition'=>  GridView::FILTER_POS_HEADER,
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],
          'numero', 
          'nord', 
          'mercadoria',
          'valor_recebido',
'honorario',
'total_pago',
[
  'label'=>'Saldo a Favor do Cliente',
'value' => function($model){
  // print_r($model);die();
                    return ($model['valor_recebido']-$model['honorario']-$model['total_pago']);                  
                }]
        ]
    ]); ?>
 </div>
 

</section>