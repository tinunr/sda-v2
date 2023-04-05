<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = $model->descricao;
$can_edit = true;
?>

<section>
<div class="row">
<div class="row button-bar">
        <div class="row pull-left">
            <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning', 'title' => 'VOLTAR']) ?>
        </div>
        <div class="row pull-right">
            <?= Html::a('<i class="fas fa-print"></i> Imprimir', ['view-pdf', 'id'=>$model->id], ['class'=>'btn','target'=>'_blanck','title'=>'Imprimir']) ?>
        
        <?php if($model->status&&Yii::$app->user->can('fin/transferencia/undo')):?>

        <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
            'class' => 'btn btn-danger','title'=>'ANULAR',
            'data' => [
                'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
    </div>
    </div>
    <div class="titulo-principal">
            <h5 class="titulo"><?=Html::encode($this->title)?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>   
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            'referencia',
            'data:date',
            // 'numero',
            ['label'=>'Conta de Origem','value'=>$model->bancoContaOrigem->banco->sigla.' - '.$model->bancoContaOrigem->numero],
            ['label'=>'Conta de Destino','value'=>$model->bancoContaDestino->banco->sigla.' - '.$model->bancoContaDestino->numero],
            'valor:currency',
            'descricao',
            [
                'label'=>'Contabilidade',
                'format' => 'raw',
                'value'=>function($model){
                    $id = Yii::$app->CntQuery->inContabilidade(\app\modules\cnt\models\Documento::MOVIMENTO_INTERNO, $model->id);
                    if ($id) {
                        $can_edit = false;

                        return Html::a('Contabilidade',['/cnt/razao/view','id'=>$id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);   
                    }
                }
            ],
        ],
    ]) ?>

    </div>


    </section>

    <section>
    <?php 
    echo \app\components\widgets\FileBrowserView::widget([
        'item_id' => $model->id,
        'action_id' => 'fin_transferencia',
        'dir' => 'data/transferencias/'.date('Y', strtotime($model->data)) . '/' . $model->numero,
        'can_edit'=>$can_edit
    ]); ?>
</section>