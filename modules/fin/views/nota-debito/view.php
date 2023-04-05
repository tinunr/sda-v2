<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
// print_r($model->ofAccountsItem );die();

$this->title = 'Nota de debito nº '.$model->numero.'/'.$model->bas_ano_id;
?>

<section>
<div class="row">
<div class="row button-bar">
<div class="row pull-left">
<?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>
    </div>
    <div class="row pull-right">
        
        <?= Html::a('<i class="fas fa-print"></i> Documento', ['view-pdf', 'id'=>$model->id], ['class'=>'btn btn-warning','target'=>'_blanck','title'=>'IMPRIMIR']) ?>  
        <?= Html::a('<i class="fas fa-print"></i> Histórico', ['historico-pdf', 'id'=>$model->id], ['class'=>'btn btn-warning','target'=>'_blanck','title'=>'IMPRIMIR']) ?>  
              <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id'=>$model->id], ['class'=>'btn btn-warning','title'=>'ATUALIZAR']) ?>

        <?php if($model->status):?>
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

<div class="titulo-principal"> <h5 class="titulo"><?=$this->title?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
           [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            'data:date',
            'valor:currency',
            'valor_pago:currency',
            'saldo:currency',
            'person.nome',
            'descricao',
        ],
    ]) ?>

</div>

<div class="titulo-principal"> <h5 class="titulo">Encontro de conta</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Numero</th>
                <th>Descrição</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($model->ofAccountsItem as $value):?>
            <tr>
                <td>#</td>
                <td><?=$value->ofAccounts->numero.'/'.$value->ofAccounts->bas_ano_id?></td>
                <td><?=$value->ofAccounts->descricao?></td>
                <td><?=$value->valor?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>


    </section>

