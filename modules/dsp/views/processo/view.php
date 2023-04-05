<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use  yii\widgets\Pjax;
use app\modules\dsp\models\DespachoDocumento;
use app\modules\dsp\models\Item;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\Tarefa;
use app\modules\dsp\models\ProcessoTarefa;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\modules\dsp\services\ProcessoService;

$formatter = \Yii::$app->formatter;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
$this->title = 'PR nº ' . $model->numero . '/' . $model->bas_ano_id;

?>
<section>
    <div class="desciplina-perfil-index">

        <div class="row button-bar">
            <div class="row pull-left">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', $urlIndex, ['class' => 'btn btn-warning']) ?>
            </div>
            <div class="row pull-right">
                <?php if(ProcessoService::bloquiarProcesso($model->id)):?>
                <?php if ($model->status != ProcessoStatus::STATUS_ANULADO) : ?>
                <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php if ($model->status != ProcessoStatus::CONCLUIDO && Yii::$app->user->can('dsp/processo/update-status')) : ?>
                <?= Html::a('<i class="fas fa-check-double"></i> Marcar Concluído', ['update-status', 'id' => $model->id], [
                            'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => 'Pretende realmente marcar este processo como concluído?',
                                'method' => 'post',
                            ],
                        ]) ?>
                <?php endif; ?>






                <?php if (Yii::$app->user->can('dsp/processo/anular-processo')) : ?>
                <?= Html::a('<i class="fas fa-undo"></i> Anular', ['anular-processo', 'id' => $model->id], [
                            'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => 'Pretende realmente anular este processo?',
                                'method' => 'post',
                            ],
                        ]) ?>
                <?php endif; ?>






                <?php endif; ?>
                <?php endif; ?>
                <?php if (empty($model->processoWorkflow)) : ?>
                <?= Html::a('<i class="fas fa-sticky-note"></i> Inicializar workflow', '#', ['class' => 'btn', 'data-toggle' => "modal", 'data-target' => '#modalInitWorkflow']) ?>
                <?php endif; ?>

                <?= Html::a('<i class="fas fa-clipboard-list"></i> Histórico', '#', ['class' => 'btn', 'data-toggle' => "modal", 'data-target' => '#modal-historico']) ?>

                <?= Html::a('<i class="fas fa-plus"></i> Novo Processo', ['create'], ['class' => 'btn btn-primary']) ?>



            </div>
        </div>
        <div class="titulo-principal">
            <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>

        <div class="row">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Processo', 'encodeLabel' => false,
                        'format' => 'html', 'value' => '<strong>Nº:</strong> ' . $model->getNumber() . ' | <strong>Estado:</strong> ' . $model->processoStatus->descricao . ' |<strong>Responsavel:</strong> ' . ($model->user_id ? $model->user->name : '') . ' | <strong>Setor:</strong> ' . ($model->dsp_setor_id ? $model->setor->descricao : '')
                    ],
                    'person.nome',
                    ['label' => 'Nome da fatura', 'value' => $model->nomeFatura->nome],
                    'descricao',
                    [
                        'label' => 'TCE',
                        'value' => implode(' ; ', \yii\helpers\ArrayHelper::map($model->processoTce, 'tce', 'tce')),
                    ],

                ],
            ]) ?>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'nord.id',
                        'n_registro_tn',
                        'valor:currency',
                    ],
                ]) ?>
            </div>
            <div class="col-md-6">

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'data:date',
                        'data_execucao:dateTime',
                        'data_conclucao:dateTime'
                    ],
                ]) ?>
            </div>
        </div>

        <?php if (!empty($model->n_levantamento)) : ?>
        <div class="titulo-principal">
            <h5 class="titulo">Pedido de Levantamento</h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            ['label' => 'Nº Pedido de Levantamento', 'attribute' => 'pedidoLevantamento.id'],
                            ['label' => 'Data de Registo', 'attribute' => 'pedidoLevantamento.data_registo'],
                            ['label' => 'Data de Autorização', 'attribute' => 'pedidoLevantamento.data_autorizacao'],
                        ],
                    ]) ?>
            </div>
            <div class="col-md-6">
                <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            ['label' => 'Data de Regularização', 'attribute' => 'pedidoLevantamento.data_regularizacao'],
                            ['label' => 'Data de Prorogação', 'attribute' => 'pedidoLevantamento.data_proragacao'],
                        ],
                    ]) ?>

            </div>

        </div>
        <?php endif; ?>

        <?php if (!empty($model->nord->numero)) : ?>
        <div class="titulo-principal">
            <h5 class="titulo">Despacho</h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            ['label' => 'Nº Despacho', 'attribute' => 'nord.despacho.id'],
                            ['label' => 'Data Registo', 'attribute' => 'nord.despacho.data_registo'],
                            'nord.id',
                            ['label' => 'Código Estância', 'attribute' => 'nord.desembaraco.code'],
                            ['label' => 'Nº Liquidação', 'attribute' => 'nord.despacho.numero_liquidade'],
                        ],
                    ]) ?>
            </div>
            <div class="col-md-6">
                <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            ['label' => 'Data Liquidação', 'attribute' => 'nord.despacho.data_liquidacao'],
                            ['label' => 'Verificador', 'attribute' => 'nord.despacho.verificador'],
                            ['label' => 'Reverificador', 'attribute' => 'nord.despacho.reverificador'],
                            ['label' => 'Nº Receita', 'attribute' => 'nord.despacho.n_receita'],
                            ['label' => 'Data Receita', 'attribute' => 'nord.despacho.data_receita'],
                        ],
                    ]) ?>
            </div>
        </div>
        <?php endif; ?>


    </div>
