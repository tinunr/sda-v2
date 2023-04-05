<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Plano de Conta';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>




  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?=Html::a('<i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success','title'=>'NOVO']);?>


  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
            'id',
            // 'cnt_plano_conta_id',
            'descricao',
            'planoContaTipo.descricao',
            [
                'attribute'=>'cnt_natureza_id',
                'format' => 'raw',
                'value'=>function($model){
                    return ($model->cnt_natureza_id=='D')?'<i style="color: green"> '.$model->cnt_natureza_id.'</i>':'<i style="color: red">'.$model->cnt_natureza_id.'</i>';
                }
            ],
            [
                'attribute'=>'tem_plano_externo',
                'format' => 'raw',
                'value'=>function($model){
                    return $model->tem_plano_externo?'<i style="color: green"> Sim</i>':'<i style="color: red">Não</i>';    
                }
            ],
            [
                'attribute'=>'tem_plano_fluxo_caixa',
                'format' => 'raw',
                'value'=>function($model){
                    return $model->tem_plano_fluxo_caixa?'<i style="color: green"> Sim</i>':'<i style="color: red">Não</i>';    
                }
            ],
            [
                'attribute'=>'is_plano_conta_iva',
                'format' => 'raw',
                'value'=>function($model){
                    return $model->is_plano_conta_iva?'<i style="color: green"> Sim</i>':'<i style="color: red">Não</i>';    
                }
            ],
        ],
    ]); ?>
</div>
</section>