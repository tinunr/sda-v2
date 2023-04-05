<?php

use app\modules\dsp\repositories\ProcessoRepository;


?>


<div class="pagina">
    <div class="row">
        <div class="titulo-principal">
            <h5 class="titulo">Processo Externo</h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>
    </div>
    <div class="panel-group" id="accordion">
        <?php foreach ($listEstadoProcessoExterno as $key => $status):?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion"
                        href="#collapse<?=$status['status']?>"><?=$status['status_name']?>
                    </a>
                    <div class="pull-right">
                        <i class="badge"><?=$status['total_processo']?></i>
                    </div>
                </h4>
            </div>
            <div id="collapse<?=$status['status']?>" class="panel-collapse collapse">
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nº Processo</th>
                                <th>Mercadoria</th>
                                <th>Nome Fatura</th>
                                <th>Valor</th>
                                <th>Data Entrada</th>
                                <th>Estado Operacional</th>
                                <th>Estado Financeiro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (ProcessoRepository::listProcessoExternoStatus($status['status']) as $key => $data):?>
                            <tr>
                                <td><?=$data->numero .'/'.$data->bas_ano_id?></td>
                                <td><?=$data->descricao?></td>
                                <td><?=$data->nomeFatura->nome?></td>
                                <td><?=$data->valor?></td>
                                <td><?=$data->data?></td>
                                <td><?=$data->processoStatus->descricao?></td>
                                <td><?=$data->processoStatusFinanceiro->descricao?></td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
