<?php

use app\modules\dsp\models\ProcessoStatus;

$provider = \app\modules\dsp\services\DspService::processPdfAll($user_id, $status, $dsp_person_id, $bas_ano_id, $dataInicio, $dataFim, $comPl);
?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Nº Pro.</th>
        <th>Mercadoria</th>
        <th> PL | LIQ  </th>
        <th>Local</th> 
        <th>Obs</th> 
    </tr>
</thead>
<tbody>
    <?php foreach ($provider->getModels() as $model) : ?>
    <tr  >
        <td>
          <strong> PR: </strong><?= $model->numero . '/' . $model->bas_ano_id ?>
            <br>
             <strong> NORD: </strong> <?= empty($model->nord->id) ? '' : $model->nord->id ?>
           <br> <?= $model->processoStatus->descricao ?></td>
            
        </td>
        <td>
          <strong> Cliente: </strong><?= $model->person->nome ?><br>
           <strong> Mercadoria: </strong> <?= $model->descricao ?>
           <br>
           <div class="data"><strong>Data:</strong> <?= Yii::$app->formatter->asDate($model->data) ?> <span
            class="setor-pr"><?= $model->diasEscritorio() ?></span></div>
        </td> 
        <td>
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
        </td>
        <td> 
            <div class="setor-pr"><strong>Setor:</strong> <?= (empty($model->dsp_setor_id) ? '' : $model->setor->descricao) ?>
            </div>
            <div class="user-pr"><strong>User:</strong> <?= (empty($model->user_id) ? '' : $model->user->name) ?></div> 
            
            <div class="setor-pr"><strong>Data</strong> <?= $model->dataUltimoWorkflow() ?></div>
        </td>
        <td>
            <?= ($model->status == ProcessoStatus::PENDENTE) ? \app\modules\dsp\services\DspService::processoObsPendenstes($model->id) : '' ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