</section>








<section>
    <div class="row">
        <!-- Nav tabs -->
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab"
                        data-toggle="tab">Documento</a>
                </li>
                <li role="presentation"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Tarefa</a>
                </li>

                <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Fatura
                        Classificada</a>
                </li>
                <li role="presentation"><a href="#despesa-manual" aria-controls="despesa-manual" role="tab"
                        data-toggle="tab">Protocolo Processo</a>
                </li>
                <li role="presentation"><a href="#obs" aria-controls="obs" role="tab" data-toggle="tab">OBS</a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="profile">
                    <?php $ano = \app\models\Ano::findOne($model->bas_ano_id)->ano;
                    // print_r(ProcessoService::bloquiarProcesso($model->id));die();
                    echo \app\components\widgets\FileBrowserView::widget([
                        'item_id' => $model->id,
                        'action_id' => 'dsp_processo',
                        'dir' => 'data/processos/' . $ano . '/' . $model->numero,
                        'can_edit'=>ProcessoService::bloquiarProcesso($model->id),
                    ]); ?>



                </div>
                <div role="tabpanel" class="tab-pane " id="home">
                    <div class="table-responsive">

                        <table class="table table-hover">
                            <tr>
                                <th>Acções</th>
                                <th>Tarefa</th>
                                <th>Data</th>
                            </tr>
                            <?php foreach ($model->processoTarefa as $key=> $tarefa) : ?>

                            <tr>
                                <td>
                                    <?= $key +1 ?>
                                </td>
                                <td><?= $tarefa->descricao ?></td>
                                <td><?= $formatter->asDateTime($tarefa->created_at) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>

                                <th>
                                    <?php if(ProcessoService::bloquiarProcesso($model->id)):?>
                                    <?= Html::a('<i class="fas fa-sync"></i>', ['/dsp/tarefa/create-processo-tarefa', 'dsp_processo_id' => $model->id], ['class' => 'btn btn-xs']) ?>
                                    <?php endif;?>
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="messages">
                    <div class="table-responsive">
                        <div class="titulo-principal">
                            <h5 class="titulo">Fatura Classificada</h5>
                            <div id="linha-longa">
                                <div id="linha-curta"></div>
                            </div>
                        </div>
                        <table class="table table-hover">
                            <tr>
                                <th>Acções</th>
                                <th>Designação de Mercadoria</th>
                                <th>Nomeclatura</th>
                                <th>Qtd. Complemetar</th>
                                <th>Peso Líquido</th>
                                <th>Peso Bruto</th>
                                <th>Valor</th>
                            </tr>
                            <?php
                            $quantidade = 0;
                            $peso_liquido = 0;
                            $peso_bruto = 0;
                            $valor = 0;
                            ?>
                            <?php foreach ($model->faturaClassificada as $key => $faturaClass) : ?>
                            <?php
                                $quantidade = $quantidade + $faturaClass->quantidade;
                                $peso_liquido = $peso_liquido + $faturaClass->peso_liquido;
                                $peso_bruto = $peso_bruto + $faturaClass->peso_bruto;
                                $valor = $valor + $faturaClass->valor;
                                ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= $faturaClass->descricao ?></td>
                                <td><?= $faturaClass->nomeclatura ?></td>
                                <td><?= $faturaClass->quantidade ?></td>
                                <td><?= $faturaClass->peso_liquido ?></td>
                                <td><?= $faturaClass->peso_bruto ?></td>
                                <td><?= $formatter->asCurrency($faturaClass->valor) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th>
                                    <?php if(ProcessoService::bloquiarProcesso($model->id)):?>

                                    <?= Html::a('<i class="fas fa-sync"></i>', ['/dsp/fatura-classificada/create', 'dsp_processo_id' => $model->id], ['class' => 'btn btn-xs']) ?>
                                    <?php endif;?>
                                </th>
                                <th></th>
                                <th></th>
                                <th><?= $quantidade ?></th>
                                <th><?= $peso_liquido ?></th>
                                <th><?= $peso_bruto ?></th>
                                <th><?= $formatter->asCurrency($valor) ?></th>
                            </tr>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="despesa-manual">
                    <div class="table-responsive">
                        <div class="titulo-principal">
                            <h5 class="titulo">Protocolo Processo</h5>
                            <div id="linha-longa">
                                <div id="linha-curta"></div>
                            </div>
                        </div>
                        <table class="table">
                            <tr>
                                <th>Processo </th>
                                <td>Nº: <?= $model->numero . '/' . $model->bas_ano_id ?></td>
                            </tr>
                            <tr>
                            <tr>
                                <th>Pedido Levantamento</th>
                                <td>Nº:
                                    <?= $model->n_levantamento ?>
                                    -- Data
                                    regularização:
                                    <?= !empty($model->pedidoLevantamento->data_regularizacao) ? $model->pedidoLevantamento->data_regularizacao : '' ?>
                                    -- Data de prorrogação
                                    <?= !empty($model->pedidoLevantamento->data_proragacao) ? $model->pedidoLevantamento->data_proragacao : '' ?>
                                </td>
                            <tr>
                                <th>TCE</th>
                                <td><?= $tceCount ?></td>
                            </tr>
                            <tr>
                                <th>DV (Declaração de Valor)</th>
                                <td><?= $model->dv ?> </td>
                            </tr>
                            <tr>
                                <th>Exame prévio/Comercial (hrs)</th>
                                <td><?= $model->exame_previo_comercial ?> </td>
                            </tr>
                            <tr>
                                <th>Requerimento Espeical</th>
                                <td><?= $model->requerimento_espeical ?> </td>
                            </tr>
                            <tr>
                                <th>Requerimento Normal</th>
                                <td><?= $model->requerimento_normal ?> </td>
                            </tr>
                            <tr>
                                <th>TN</th>
                                <td><?= !empty($model->n_registro_tn) ? 1 : '' ?> </td>
                            </tr>
                            <tr>
                                <th>Valor Aduanério</th>
                                <td><?= !empty($model->nord->id) ? $formatter->asCurrency(\app\modules\dsp\services\NordService::valorAduaneiro($model->nord->id)) : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Tatal Item</th>
                                <td><?= !empty($model->nord->id) ? \app\modules\dsp\services\NordService::totalNumberOfItems($model->nord->id) : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <th> Quantidade de contentor Policia-acompanhamento p/desova</th>
                                <td><?= $model->policia_desova?>
                                </td>
                            </tr>
                            <tr>
                                <th>Polícia-acompanhamento p/selagem bebidas</th>
                                <td><?= $model->policia_selagem?'Sim':'Não' ?>
                                </td>
                            </tr>

                        </table>



                        <div class="titulo-principal">
                            <h5 class="titulo">Item de Despesa Não constante no XML</h5>
                            <div id="linha-longa">
                                <div id="linha-curta"></div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <tr>
                                <th>Acções</th>
                                <th>Item</th>
                                <th>Valor</th>
                            </tr>
                            <?php foreach ($model->processoDespesaManual as $key => $despesaManual) : ?>

                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= str_pad($despesaManual->item->id, 2, '0', STR_PAD_LEFT) . ' - ' . $despesaManual->item->descricao ?>
                                </td>

                                <td><?= $formatter->asCurrency($despesaManual->valor) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th>
                                    <?php if(ProcessoService::bloquiarProcesso($model->id)):?>
                                    <?= Html::a('<i class="fas fa-sync"></i>', ['/dsp/processo-despesa-manual/create', 'dsp_processo_id' => $model->id], ['class' => 'btn btn-xs']) ?>
                                    <?php endif;?>
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="obs">
                    <div class="table-responsive"> 
                        <table class="table table-hover">
                            <tr>
                                <th>#</th>
                                <th></th>
                            </tr>
                            <?php foreach ($model->processoObs as  $obs) : ?> 
                            <br>
                                <td>
                                    <?php if(ProcessoService::bloquiarProcesso($model->id)):?> 
                                    <?php if ($obs->status == 0) : ?>
                                    <?= Html::a('<i class="fa fa-check-circle"></i>', ['obs-status', 'id' => $obs->id, 'dsp_processo_id' => $model->id], [
                                                'class' => 'btn btn-xs',
                                                'data' => [
                                                    'confirm' => 'Marcar como resolvido?',
                                                    'method' => 'post',
                                                ],
                                            ]) ?>
                                    <?php endif; ?>
                                    <?php endif;?>
                                </td> 
                                <td ><?= ($obs->status == 0) ? 'Pendente' : 'Resolvido' ?> | <?= $obs->obs ?></td>
                                <td style="width: 30%;">
                                <strong>Criado por: </strong><?=$obs->userCreated->name?><strong> aos: </strong> <?=Yii::$app->formatter->asDateTime($obs->created_at)?> 
                                <?php if($obs->status != 0): ?>
                                    </br>
                                    <strong>Resolvido por: </strong><?=$obs->userUpdated->name?><strong> aos: </strong> <?=Yii::$app->formatter->asDateTime($obs->updated_at)?>
                                <?php endif;?>
                                
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th colspan="4">
                                    <?php if(ProcessoService::bloquiarProcesso($model->id)):?>
                                    <?= Html::a('<i class="fas fa-plus"></i>', '#', ['class' => 'btn btn-xs', 'data-toggle' => "modal", 'data-target' => '#modalObs']) ?>
                                    <?php endif;?>
                                </th> 
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Tab panes -->
        </div>
        <!-- Nav tabs -->
    </div>
