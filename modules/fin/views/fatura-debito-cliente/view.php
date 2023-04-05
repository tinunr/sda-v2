<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\fin\models\FaturaProvisoria;
use app\components\helpers\NumberHelper;

$formatter = \Yii::$app->formatter;
$faturaProvisoria = FaturaProvisoria::find()
    ->where(['dsp_processo_id' => $model->dsp_processo_id])
    ->one();

$fpRc = Yii::$app->FinQuery->fpFaturaDefinitiva($model->fin_fatura_definitiva_id);
$total_honorario = 0;
$total_outros = 0;
$total_despesas = 0;
$total_honorariob = 0;
$total_outrosb = 0;
$total_despesasb = 0;
$regimeConfig = Yii::$app->params['regimeConfig'];
$valorBaseHonorario = Yii::$app->FinQuery->valorBaseHonorario($model->fin_fatura_definitiva_id);
$can_edit = true;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Nota de débito de cliente ' . $model->numero;

?>

<section>
    <div class="Curso-index">

        <div class="row button-bar">
            <div class="row pull-left">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning', 'title' => 'VOLTAR']) ?>
            </div>
            <div class="row pull-right">

                <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-warning', 'title' => 'ATUALIZAR ']) ?>
                <?php Html::a('<i class="fas fa-print"></i> Imprimir', ['view-pdf', 'id' => $model->id], ['class' => 'btn btn-warning', 'target' => '_blanck', 'title' => 'Imprimir']) ?>
                <?= Html::a('<i class="fas fa-print"></i> Imprimir', ['view-pdf-new', 'id' => $model->id], ['class' => 'btn btn-warning', 'target' => '_blanck', 'title' => 'Imprimir']) ?>

<?php if ($model->status) : ?>
                <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
                        'class' => 'btn btn-danger', 'title' => 'ANULAR',
                        'data' => [
                            'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?php endif;?>
            </div>
        </div>
        <div class="titulo-principal">
            <h5 class="titulo">NOTA DE DÉBITO</h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>


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
                        'label' => 'Nº Processo',
                        'attribute' => 'processo',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a($model->processo->numero . '/' . $model->processo->bas_ano_id, ['/dsp/processo/view', 'id' => $model->dsp_processo_id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']);
                        }
                    ],
					[
						'label' => 'Nº FD',
                        'format' => 'raw',
						'value'=>function($model){
							return $model->faturaDefinitiva->getNumber();
						}
					],
                    [
                        'label' => 'NORD',
                        'format' => 'raw',
                        'value' => (empty($model->nord) ? '' : Html::a($model->nord, ['/dsp/nord/view', 'id' => $model->nord], ['class' => 'btn-link', 'target' => '_blanck'])),
                    ],
                    [
                        'label' => 'FP / REC',
                        'format' => 'raw',
                        'value' => $fpRc['fatura_provisorias'] . ' / ' . $fpRc['recibos'],
                    ],
                    'person.nome',
                    'valor:currency',
                ],
            ])
            ?>
        </div>
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'data:date',
                    'processo.nord.despacho.id',
                    'processo.nord.despacho.data_registo:date',
                    'processo.nord.despacho.n_receita',
                    'processo.nord.despacho.data_receita:date',
                     [
                        'label' => 'Contabilidade',
                        'format' => 'raw',
                        'value' => function ($model) { 
                            if (($id =$model->inContabilidade())!==null) { 
                                return Html::a('Contabilidade', ['/cnt/razao/view', 'id' => $id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']);
                            }
                        }
                    ],
                ],
            ])



            ?>
        </div>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'descricao',
            ],
        ])



        ?>
    </div>
</section>
<section>
    <div class="row">
        <div class="titulo-principal">
            <h5 class="titulo">Despesas</h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class=" desc" colspan="2"><strong>Designação</strong></th>
                    <th class=" total"><strong>Valor</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->fin_fatura_definitiva_id) as $key => $modelItem) : ?>
                <tr>
                    <td class=" desc" colspan="2">
                        <?= str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($modelItem['valor']) ?></td>
                    <?php $total_despesasb = $total_despesasb +  $modelItem['valor']; ?>
                </tr>
                <?php endforeach; ?>

            </tbody>
            <tfoot>
                <tr>
                    <td class=" desc" colspan="2"><strong>TOTAL:
                            <?= NumberHelper::ConvertToWords($total_despesasb) ?> escudos</strong></td>
                    <td class=" total"><strong><?= $formatter->asCurrency($total_despesasb) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
