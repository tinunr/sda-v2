<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\modules\fin\models\FaturaProvisoria;
use app\components\helpers\NumberHelper;

$formatter = \Yii::$app->formatter;

$faturaDefinitiva = $model->faturaDefinitiva;
$faturaProvisoria = FaturaProvisoria::find()
    ->where(['dsp_processo_id' => $model->dsp_processo_id])
    ->one();

$fpRc = Yii::$app->FinQuery->fpFaturaDefinitiva($faturaDefinitiva->id);
$total_honorario = 0;
$total_outros = 0;
$total_despesas = 0;
$total_honorariob = 0;
$total_outrosb = 0;
$total_despesasb = 0;
$regimeConfig = Yii::$app->params['regimeConfig'];
$valorBaseHonorario = Yii::$app->FinQuery->valorBaseHonorario($faturaDefinitiva->id);
$can_edit = true;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
// print_r($model->listItemns());die();
$this->title = 'Fatura número ' . $model->numero;

?>

<section>
    <div class="Curso-index">

        <div class="row button-bar">
            <div class="row pull-left">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning', 'title' => 'VOLTAR']) ?>
            </div>
            <div class="row pull-right">

                <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-warning', 'title' => 'ATUALIZAR ']) ?>
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
                    <?php if (empty($model->iud) ): ?>
                <?= Html::a('<i class="fas fa-file-export"></i> Enviar para PE', ['send-tope', 'id' => $model->id], [
                        'class' => 'btn btn-danger', 'title' => ' Enviar para PE',
                        'data' => [
                            'confirm' => 'PRETENDE REALENTE NVIAR ESTA FATURA A PLATAFORMA ELETRÓNICA DA EFATURA?',
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?php endif;?>

            </div>
        </div>
        <div class="titulo-principal">
            <h5 class="titulo">Fatura</h5>
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
						'label' => 'Nº FD',
                        'format' => 'raw',
						'value'=>function($model){
							return $model->faturaDefinitiva->getNumber();
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
                     [
                        'label' => 'PE',
                        'attribute' => 'iud',
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model->peStatus();
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
            <h5 class="titulo">Honorário</h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>
        <table class=" table">
            <thead>
                <tr>
                    <th class=" desc"><strong>Designação</strong></th>
                    <th class=" total"><strong>Valor</strong></th>
                    <th class=" total"><strong>Uni./Ton./Hec.</strong></th>
                    <th class=" total" style="width: 310px;"><strong>Posisão da Tabela</strong></th>
                    <th class=" total"><strong>Total</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$model->person->isencao_honorario | Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) > 0) : ?>

                <?php if (!empty($model->taxa_comunicaco)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->taxa_comunicaco ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['taxa_comunicaco'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($model->taxa_comunicaco) ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $posicao_tabela['taxa_comunicaco'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($model->taxa_comunicaco) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->tn)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->tn * $posicao_tabela['tn'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['tn'] ?></td>
                    <td class=" total"><?= $model->tn ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $posicao_tabela['tn'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($model->tn * $posicao_tabela['tn']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->form)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->form * $posicao_tabela['form'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['form'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->form ?></td>
                    <td class=" total"><?= $posicao_tabela['form'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($model->form * $posicao_tabela['form']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->regime_normal)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->regime_normal * $posicao_tabela['regime_normal'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['regime_normal'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->regime_normal ?></td>
                    <td class=" total"><?= $posicao_tabela['regime_normal'] ?></td>
                    <td class=" total">
                        <?= $formatter->asCurrency($model->regime_normal * $posicao_tabela['regime_normal']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->regime_especial)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->regime_especial * $posicao_tabela['regime_especial'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['regime_especial'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->regime_especial ?></td>
                    <td class=" total"><?= $posicao_tabela['regime_especial'] ?></td>
                    <td class=" total">
                        <?= $formatter->asCurrency($model->regime_especial * $posicao_tabela['regime_especial']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->exprevio_comercial)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->exprevio_comercial * $posicao_tabela['exprevio_comercial'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['exprevio_comercial'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->exprevio_comercial ?></td>
                    <td class=" total"><?= $posicao_tabela['exprevio_comercial'] ?></td>
                    <td class=" total">
                        <?= $formatter->asCurrency($model->exprevio_comercial * $posicao_tabela['exprevio_comercial']) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->expedente_matricula)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->expedente_matricula * $posicao_tabela['expedente_matricula'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['expedente_matricula'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->expedente_matricula ?></td>
                    <td class=" total"><?= $posicao_tabela['expedente_matricula'] ?></td>
                    <td class=" total">
                        <?= $formatter->asCurrency($model->expedente_matricula * $posicao_tabela['expedente_matricula']) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->dv)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->dv * $posicao_tabela['dv'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['dv'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->dv ?></td>
                    <td class=" total"><?= $posicao_tabela['dv'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($model->dv * $posicao_tabela['dv']) ?></td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($model->gti)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->gti * $posicao_tabela['gti'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['gti'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->gti ?></td>
                    <td class=" total"><?= $posicao_tabela['gti'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($model->gti * $posicao_tabela['gti']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->pl)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->pl * $posicao_tabela['pl'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['pl'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->pl ?></td>
                    <td class=" total"><?= $posicao_tabela['pl'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($model->pl * $posicao_tabela['pl']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($model->tce)) : ?>
                <?php $total_honorariob = $total_honorariob + $model->tce * $posicao_tabela['tce'] ?>
                <tr>
                    <td class=" desc"><?= $posicao_tabela_desc['tce'] ?></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->tce ?></td>
                    <td class=" total"><?= $posicao_tabela['tce'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($model->tce * $posicao_tabela['tce']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($model->acrescimo > 0) : ?>
                <?php $total_honorariob = $total_honorariob + ($model->acrescimo * $regimeConfig['valorPorItem']) ?>
                <tr>
                    <td class=" desc">Acréscimo</td>
                    <td class=" total"></td>
                    <td class=" total"><?= $model->acrescimo ?></td>
                    <td class=" total">100 por item</td>
                    <td class=" total"><?= ($model->acrescimo * $regimeConfig['valorPorItem']) ?></td>
                </tr>
                <?php endif; ?>

                <?php $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) - $total_honorariob; ?>
                <?php if (empty($model->tn) && !$model->person->isencao_honorario && ((Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) - $total_honorariob) > 0)) : ?>
                <tr>
                    <td class=" desc">Tabela Honorário</td>
                    <td class=" total">
                        <?= ($valorBaseHonorario['tipo'] && $valor_tabel_honorario > 500) ? $formatter->asCurrency($valorBaseHonorario['valor']) : '' ?>
                    </td>
                    <td class=" total">
                        <?= (!$valorBaseHonorario['tipo'] && $valor_tabel_honorario > 500) ? $valorBaseHonorario['valor'] : '' ?>
                    </td>
                    <td class=" desc">
                        <?php if ($valor_tabel_honorario == 500) : ?>
                        <?php echo "Valor Mininimo 500"; ?>
                        <?php elseif (!empty($model->posicao_tabela)) : ?>
                        <?= $model->posicao_tabela ?>
                        <?php else : ?>
                        <?= empty($faturaProvisoria->regimeItem->forma) ? (empty($faturaProvisoria->regimeItemItem->forma) ? 'Valor Mininimo 500' : $faturaProvisoria->regimeItemItem->forma) : $faturaProvisoria->regimeItem->forma ?>
                        <?php endif; ?>

                    </td>
                    <td class=" total">
                        <?= $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) - $total_honorariob) ?>
                    </td>
                    <?php $a = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) - $total_honorariob ?>
                    <?php $total_honorariob = $total_honorariob + $a ?>
                </tr>
                <?php endif; ?>


                <?php endif; ?>

                <?php foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->faturaDefinitiva->id) as $key => $modelItem) : ?>
                <tr>
                    <td class=" desc" colspan="4">
                        <?= str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($modelItem['valor']) ?></td>
                    <?php $total_outrosb = $total_outrosb +  $modelItem['valor']; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class=" desc" colspan="4"><strong>Total Geral Honorário</strong></td>
                    <td class=" total">
                        <strong><?= $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) + $total_outrosb) ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class=" desc" colspan="4"><strong>Base tributável</strong></td>
                    <td class=" total">
                        <strong><?= $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->fin_fatura_definitiva_id)) ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class=" desc" colspan="4"><strong>IVA</strong></td>
                    <td class=" total">
                        <strong><?= $formatter->asCurrency(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->faturaDefinitiva->id)) ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class=" desc" colspan="4"><strong>TOTAL: 
                            <?= NumberHelper::ConvertToWords($total_outrosb + Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->faturaDefinitiva->id)) ?></strong>
                    </td>
                    <td class=" total">
                        <strong><?= $formatter->asCurrency($total_outrosb + Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->fin_fatura_definitiva_id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->fin_fatura_definitiva_id)) ?></strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
