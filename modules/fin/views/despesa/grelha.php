<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Grelha - Despesas & Fatura Provisória';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_searchGrelha', ['model' => $searchModel]); ?>
  </div></div> 
    <?php yii\widgets\Pjax::begin()?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:70px;'],
                'header'=>'Ações',
                'template' => '{update}',
                 'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<i class="fa fa-pencil-alt"></i>', Url::to(['/fin/despesa/grelha-update','id'=>$model->id]), [
                                        'title' =>'Atualizar',
                                        'class'=>'btn  btn-xs',
                            ]);
                        },
                    ],

                ],

           [
                'attribute'=>'numero',
                'format'=>'raw',
                'value'=>function($model){
                    return Yii::$app->ImgButton->Status($model->status).' '.$model->numero.'/'.$model->bas_ano_id;
                }
            ],
            [
              'label'=>'Processo',
              'attribute' => 'processo',
              'format' => 'raw',
              'value'=>function($model){
                  return empty($model->dsp_processo_id)?'':Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);    

              }
            ],
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