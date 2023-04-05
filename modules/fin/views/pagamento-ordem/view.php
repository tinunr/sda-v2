<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'ORDEM DE PAGAMENTO nº ' . $model->numero . '/' . $model->bas_ano_id;
?>
<section>
    <div class="row">

        <div class="row button-bar">
            <div class="row pull-left">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning', 'title' => 'VOLTAR']) ?>
            </div>
            <div class="row pull-right">
                <?php if ($model->status == 1) : ?>
                <?= Html::a('<i class="fas fa-print"></i> Imprimir', ['view-pdf', 'id' => $model->id], ['class' => 'btn', 'target' => '_blanck', 'title' => 'Imprimir']) ?>
                <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
                        'class' => 'btn', 'title' => 'ANULAR',
                        'data' => [
                            'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                            'method' => 'post',
                        ],
                    ]) ?>

                <?php endif; ?>
                <?= Html::a('<i class="fas fa-check"></i> ' . ($model->send ? 'Invalidar' : 'Validar'), ['send-unsend', 'id' => $model->id], [
                    'class' => 'btn', 'title' => 'VALIDAR',
                    'data' => [
                        'confirm' => 'PRETENDE VALIDAR ESTE REGISTO?',
                        'method' => 'post',
                    ],
                ]) ?>


                <?php if ($model->send==1) : ?>
                <?= Html::a('<i class="fas fa-sync"></i> Efetuar Pagamento', ['/fin/pagamento/create-by-ordem', 'fin_pagamento_ordem_id' => $model->id], [
                        'class' => 'btn', 'title' => 'ANULAR',
                        'data' => [
                            'confirm' => 'PRETENDE REALENTE EFETUAR O PAGAMENTO?',
                            'method' => 'post',
                        ],
                    ]) ?>

                <?php endif; ?>

            </div>
        </div>
        <div class="titulo-principal">
            <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute' => 'numero',
                            'encodeLabel' => false,
                            'format' => 'html',
                            'value' => function ($model) {
                                return yii::$app->ImgButton->statusOrdemPagamento($model->status, $model->send) . $model->numero . '/' . $model->bas_ano_id;
                            }
                        ],
                        [
                            'label' => 'Pagamento',
                            'format' => 'raw',
                            'value' => (empty($model->fin_pagamento_id) ? '' : Html::a($model->pagamento->numero . '/' . $model->pagamento->bas_ano_id, ['/fin/pagamento/view', 'id' => $model->fin_pagamento_id], ['class' => 'btn-link', 'target' => '_blanck'])),
                        ],
                        'data:date',
                        'person.nome',
                        'descricao',


                    ],
                ]) ?></div>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'bancos.descricao',
                        'bancoContas.numero',
                        'documentoPagamento.descricao',
                        'valor:currency',
                        'numero_documento',
                        'data_documento:date',
                        ['label'=>'Validado Por','value'=>!empty($model->person_validacao)?$model->personValidacao->name:'',],
                        'data_validacao:date',

                    ],
                ]) ?>
            </div>
        </div>


    </div>


    <div class="titulo-principal">
        <h5 class="titulo">despesas</h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>


    <div class="table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Despesa</th>
                    <th scope="col">Valor</th>
                    <th scope="col">Valor Pago</th>
                    <th scope="col">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($model->item as $key => $value) : ?>
                <tr>
                    <th scope="row"><?= $i ?></th>
                    <td>
                        <?= Html::a($value->despesa->numero . ' - ' . $value->despesa->descricao, ['/fin/despesa/view', 'id' => $value->despesa->id], ['class' => 'btn-link', 'target' => '_blanck']) ?>

                    </td>

                    <td><?= Yii::$app->formatter->asCurrency($value->valor) ?></td>
                    <td><?= Yii::$app->formatter->asCurrency($value->valor_pago) ?></td>
                    <td><?= Yii::$app->formatter->asCurrency($value->valor - $value->valor_pago) ?></td>
                </tr>
                <?php $i++;
                endforeach; ?>

            </tbody>
        </table>
    </div>
</section>

<section>
    <?php $ano = \app\models\Ano::findOne($model->bas_ano_id)->ano;
    echo \app\components\widgets\FileBrowserView::widget([
        'item_id' => $model->id,
        'action_id' => 'fin_pagamento_ordem',
        'dir' => 'data/pagamentos_ordem/' . $ano . '/' . $model->numero,
        'can_edit' => true,
    ]); ?>
</section>
