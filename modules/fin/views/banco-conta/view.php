<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = $model->banco->sigla.' - '.$model->numero;
?>

<section>
<div class="row">


    <p>
        <?= Html::a(Yii::$app->ImgButton->img('left'), ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>
        <?= Html::a(Yii::$app->ImgButton->img('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary','title'=>'ATUALIZAR']) ?>
        
    </p>
    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'numero',
                'format'=>'raw',
                'value'=>function($model){
                    return Yii::$app->ImgButton->Status($model->status).$model->banco->sigla.' - '.$model->numero;
                }
            ],
            // 'banco.sigla',
            // 'numero',
            'descoberta:currency',
            'saldo:currency',
            'diario.descricao',
            'planoConta.descricao',
            'planoFluxoCaixa.descricao',


        ],
    ]) ?>

    </div>


    </section>

