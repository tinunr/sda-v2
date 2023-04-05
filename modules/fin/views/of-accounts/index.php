<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Encontro de contas';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?= ButtonDropdown::widget([
    'label' => '<i class="fas fa-list"></i>'.' NOVO',
    'options'=>['class'=>'dropdown-btn dropdown-new'],
    'encodeLabel' => false,
    'dropdown' => [
        'encodeLabels' => false,
        'options'=>['class'=>'dropdown-new'],
        'items' => [
            ['label' => '<i class="fas fa-plus"></i>'.' A Partir da Despesa', 'url' => ['create-b']],
            ['label' => '<i class="fas fa-plus"></i>'.' A Partir da Fatura', 'url' => ['create-a']],
            ['label' => '<i class="fas fa-plus"></i>'.' N. Débito/ Factura Despesa', 'url' => ['create-c']],
        ],
    ],
]);?>
  </div></div> 
<?php yii\widgets\Pjax::begin() ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}'],
             [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ], 
            'valor:currency',            
            'data:date',            
            'person.nome',            
            'descricao',

        ],
    ]); ?>
<?php yii\widgets\Pjax::end() ?>

</div>
</section>