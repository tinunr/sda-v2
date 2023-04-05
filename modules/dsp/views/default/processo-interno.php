<?php

use app\modules\dsp\repositories\ProcessoRepository;


?>


<div class="pagina">
    <div class="row">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" class="active"><a href="#funcionario" aria-controls="funcionario" role="tab"
                    data-toggle="tab">PROCESSO POR FUNCIONÁRIO</a></li>
            <li role="presentation"><a href="#setor" aria-controls="setor" role="tab" data-toggle="tab">PROCESSO POR
                    SETOR</a></li>

        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="funcionario">
                <div class="row">

                    <div class="panel-group" id="accordion">
                        <?php foreach ($personProcessos as $key => $processo):?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                        href="#collapse<?=$processo['user_id']?>"><?=$processo['user_name']?>
                                    </a>
                                    <div class="pull-right">
                                        <i class="badge"><?=$processo['total_processo']?></i>
                                    </div>
                                </h4>
                            </div>
                            <div id="collapse<?=$processo['user_id']?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <table class="table  table-striped">
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
                                            <?php foreach (ProcessoRepository::listProcessoInternoFuncionario($processo['user_id']) as $key => $data):?>
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
            </div>
            <div role="tabpanel" class="tab-pane" id="setor">
                <div class="row">

                    <div class="panel-group" id="accordion">

                        <?php foreach ($listSetorProcessoInterno as $key => $setor):?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                        href="#collapse-setor-<?=$setor['dsp_setor_id']?>"><?=$setor['dsp_setor_name']?>
                                    </a>
                                    <div class="pull-right">
                                        <i class="badge"><?=$setor['total_processo']?></i>
                                    </div>
                                </h4>
                            </div>
                            <div id="collapse-setor-<?=$setor['dsp_setor_id']?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <table class="table  table-striped">
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
                                            <?php foreach (ProcessoRepository::listProcessoInternoSetor($setor['dsp_setor_id']) as $key => $data):?>
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
            </div>
        </div>
    </div>
</div>