</section>











<section>


    <?php if (!empty($model->faturaProvisorio)) : ?>
    <div class="titulo-principal">
        <h5 class="titulo">Fatura provisória</h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>
    <table class="table table-hover">
        <tr>
            <th>Tipo</th>
            <th>Numero</th> 
            <th>Valor</th>
            <th>Valor Recebido</th>
            <th>Saldo</th>
            <th>Data</th>
        </tr>
        <?php foreach ($model->faturaProvisorio as $key => $value) : ?>
        <tr>
            <th><strong>Fatura Provisória</strong></th>
            <th><?= yii::$app->ImgButton->Status($value->status) . $value->getNumber()   ?></th> 
            <th><?= Yii::$app->formatter->asCurrency($value->receita->valor) ?></th>
            <th><?= Yii::$app->formatter->asCurrency($value->receita->valor_recebido) ?></th>
            <th><?= Yii::$app->formatter->asCurrency($value->receita->saldo) ?></th>
            <th><?= Yii::$app->formatter->asDate($value->data) ?></th>
        </tr>
        <?php foreach ($value->receita->recebimentoItem as $key => $item) :  ?>
        <tr>
            <td>Recebimento</td>
            <td><?= yii::$app->ImgButton->Status($item->recebimento->status) . $item->recebimento->getNumber()   ?> </td> 
            <td><?= $formatter->asCurrency($item->valor) ?></td>
            <td><?= $formatter->asCurrency($item->valor_recebido) ?></td>
            <td><?= $formatter->asCurrency($item->saldo) ?></td>
            <td><?= $formatter->asDate($item->recebimento->data) ?></td>
        </tr>
        <?php endforeach; ?>

        <!-- Encontro de Conta -->
        <?php if (!empty($value->receita->ofAccounts)) : ?>
        <?php foreach ($value->receita->ofAccounts as $key => $encontroConta) : ?>
        <tr>
            <td>Econtro de Conta</td>
            <td><?= Yii::$app->ImgButton->Status($encontroConta->status) . ' ' . $encontroConta->getNumber()   ?> </td>  
            <td><?= $formatter->asCurrency($encontroConta->valor) ?></td>
            <td></td>
            <td><?= $formatter->asDate($encontroConta->data) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($value->receita->ofAccountsItem)) : ?>
        <?php foreach ($value->receita->ofAccountsItem as $key => $encontroConta) : ?>
        <tr>
            <td>Econtro de Conta</td>
            <td><?= Yii::$app->ImgButton->Status($encontroConta->ofAccounts->status) . ' ' . $encontroConta->ofAccounts->getNumber()  ?>
            </td> 
            <td></td>
            <td><?= $formatter->asCurrency($encontroConta->valor) ?></td>
            <td></td>
            <td><?= $formatter->asDate($encontroConta->ofAccounts->data) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>





    <?php if (!empty($model->despesa)) : ?>
    <div class="titulo-principal">
        <h5 class="titulo">despesas & pagamento</h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>
    <table class="table table-hover">
        <tr>
            <th>Tipo</th>
            <th>Numero</th>
            <th>Fornecedor</th>
            <th>Valor</th>
            <th>Valor Pago</th>
            <th>Saldo</th>
            <th>Data</th>
        </tr>
        <?php foreach ($model->despesa as $key => $value) : ?>
        <tr>
            <th>Despesa</th>
            <th><?= yii::$app->ImgButton->Status($value->status) . $value->getNumber()  ?></th>
            <th><?= $value->person->nome ?></th>
            <th><?= Yii::$app->formatter->asCurrency($value->valor) ?></th>
            <th><?= Yii::$app->formatter->asCurrency($value->valor_pago) ?></th>
            <th><?= Yii::$app->formatter->asCurrency($value->saldo) ?></th>
            <th><?= Yii::$app->formatter->asDate($value->data) ?></th>
        </tr>
        <?php if (!empty($value->pagamentoItem)) : ?>
        <?php foreach ($value->pagamentoItem as $key => $pagamentoItem) : ?>
        <tr>
            <td>Pagamento</td>
            <td><?= Yii::$app->ImgButton->Status($pagamentoItem->pagamento->status) . ' ' . $pagamentoItem->pagamento->getNumber()  ?>
            </td>
            <td></td>
            <td></td>
            <td><?= $formatter->asCurrency($pagamentoItem->valor) ?></td>
            <td></td>
            <td><?= $formatter->asDate($pagamentoItem->pagamento->data) ?></td>
            <td></td>
        </tr>

        <?php endforeach; ?>
        <?php endif; ?>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>




 <div class="titulo-principal">
        <h5 class="titulo">Fatura definitiva | Fatura debito de cliente | Fatura</h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>
    <?php if (!empty($model->faturaDefinitiva)) : ?>
   
    <table class="table table-hover">
        <tr>
            <th>Documento</th>
            <th>Numero</th> 
            <th>Valor</th>
            <th>Data</th>
        </tr>
        <?php foreach ($model->faturaDefinitiva as $key => $value) : ?>
        <tr>
            <td> Fatura definitiva</td>
            <td><?= yii::$app->ImgButton->Status($value->status) . $value->getNumber()   ?></td> 
            <td><?= Yii::$app->formatter->asCurrency($value->valor) ?></td>
            <td><?= Yii::$app->formatter->asDate($value->data) ?></td>
        </tr>
        <?php endforeach; ?> 
    <?php endif; ?> 
     <?php if (!empty($model->faturaDebitoCliente)) : ?> 
        <?php foreach ($model->faturaDebitoCliente as $key => $value) : ?>
        <tr>
            <td> Fatura debito de cliente</td>
            <td><?=yii::$app->ImgButton->Status($value->status). $value->getNumber() ?></td> 
            <td><?= Yii::$app->formatter->asCurrency($value->valor) ?></td>
            <td><?= Yii::$app->formatter->asDate($value->data) ?></td>
        </tr>
        <?php endforeach; ?> 
    <?php endif; ?>


     <?php if (!empty($model->faturaEletronica)) : ?> 
        <?php foreach ($model->faturaEletronica as $key => $value) : ?>
        <tr>
            <td> Fatura</td>
            <td><?=yii::$app->ImgButton->Status($value->status).   $value->getNumber() ?></td> 
            <td><?= Yii::$app->formatter->asCurrency($value->valor) ?></td>
            <td><?= Yii::$app->formatter->asDate($value->data) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>









