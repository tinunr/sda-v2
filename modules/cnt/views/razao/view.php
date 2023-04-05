<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = ' Razão nº '.$model->numero.'/'.$model->bas_ano_id;
?>

<section>
<div class="Curso-index">

<div class="row button-bar">
            <div class="row pull-left">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>
            </div>
            <div class="row pull-right">
        <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->status==1):?>
        <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
            'class' => 'btn btn-danger','title'=>'ANULAR',
            'data' => [
                'confirm' => 'PRETENDE REALMENTE ANULAR ESTE LANÇAMENTO?',
                'method' => 'post',
            ],
        ]) ?>
    <?php endif;?>
        
    </div>
    </div>
    
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<div class="row">
<div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'numero',
                'format' => 'raw',
                'value'=>function($model){
                    return  yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->cnt_diario_id.'/'.$model->bas_mes_id.'/'.$model->bas_ano_id;     
                }
              ],
            [
                'label'=>'Origem',
                'attribute'=>'documento_origem_id',
                'format' => 'raw',
                'value'=>function($model){
                    if (!empty($model->documento->descricao)) {
                        return Html::a($model->documento->descricao,$model->origemUrl(),['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);  
                    }
                }
              ],
            'diario.descricao', 
            'descricao',     
        ],
    ])?>
</div>
<div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label'=>'Data Doc.',
                'format' => 'raw',
                'value'=>function($model){
                    return  $model->mes->descricao.'-'.$model->ano->ano;     
                }
              ],
            'data:date',
            'valor_debito:currency',
            'valor_credito:currency',
        ],
    ])?>
</div>
</div>
  
<div class="titulo-principal"> <h5 class="titulo">RAZÃO ITEM</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

      <?= GridView::widget([
        'dataProvider' => $providerRazaoItem,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'descricao',
            'cnt_natureza_id',
            'cnt_plano_conta_id',
            ['label'=>'Cod.','attribute'=>'cnt_plano_terceiro_id'],
            'planoTerceiro.nome',
            'cnt_plano_fluxo_caixa_id',
            'cnt_plano_iva_id',
            'valor:currency',

            
        ],
    ]); ?>
</div>
</section>



<section>
    <?php $filedata = \app\modules\cnt\services\RazaoService::ficheiroData($model->id); 
    echo \app\components\widgets\FileBrowserView::widget([
        'item_id' => $filedata['item_id'],
        'action_id' => $filedata['action_id'],
        'dir' => $filedata['dir'],
        'can_edit'=>true, 
        'folders'=>[ 
            $filedata['dir_complemento'],  
        ]
    ]); ?>
</section>




  


