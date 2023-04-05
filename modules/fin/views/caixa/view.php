<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\modules\fin\models\Caixa;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Caixa - ' . $model->descricao;
?>

<section>
    <div class="row">


        <p>
            <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn', 'title' => 'VOLTAR']) ?>
            <?php if ($model->status == Caixa::OPEN_CAIXA && Yii::$app->user->can('fin/caixa/update-saldo')) : ?>
            <?= Html::a(Yii::$app->ImgButton->Img('update') . ' Saldo', ['update-saldo', 'id' => $model->id], [
                    'class' => 'btn btn-danger', 'title' => 'ANULAR',
                    'data' => [
                        'confirm' => 'PRETENDE REALMENTE ATUALIZAR SALDO ?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>

            <?php if ($model->status == Caixa::CLOSE_CAIXA && Yii::$app->user->can('fin/caixa/open-caixa')) : ?>
            <?= Html::a('<i class="fas fa-undo"></i> Anular' . ' Abrir caixa', ['open-caixa', 'id' => $model->id], [
                    'class' => 'btn btn-danger', 'title' => 'ANULAR',
                    'data' => [
                        'confirm' => 'PRETENDE REALMENTE ABRIR ESA CAIXA ?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>

        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'bancoConta.banco.descricao',
                'bancoConta.numero',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return \app\modules\fin\services\CaixaService::ImgCaixaStaus($model) . ' ' . Caixa::STATUS_TEXTO[$model->status];;
                    }
                ],
                'descricao',
                // 'status',
                'data_abertura:dateTime',
                'saldo_inicial:currency',
                'data_fecho:dateTime',
                'saldo_fecho:currency',
            ],
        ])
        ?>
        <?= Html::hiddenInput('fin_caixa_id', $model->id, ['id' => 'fin_caixa_id', 'class' => 'form-control']); ?>

    </div>


</section>

<section>

    <?= Html::beginForm(['/fin/caixa/close-caixa', 'id' => $model->id]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'id' => 'grid',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'created_at:dateTime',

            [
                'class' => '\yii\grid\CheckboxColumn',


                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model->caixa->status == 1) {
                        return ['value' => $key];
                    }
                    return ['style' => ['display' => 'none']]; // OR ['disabled' => true]
                },
                'contentOptions' => [
                    'onclick' => 'updateForm(this)',
                    //'style' => 'cursor: pointer'
                ],
            ],
            [
                'attribute' => 'caixaOperacao.descricao',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function ($model) {
                    return Yii::$app->ImgButton->ImgCaixaOpercacao($model->fin_caixa_operacao_id) . ' ' . $model->caixaOperacao->descricao;
                }
            ],
            'descricao',
            'valor_entrada:currency',
            'valor_saida:currency',
            [
                'attribute' => 'saldo',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function ($model) {
                    return Yii::$app->formatter->asCurrency(Yii::$app->FinCaixa->caixaTransacaoSaldo($model->id));
                }
            ],

        ],
    ]); ?>
    <?php if ($model->status == 1 && Yii::$app->user->can('fin/caixa/close-caixa')) : ?>
    <?= Html::submitButton(Yii::$app->ImgButton->Img('save') . ' Fechar Caixa', [
            'id' => 'avancar',
            'class' => 'btn btn-info',
            'style' => [
                'display' => 'block'
            ],
            'data' => [
                'confirm' => 'Pretende realmente fechar a caixa?',
                'method' => 'post',
            ],
        ]); ?>
    <?php endif; ?>
    <?php Html::endForm(); ?>
</section>