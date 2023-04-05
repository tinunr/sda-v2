<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */

$this->title = 'Tipologia  ID '.$model->id;


?>
<section>
<div class="Curso-index">


    <p>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Yii::$app->ImgButton->Img('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'abreviatura',
            'descricao',
            'numero_destino',
            'numero_destino_b',


        ],
    ]) ?>

</div>
</section>

