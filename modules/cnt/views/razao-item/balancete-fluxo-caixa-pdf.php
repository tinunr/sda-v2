<?php
$formatter = Yii::$app->formatter;
$total_debito_anterior = 0;
$total_credito_anterior = 0;
$total_debito_atual = 0;
$total_credito_atual = 0;
$total_saldo_debito = 0;
$total_saldo_credito = 0;

// print_r($dataProvider->getModels());die();
?>

<div class="row">
    <p class="text-center">Mês: <?=$titleReport?></p>
    <p class="text-center">Moeda: Nacional</p>
    <table class="table tabe-striped ">
        <thead>
            <tr>
                <th>Conta</th>
                <th></th>
                <th colspan="2" class="text-center">Acumulados do Mês</th>
                <th colspan="2" class="text-center">Acumulado para mês Seguinte</th>
                <th colspan="2" class="text-center">Saldos </th>
            </tr>
            <tr>
                <th>Cod.</th>
                <th>Descrição</th>
                <th>Debitos</th>
                <th>Creditos</th>
                <th>Debitos</th>
                <th>Creditos </th>
                <th>Devidores </th>
                <th>Credores</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($palnaoConta as $key => $model):?>

                <?php
                    $debitoAtual = Yii::$app->CntQuery->getDebitoAtualPFC($model->id,$data['RazaoItemSearch']['begin_ano'],$data['RazaoItemSearch']['begin_mes'],$data['RazaoItemSearch']['cnt_plano_terceiro_id'],$data['RazaoItemSearch']['bas_formato_id']);
                    $creditoAtual = Yii::$app->CntQuery->getCreditoAtualPFC($model->id,$data['RazaoItemSearch']['begin_ano'],$data['RazaoItemSearch']['begin_mes'],$data['RazaoItemSearch']['cnt_plano_terceiro_id'],$data['RazaoItemSearch']['bas_formato_id']);

                ?>
                <?php if($debitoAtual >0||$creditoAtual>0):?>
                <?php
                    $debitoAnteriro = Yii::$app->CntQuery->getDebitoAnteriorPFC($model->id,$data['RazaoItemSearch']['begin_ano'],$data['RazaoItemSearch']['begin_mes'],$data['RazaoItemSearch']['cnt_plano_terceiro_id'],$data['RazaoItemSearch']['bas_formato_id']);
                    $creditoAnterior = Yii::$app->CntQuery->getCreditoAnteriorPFC($model->id,$data['RazaoItemSearch']['begin_ano'],$data['RazaoItemSearch']['begin_mes'],$data['RazaoItemSearch']['cnt_plano_terceiro_id'],$data['RazaoItemSearch']['bas_formato_id']);

                    if (($value = ($debitoAnteriro -$creditoAnterior))>0) {
                        $saldo_d = $value;
                        $saldo_c = 0;
                    }else{
                        $saldo_d = 0;
                        $saldo_c = -($value);
                    }
                    
                    $total_debito_anterior = $total_debito_anterior + $debitoAnteriro;
                    $total_credito_anterior = $total_credito_anterior + $creditoAnterior;
                    $total_debito_atual = $total_debito_atual + $debitoAtual;
                    $total_credito_atual = $total_credito_atual + $creditoAtual;
                    
                ?>
            <tr>
                <td><?=$model['codigo']?></td>
                <td><?=$model['descricao']?></td>                
                <td><?=$formatter->asCurrency($debitoAtual)?></td>
                <td><?=$formatter->asCurrency($creditoAtual)?></td>
                <td><?=$formatter->asCurrency($debitoAnteriro)?></td>
                <td><?=$formatter->asCurrency($creditoAnterior)?></td>
                <td><?=$formatter->asCurrency($saldo_d)?></td>
                <td><?=$formatter->asCurrency($saldo_c)?></td>
            </tr>
        <?php endif;?>
        <?php endforeach;?>
        </tbody>
        <tfoot>
            <tr>
                <th>Totais</th>
                <th></th>
                <th><?=$formatter->asCurrency($total_debito_anterior)?></th>
                <th><?=$formatter->asCurrency($total_credito_anterior)?></th>
                <th><?=$formatter->asCurrency($total_debito_atual)?></th>
                <th><?=$formatter->asCurrency($total_credito_atual)?></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>