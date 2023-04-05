<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignment */

$this->title = 'Documentos';
?>
<section>
<div class="auth-assignment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Voltar'), ['index'], ['class' => 'btn btn-primary']) ?>
        
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'descricao',
            'documentoTipo.descricao',
            'diario.descricao',
            'planoConta.descricao',
        ],
    ]) ?>

</div>
</section>