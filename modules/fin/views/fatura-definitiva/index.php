<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fatura Definitiva';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
 
  <?= ButtonDropdown::widget([
            'label' => '<i class="fas fa-plus"></i>',
            'options' => ['class' => 'dropdown-btn dropdown-new'],
            'encodeLabel' => false,
            'dropdown' => [
                'encodeLabels' => false,
                'options' => ['class' => 'dropdown-new'],
                'items' => [
                    ['label' => 'FD NORMAL', 'url' => ['create-parceal']],
                    ['label' => 'FD ESPECIAL', 'url' => 'create-comofp'],
                ],
            ],
        ]); ?>
  </div></div> 
    <?php yii\widgets\Pjax::begin()?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}'],
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->statusSend($model->status, $model->send).$model->numero.'/'.$model->bas_ano_id;                 
                }
            ], 
            [
                'label'=>'Nº Processo',
                'attribute'=>'processo',
                'format' => 'raw',
                'value'=>function($model){
                    return Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);    
    
                }
              ],
            'data:date',
            'valor:currency',
            // 'processo.descricao',
            'person.nome',
        ],
    ]); ?>
    <?php yii\widgets\Pjax::end()?>

</div>
</section>