<?php

use yii\bootstrap\Html;
?>
<div class="col-md-1">
    <?= Html::a('<i class ="fa fa-eye"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-xs']) ?>
    <?= Html::a('<i class ="fa fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-xs']) ?>
</div>
<div class="col-md-1">
    <div class="number-pr"><?= $model->numero . '/' . $model->bas_ano_id ?></div>
    <div class="number-nord">
        <?= empty($model->nord->id) ? '' : Html::a('NORD: ' . $model->nord->id, ['/dsp/nord/view', 'id' => $model->nord->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']) ?>
    </div>
</div>
<div class="col-md-4">
    <div class="cliente"><?= $model->person->nome ?>
        <span><?= (empty($model->person->nif) ? '' : Html::a($model->person->nif, ['/dsp/person/view', 'id' => $model->person->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank'])) ?></span>
    </div>
    <div class="status"><span>Operacional:</span> <?= $model->processoStatus->descricao ?>
        <span>Financeiro:</span>
        <?= !empty($model->status_financeiro_id) ? $model->processoStatusFinanceiro->descricao : '' ?>
    </div>
    <div class="mercadoria"><span>Mercadoria:</span> <?= $model->descricao ?></div>

</div>
<div class="col-md-3">
    <?php if ($model->getNumeroPedidoLevantamento()) : ?>
    <div class="mercadoria"><span>Nº PL:</span> <?= $model->getNumeroPedidoLevantamento() ?><span> Data
            regularização:</span> <?= $model->plDataRegularizacao() ?> / <?= $model->diasPedidoLevantamento() ?>
    </div>
    <?php endif; ?>
    <?php if ($model->getNumeroRegisto()) : ?>
    <div class="mercadoria"><span>Nº Registo:</span> <?= $model->getNumeroRegisto() ?><span> Data:</span>
        <?= $model->getDataRegisto() ?>
    </div>
    <?php endif; ?>
    <?php if ($model->getNumeroLiquidacao()) : ?>
    <div class="mercadoria"><span>Nº Liquidação:</span> <?= $model->getNumeroLiquidacao() ?><span> Data:</span>
        <?= $model->getDataLiquidacao() ?>
    </div>
    <?php endif; ?>
</div>
<div class="col-md-3">
    <div class="data"><strong>Data:</strong> <?= Yii::$app->formatter->asDate($model->data) ?> <span
            class="setor-pr"><?= $model->diasEscritorio() ?></span></div>
    <div class="setor-pr"><strong>Setor:</strong> <?= (empty($model->dsp_setor_id) ? '' : $model->setor->descricao) ?>
    </div>
    <div class="user-pr"><strong>User:</strong> <?= (empty($model->user_id) ? '' : $model->user->name) ?></div>
    <div class="setor-pr"><strong>Data user / setor</strong> <?= $model->dataUltimoWorkflow() ?></div>
</div>
