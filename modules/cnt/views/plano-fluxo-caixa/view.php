<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */

$this->title = 'N.O.R.D  '.$model->id;


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
            'id',
            'descricao',
        ],
    ]) ?>

</div>
</section>

