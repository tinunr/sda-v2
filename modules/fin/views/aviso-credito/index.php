<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nota de Credito';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?php  echo Html::a('<i class="fas fa-plus"></i>', ['create'], ['class' => 'btn','title'=>'NOVO']) ?>

  </div></div> 
    <?php yii\widgets\Pjax::begin()?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','header'=>'Acções','template'=>'{view}'],
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            [
                'label'=>'Processo',
                'format' => 'raw',
                'value'=>function($model){
                    return empty($model->dsp_processo_id)?'':Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','target'=>'_blanck']);
                }
            ],
            [
                'label'=>'Fatura Definitiva',
                'format' => 'raw',
                'value'=>function($model){
                    return empty($model->fin_fatura_defenitiva_id)?'':Html::a($model->faturaDefinitiva->numero.'/'.$model->faturaDefinitiva->bas_ano_id,['/fin/fatura-definitiva/view','id'=>$model->fin_fatura_defenitiva_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);   

                }
            ],
            'valor:currency',
            'person.nome',
            [
                'label'=>'Despesa',
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a(yii::$app->ImgButton->Status($model->despesa->status).$model->despesa->numero,['/fin/despesa/view','id'=>$model->despesa->id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);
                }
            ],
            'despesa.saldo:currency',

        ],
    ]); ?>
    <?php yii\widgets\Pjax::end()?>

</div>
</section>