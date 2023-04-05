<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'despesa nº ' . $model->numero.'/'.$model->bas_ano_id;
?>
<section>
<div class="Curso-index">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['view','id'=>$model->id], ['class' => 'btn btn-warning']) ?>

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
<?php if(empty($model->dsp_processo_id)):?>

    <?= $this->render('_form_agencia', [
        'model' => $model,
        'modelsDespesaItem'=>$modelsDespesaItem,
            'readonly'=>$readonly,

    ]) ?>
<?php elseif(!empty($model->dsp_processo_id)&&empty($model->dsp_fatura_provisoria_id)):?>
<?= $this->render('_forrn_no_fp', [
        'model' => $model,
        'modelsDespesaItem'=>$modelsDespesaItem,
            'readonly'=>$readonly,

    ]) ?>
<?php else:?>
	<?= $this->render('_form', [
        'model' => $model,
        'modelsDespesaItem'=>$modelsDespesaItem,
            'readonly'=>$readonly,
        
    ]) ?>
<?php endif;?>
</div>
