<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\ButtonGroup;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Recebimento';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>


<?= ButtonDropdown::widget([
    'label' => Yii::$app->ImgButton->Img('list').' Novo',
    'options'=>['class'=>'dropdown-btn dropdown-new'],
    'encodeLabel' => false,
    'dropdown' => [
        'encodeLabels' => false,
        'options'=>['class'=>'dropdown-new'],
        'items' => [
            ['label' => '<i class="fas fa-plus"></i>'.' Tesouraria', 'url' => ['criar']],
            ['label' => '<i class="fas fa-plus"></i>'.' Adiantamento', 'url' => ['adiantamento']],
            ['label' => '<i class="fas fa-plus"></i>'.' Reembolso', 'url' => ['reembolso']],
            ['label' => '<i class="fas fa-plus"></i>'.' Fatura FP | FD', 'url' => 'receita'],

        ],
    ],
]);?>


 
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
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            'recebimentoTipo.descricao',
            [
                'attribute'=>'documentoPagamento.descricao',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->ImgDocumentoPagamento($model->fin_documento_pagamento_id).' '.$model->documentoPagamento->descricao;                  
                }
            ],
            'valor:currency',
            'data:date',
            'person.nome',
            #'documentoPagamento.descricao'

        ],
    ]); ?>
    <?php yii\widgets\Pjax::end()?>

</div>
</section>

