<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Item da Fatura Provisória '.$model->id;
?>

<section>
<div class="Curso-index">


    <p>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
    </p>
    


  <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'itemTipo.descricao',
            'descricao',
            'person.nome',
            [
                'label'=>'Ativo Protocolo Processo',
                'value'=> $model->protocolo_processo?'Sim':'Não',
            ]
        ],
    ]) ?>

<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'planoConta.descricao',
            'planoFluxoCaixa.descricao',
            'planoIva.descricao',
        ],
    ]) ?>
</section>




  

</div>
