<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = $model->id;
?>

<section>
<div class="row">


    <p>
        <?= Html::a('Voltar', $urlIndex, ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
    </p>
    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'path',
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
    
    ]) ?>

    </div>


    </section>






