<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\modules\fin\models\FaturaProvisoria;
use app\components\helpers\NumberHelper;

$formatter = \Yii::$app->formatter;
$faturaProvisoria = FaturaProvisoria::find()
    ->where(['dsp_processo_id' => $model->dsp_processo_id])
    ->one();

$fpRc = Yii::$app->FinQuery->fpFaturaDefinitiva($model->id);
$total_honorario = 0;
$total_outros = 0;
$total_despesas = 0;
$total_honorariob = 0;
$total_outrosb = 0;
$total_despesasb = 0;
$regimeConfig = Yii::$app->params['regimeConfig'];
$valorBaseHonorario = Yii::$app->FinQuery->valorBaseHonorario($model->id);
$can_edit = true;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Fatura definitiva ' . $model->numero;

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
				<?= Html::a('<i class="fas fa-print"></i> Imprimir Resumo', ['view-pdf-resumo', 'id' => $model->id], ['class' => 'btn btn-warning', 'target' => '_blanck', 'title' => 'Imprimir']) ?>
                <?= Html::a('<i class="fas fa-print"></i> Imprimir', ['view-pdf-new', 'id' => $model->id], ['class' => 'btn btn-warning', 'target' => '_blanck', 'title' => 'Imprimir']) ?>


                <?= Html::a('<i class="fas fa-file-export"></i> Gerar Faturas', ['create-fatura', 'id' => $model->id], [
                    'class' => 'btn btn-danger', 'title' => 'Gerar Fatura',
                    'data' => [
                        'confirm' => 'PRETENDE REALENTE GERAR AS FATURAS?',
                        'method' => 'post',
                    ],
                ]) ?>

                <?php if ($model->status) : ?>
                <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
                        'class' => 'btn btn-danger', 'title' => 'ANULAR',
                        'data' => [
                            'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?= Html::a('<i class="fas fa-undo"></i> ' . ($model->send ? 'Invalidar' : 'Validar'), ['send-unsend', 'id' => $model->id], [
                        'class' => 'btn btn-danger', 'title' => 'ANULAR',
                        'data' => [
                            'confirm' => 'MARCAR ESTA FATURA COMO ' . ($model->send ? 'NÃO CONFERIDO' : 'CONFERIDO'),
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="titulo-principal">
            <h5 class="titulo">Fatura Definitiva</h5>
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
                            return Yii::$app->ImgButton->statusSend($model->status, $model->send) . ' ' . $model->getNumber()  ;
                        }
                    ],
                    [
                        'label' => 'Nº Processo | Nord',
                        'attribute' => 'processo',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->processo->getNumber() . ' | '  .  empty($model->processo->nord)?'': $model->processo->nord->getNumber();
                        }
                    ],
                    [
                        'label' => 'Fatura | Fatura Debito Cliente',
                        'format' => 'raw',
                        'value' => $model->getFaturaEletronicaLink().' | '.$model->getFaturaDebitoClienteLink(),
                    ],
                    [
                        'label' => 'FP | REC | EC',
                        'format' => 'raw',
                        'value' =>$model->faturaProvisoriasLink().'  | '. $model->recebimentosLink().' | '.$model->encontroDeContasLink(),
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
                            $id = Yii::$app->CntQuery->inContabilidade(\app\modules\cnt\models\Documento::FATURA_DEFINITIVA, $model->id);
                            if ($id) {
                                $can_edit = false;
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
                <?php if (!$model->person->isencao_honorario | Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) > 0) : ?>
 
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
				
			 <?php if (!empty($model->valorOutrosHonorarios())) : ?>
                <?php $total_honorariob = $total_honorariob + $model->valorOutrosHonorarios() ?>
                <tr>
                    <td class=" desc">Outros Honorários</td>
                    <td class=" total"></td>
                    <td class=" total"></td>
                    <td class=" total"></td>
                    <td class=" total"><?= $formatter->asCurrency($model->valorOutrosHonorarios()) ?></td>
                </tr>
                <?php endif; ?>

                <?php $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorariob; ?>
                <?php if (empty($model->tn) && !$model->person->isencao_honorario && ((Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorariob) > 0)) : ?>
                <tr>
                    <td class=" desc">Tabela Honorário</td>
                    <td class=" total">
                        <?= ($valorBaseHonorario['tipo'] && $valor_tabel_honorario > 500) ?  $valorBaseHonorario['valor'] : '' ?>
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
                        <?= $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorariob) ?>
                    </td>
                    <?php $a = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) - $total_honorariob ?>
                    <?php $total_honorariob = $total_honorariob + $a ?>
                </tr>
                <?php endif; ?>
            </tbody>
            <?php endif; ?>
			

            <tfoot>
                <tr>
                    <td class=" desc" colspan="4"><strong>Total Geral Honorário</strong></td>
                    <td class=" total">
                        <strong><?= $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id)) ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class=" desc" colspan="4"><strong>IVA sobre Honorário</strong></td>
                    <td class=" total">
                        <strong><?= $formatter->asCurrency(Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class=" desc" colspan="4"><strong>SUB. TOTAL (Honorário mais IVA de Honorário)
                            <?= NumberHelper::ConvertToWords(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) ?></strong>
                    </td>
                    <td class=" total">
                        <strong><?= $formatter->asCurrency(Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->id) + Yii::$app->FinQuery->valorIvaHonorarioFaturaDefinitiva($model->id)) ?></strong>
                    </td>
                </tr>
            </tfoot>

        </table>















    </div>


</section>
<section>
    <div class="row">

        <div class="titulo-principal">
            <h5 class="titulo">Outros</h5>
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
                <?php foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->id) as $key => $modelItem) : ?>
                <tr>
                    <td class=" desc" colspan="2">
                        <?= str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT) . ' - ' . $modelItem['descricao'] ?></td>
                    <td class=" total"><?= $formatter->asCurrency($modelItem['valor']) ?></td>
                    <?php $total_outrosb = $total_outrosb +  $modelItem['valor']; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class=" desc" colspan="2"><strong>SUB. TOTAL: (Outros)
                            <?= NumberHelper::ConvertToWords($total_outrosb) ?> escudos</strong></td>
                    <td class=" total"><strong><?= $formatter->asCurrency($total_outrosb) ?></strong></td>
                </tr>
            </tfoot>
        </table>
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
                <?php foreach (Yii::$app->FinQuery->despesaFaturaDefinitiva($model->id) as $key => $modelItem) : ?>
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
                    <td class=" desc" colspan="2"><strong>SUB. TOTAL: (Despesas)
                            <?= NumberHelper::ConvertToWords($total_despesasb) ?> escudos</strong></td>
                    <td class=" total"><strong><?= $formatter->asCurrency($total_despesasb) ?></strong></td>
                </tr>
                <tr>
                    <td class=" desc" colspan="2"><strong>TOTAL: <?= NumberHelper::ConvertToWords($model->valor) ?>
                            escudos</strong></td>
                    <td class=" total"><strong><?= $formatter->asCurrency($model->valor) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
