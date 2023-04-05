<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\fin\models\FaturaProvisoriaItem;
use yii\helpers\BaseStringHelper;
$formatter = Yii::$app->formatter;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DesciplinaPerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Honorário';
?>
<section>
<div class="desciplina-perfil-index">
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

 
  <?php  echo $this->render('_searchHonorario', ['model' => $searchModel]); ?>


 
   </div></div>
 <div class="row">
   <?php yii\widgets\Pjax::begin() ?>
     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter'=>TRUE,
        'columns' => [           
            ['label'=>'Nº FP','attribute' =>'numero'],
            'nord',
            [ 
              'label'=>'Valor Honorário',
              'attribute' => 'valor',
              'value'=>function($model){
                return Yii::$app->formatter->asCurrency($model['valor']);
              },
              'footer'=>$formatter->asCurrency(FaturaProvisoriaItem::totalItem($dataProvider->models,'valor'))
            ],
           [ 
             'attribute'=>'data',
            //  'value'=>'data:date',
              'contentOptions' => ['style' => 'width:80px;'],
            ],
            ['attribute' =>'nome','contentOptions' => ['style' => 'max-width:200px;']],
            [
              'label'=>'Mercadoria',
              'attribute' => 'mercadoria',
              'value'=>function($model){
                return $model['mercadoria'];
              },
              'contentOptions' => ['style' => 'max-width:450px;'],
            ],
            // 'recebido',  
            'numero_recebimento',
            'data_recebimento:date',       
        ],
    ]); ?>
<?php yii\widgets\Pjax::end() ?>



 </div>
 

</div>
</section>