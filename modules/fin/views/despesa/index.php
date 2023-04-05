<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Despesas';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
<?= ButtonDropdown::widget([
    'label' => '<i class="fas fa-plus"></i>',
    'options'=>['class'=>'dropdown-btn dropdown-new'],
    'encodeLabel' => false,
    'dropdown' => [
        'encodeLabels' => false,
        'options'=>['class'=>'dropdown-new'],
        'items' => [
            ['label' =>'<i class="fas fa-plus">Desp. Cliente</i> ', 'url' => ['create']],
            ['label' =>'<i class="fas fa-plus">Desp. Agencia</i> ', 'url' => 'create-agencia'],
        ],
    ],
]);?>

  </div></div> 
    <?php yii\widgets\Pjax::begin()?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
           [
                'attribute'=>'numero',
                'format'=>'raw',
                'value'=>function($model){
                    return Yii::$app->ImgButton->Status($model->status).' '.$model->numero;
                }
            ],
            // [
            //   'label'=>'Processo',
            //   'attribute' => 'processo',
            //   'format' => 'raw',
            //   'value'=>function($model){
            //       return empty($model->dsp_processo_id)?'':Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);    

            //   }
            // ],
            // [
            //   'label'=>'FP',
            //   'format' => 'raw',
            //   'attribute' => 'faturaProvisoria',
            //   'value'=>function($model){
            //       return empty($model->dsp_fatura_provisoria_id)?'':Html::a($model->faturaProvisoria->numero.'/'.$model->faturaProvisoria->bas_ano_id,['/fin/fatura-provisoria/view','id'=>$model->dsp_fatura_provisoria_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);    

            //   }
            // ],
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
            // 'valor_pago:currency',
            // 'saldo:currency',
            'data:date',
            'person.nome',

        ],
    ]); ?>
    <?php yii\widgets\Pjax::end()?>

</div>
</section>