</section>








<?php

Modal::begin([
    'id' => 'modal-historico',
    'header' => 'Histórico do processo',
    'toggleButton' => false,
    'size' => Modal::SIZE_LARGE
]); ?>
<div class="row">
    <!-- Nav tabs -->
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#workflow" aria-controls="workflow" role="tab"
                    data-toggle="tab">Workflow</a></li>
            <li role="presentation"><a href="#status-operacional" aria-controls="status-operacional" role="tab"
                    data-toggle="tab">Estado
                    Operacionais</a>
            </li>
            <li role="presentation"><a href="#status-financeiro" aria-controls="status-financeiro" role="tab"
                    data-toggle="tab">Estado
                    Financeiro</a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="workflow">
                <div class="table-responsive">

                    <table class="table table-hover">
                        <tr>
                            <th>Estado</th>
                            <th>Funcionário</th>
                            <th>Setor</th>
                            <th>Inicio</th>
                            <th>Fim</th>
                        </tr>
                        <?php if (!empty($model->processoWorkflow)) : ?>
                        <?php foreach ($model->processoWorkflow as $workflow) : ?>
                        <tr>
                            <td><?= $workflow->workflowStatus->descricao  . ' a ' . $formatter->asRelativeTime($workflow->status == 1 ? $workflow->in_data_hora : ($workflow->status == 2 ? $workflow->recebeu_data_hora : $workflow->out_data_hora)) ?>
                            </td>
                            <td><?= empty($workflow->user_id) ? '' : $workflow->user->name ?></td>
                            <td><?= empty($workflow->dsp_setor_id) ? '' : $workflow->setor->descricao ?></td>
                            <td><?= $formatter->asDateTime($workflow->data_inicio) ?></td>
                            <td><?= $formatter->asDateTime($workflow->data_fim) ?></td>

                        </tr>
                        <?php endforeach; ?>
                        <?php endif ?>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="status-operacional">
                <div class="table-responsive">

                    <table class="table table-hover">
                        <tr>
                            <th>Estado</th>
                            <th>Funcionário</th>
                            <th>Setor</th>
                            <th>Data</th>
                        </tr>
                        <?php if (!empty($model->processoStatusOperacional)) : ?>
                        <?php foreach ($model->processoStatusOperacional as $operacional) : ?>
                        <tr>
                            <td>
                                <?= $operacional->processoStatus->descricao ?>
                            </td>
                            <td><?= empty($operacional->user_id) ? '' : $operacional->user->name ?></td>
                            <td><?= empty($operacional->dsp_setor_id) ? '' : $operacional->setor->descricao ?></td>
                            <td><?= $formatter->asDateTime($operacional->created_at) ?></td>

                        </tr>
                        <?php endforeach; ?>
                        <?php endif ?>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="status-financeiro">
                <div class="table-responsive">

                    <table class="table table-hover">
                        <tr>
                            <th>Estado</th>
                            <th>Descricao</th>
                            <th>Funcionário</th>
                            <th>Setor</th>
                            <th>Data</th>
                        </tr>
                        <?php if (!empty($model->processoStatusFinanceiroHistorico)) : ?>
                        <?php foreach ($model->processoStatusFinanceiroHistorico as $financeiro) : ?>
                        <tr>
                            <td>
                                <?= $financeiro->processoStatus->descricao ?>
                            </td>
                            <td><?= $financeiro->descricao ?></td>
                            <td><?= empty($financeiro->user_id) ? '' : $financeiro->user->name ?></td>
                            <td><?= empty($financeiro->dsp_setor_id) ? '' : $financeiro->setor->descricao ?></td>
                            <td><?= $formatter->asDateTime($financeiro->created_at) ?></td>

                        </tr>
                        <?php endforeach; ?>
                        <?php endif ?>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Tab panes -->
    </div>
    <!-- Nav tabs -->
