<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$formatter = Yii::$app->formatter;

$total = 0;
$total_despesa = 0;
$total_agencia = 0;

/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
$this->title = 'Fatura Proforma recino nº' . $model->numero;
?>

<div class="desciplina-perfil-view">
    <section>
        <div class="row button-bar">
            <div class="row pull-left">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning', 'title' => 'VOLTAR']) ?>
            </div>
            <div class="row pull-right">
                <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-warning', 'title' => 'ATUALIZAR ']) ?>
                <?= Html::a('<i class="fas fa-print"></i> Imprimir', ['view-pdf', 'id' => $model->id], ['class' => 'btn btn-warning', 'target' => '_blanck', 'title' => 'Imprimir']) ?>
                <?php if ($model->status) : ?>
                <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
                        'class' => 'btn btn-danger', 'title' => 'ANULAR',
                        'data' => [
                            'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>

                <?php if ($model->status) : ?>
                <?= Html::a('<i class="fas fa-share"></i> ' . ($model->send ? 'Invalidar' : 'Validar'), ['send-unsend', 'id' => $model->id], [
                        'class' => 'btn btn-success', 'title' => 'ENVIAR',
                        'data' => [
                            'confirm' => 'MARCAR ESTA FATURA COMO ' . ($model->send ? 'NÃO CONFERIDO' : 'CONFERIDO'),
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>

                <?php if ($model->status && $model->send) : ?>
                <?= Html::a('<i class="fas fa-sync"></i> Criar Fatura Provisória', ['create-fatura-frovisoria', 'id' => $model->id], [
                        'class' => 'btn btn-success', 'title' => 'CRIAR FATURA PROVISÓRIA',
                        'data' => [
                            'confirm' => 'PRETENDE REALMENTE CRIAR A FUTURA PROVISÓRIA?.',
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
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Yii::$app->ImgButton->statusSend($model->status, $model->send) . ' ' . $model->numero . '/' . $model->bas_ano_id;
                            }
                        ],
                        [
                            'label' => 'NORD / Processo',
                            'format' => 'raw',
                            'value' => (empty($model->nord) ? '' : Html::a($model->processo->nord->numero . '/' . $model->processo->nord->bas_ano_id, ['/dsp/nord/view', 'id' => $model->nord], ['class' => 'btn-link', 'target' => '_blanck'])) . ' /  ' . (empty($model->dsp_processo_id) ? '' : Html::a($model->processo->numero . '/' . $model->processo->bas_ano_id, ['/dsp/processo/view', 'id' => $model->dsp_processo_id], ['class' => 'btn-link', 'target' => '_blanck']))
                        ],
                        'data:date',
                        'valor:currency',


                    ],
                ])



                ?>
            </div>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [

                        'dsp_regime_id',
                        'dsp_regime_descricao',
                        'dsp_regime_item_id',
                        'dsp_regime_item_valor',
                        'dsp_regime_item_tabela_anexa',
                        'dsp_regime_item_tabela_anexa_valor',

                    ],
                ])



                ?>
            </div>
        </div>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [

                'person.nome',
                'mercadoria',
            ],
        ])
        ?>
        <div class="row">

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [

                        'impresso_principal',
                        'impresso_intercalar',
                        'dv',
                        'tce',
                        'pl',
                        'tn',
                        'gti',

                    ],
                ])



                ?>
            </div>

            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [

                        'fotocopias',
                        'qt_estampilhas',
                        'form',
                        'regime_normal',
                        'regime_especial',
                        'exprevio_comercial',
                        'expedente_matricula',

                    ],
                ])



                ?>
            </div>
        </div>














        <div class="row">
            <div class="col-md-12">

                <div class="titulo-principal">
                    <h5 class="titulo">item da fatura provisória</h5>
                    <div id="linha-longa">
                        <div id="linha-curta"></div>
                    </div>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ITEM</th>
                            <th scope="col">VALOR</th>
                            <th scope="col">ORG</th>
                            <th scope="col">A FAVOR </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($modelsFaturaProformaItem as $key => $item) :  ?>
                        <?php
                            if ($item->item->person->id == 1) {
                                $total_agencia = $total_agencia + $item->valor;
                            } else {
                                $total_despesa = $total_despesa + $item->valor;
                            }
                            $total = $total + $item->valor;
                            ?>
                        <tr>
                            <th scope="row"><?= $i ?> <input type="checkbox" id="vehicle1" name="vehicle1" value="Bike">
                            </th>
                            <td><?= str_pad($item->item->id, 2, '0', STR_PAD_LEFT) . ' - ' . $item->item->descricao ?>
                            </td>
                            <td><?= $formatter->asCurrency($item->valor) ?></td>
                            <td><?= $item->item_origem_id ?></td>
                            <td><?= $item->item->person->nome ?></td>
                        </tr>


                        <?php $i++;
                        endforeach; ?>
                        <tr>
                            <th scope="row" colspan="2">TOTAL</th>
                            <th><?= $formatter->asCurrency($total) ?></th>
                            <th colspan="2">Total Despesa: <?= $formatter->asCurrency($total_despesa) ?> | Total
                                Agência:
                                <?= $formatter->asCurrency($total_agencia) ?></th>

                        </tr>
                    </tbody>
                </table>

            </div>



    </section>



</div>

</div>


</div>
