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
        <?= Html::a('Atualizar', ['update','id'=>$model->id,'bas_ano_id'=>$model->bas_ano_id], ['class' => 'btn btn-warning']) ?>
    </p>
    
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id', 'bas_ano_id', 'dsp_desembaraco_id', 'data_registo', 'reverificador', 'data_autorizacao', 'data_regularizacao', 'data_proragacao', 'status', 'declarante', 'importador_nif', 'importador_nome', 'manifesto', 'titulo_propriedade', 'vereficador', 'n_volumes', 'r_volumes', 'peso_bruto', 'ser', 'created_by', 'updated_by', 'created_at', 'updated_at'

           
        ],
    ]) ?>

</div>
</section>