</div>








<?php Modal::end();






Modal::begin([
    'id' => 'modalObs',
    'header' => 'OBS',
    'toggleButton' => false,
    'size' => Modal::SIZE_DEFAULT
]);
Pjax::begin(['timeout' => 5000]);
$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'action' => Url::toRoute(['/dsp/processo/create-obs', 'id' => $model->id]),
    'method' => 'post'
]);
?>

<?= $form->field($modelObs, 'obs')->textArea() ?>
<br>
<div class="form-group">
    <?= Html::submitButton('Salvar', ['class' => 'btn']) ?>
    <?= Html::a('Cancelar', '#', ['class' => 'btn', 'onclick' => '$("#modalObs").modal("hide");']) ?>
</div>
<?php
ActiveForm::end();
Pjax::end();
Modal::end();
?>






<?php
Modal::begin([
    'id' => 'modalInitWorkflow',
    'header' => 'Inicializar Workflow',
    'toggleButton' => false,
    'size' => Modal::SIZE_DEFAULT,
    'options' => [
        // 'id' => 'kartik-modal',
        'tabindex' => false // important for Select2 to work properly
    ],
]);
Pjax::begin(['timeout' => 5000]);
$form2 = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'action' => Url::toRoute(['/dsp/processo-workflow/init', 'dsp_processo_id' => $model->id]),
    'method' => 'post'
]);
?>
<?= $form2->field($workflow, 'user_id')->widget(Select2::classname(), [
    'data' => ArrayHelper::map(\app\models\User::find()->where(['status' => 10])->orderBy('name')->all(), 'id', 'name'),
    'options' => ['placeholder' => ''],
    'pluginOptions' => [
        'allowClear' => true
    ],
])->label('Setor'); ?>
<div class="form-group">
    <?= Html::submitButton('Aceitar', ['class' => 'btn']) ?>
    <?= Html::a('Cancelar', '#', ['class' => 'btn', 'onclick' => '$("#modal").modal("hide");']) ?>
