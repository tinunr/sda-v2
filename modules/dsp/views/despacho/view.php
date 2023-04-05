<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */

$this->title = 'Despacho  '.$model->id;


?>
<section>
<div class="Curso-index">


    <p>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
    </p>
    
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id', 'dsp_desembaraco_id', 'bas_ano_id', 'data_registo', 'destinatario_id', 's_registo', 'numero_liquidade', 'data_prorogacao', 's_liquidade', 'data_liquidacao', 'verificador', 'reverificador', 'tramitacao', 'valor', 'status', 'modelo', 'rg', 'declarante', 'nord', 'artigo', 'destinatario_nif', 'destinatario_nome', 'n_receita', 'data_receita', 'time_end_freeze', 'cor', 'created_by', 'updated_by', 'created_at', 'updated_at', 's_reg'


        ],
    ]) ?>

</div>
</section>

