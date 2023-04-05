<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = $model->id;
?>

<section>
    <div class="row">

        <div class="row button-bar">
            <div class="row pull-left">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class'=>'btn btn-warning']) ?>
            </div>
            <div class="row pull-right">
                <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id'=>$model->id], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'descricao',
                ],
            ]) ?>
    </div>

</section>