</div>
<?php
ActiveForm::end();
Pjax::end();



Modal::end();

























Modal::begin([
    'id' => 'modal-tarefa',
    'header' => 'Adicionar Tarefa',
    'toggleButton' => false,
    'size' => Modal::SIZE_DEFAULT
]);
Pjax::begin(['timeout' => 5000]);
$form2 = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'action' => Url::toRoute(['/dsp/tarefa/create-processo-tarefa', 'dsp_processo_id' => $model->id]),
    'method' => 'post'
]);
?>
<?= $form2->field($modelTarefa, 'dsp_tarefa_id')->widget(Select2::classname(), [
    'theme' => Select2::THEME_BOOTSTRAP,
    'data' => ArrayHelper::map(Tarefa::find()->orderBy('descricao')->all(), 'id', 'descricao'),
    'options' => ['placeholder' => ''],
    'pluginOptions' => [
        'allowClear' => true,

    ],

]); ?>
<br>
<div class="form-group">
    <?= Html::submitButton('Salvar', ['class' => 'btn']) ?>
    <?= Html::a('Cancelar', '#', ['class' => 'btn', 'onclick' => '$("#modal-tarefa").modal("hide");']) ?>
</div>
<?php
ActiveForm::end();
Pjax::end();
Modal::end();








?>
