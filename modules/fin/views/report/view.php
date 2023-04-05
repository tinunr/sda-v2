<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = $model->id;
?>

<section>
<div class="row">


    <p>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
    </p>
    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'faturaProvisoria.processo.numero',
            'faturaProvisoria.numero',
            
            'valor:currency',
            
            'person.nome',
        ],
    ]) ?>

    </div>


    </section>

