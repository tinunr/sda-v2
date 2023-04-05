<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */

$this->title = 'Lançamento '.$model->id;


?>
<section>
<div class="Curso-index">


    <p>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Atualizar', ['update','id'=>$model->id], ['class' => 'btn btn-warning']) ?>
    </p>
    
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'lancamentoTipo.descricao',
            'diario.descricao',
            'mes.descricao',
            'descricao',
        ],
    ]) ?>



    <div class="table-responsive">
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Origem</th>
                    <th>Destino</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($model->lancamentoItem as $key => $value):?>
                <tr>
                    <td><?=$key+1?></td>
                    <td><?=$value->origem->id.' - '.$value->origem->descricao?></td>
                    <td><?=$value->destino->id.' - '.$value->destino->descricao?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        
    </div>

</div>
</section>

