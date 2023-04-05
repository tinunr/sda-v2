<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Financeiro Dashboard';
$formatter = Yii::$app->formatter;
$total_caixa = 0;
$total_banco = 0;

// $this->registerJsFile(Url::to('@web/src/fin/dashboard.js'), ['position' => \yii\web\View::POS_END]);
$this->registerJsFile(Url::to('@web/js/plugin/Chart.js'), ['position' => \yii\web\View::POS_BEGIN]);
$this->registerJsFile(Url::to('@web/js/plugin/jquery-3.3.1.min.js'), ['position' => \yii\web\View::POS_BEGIN]);
?>


<div class="row">

    <div class="col-md-6 col-xs-6">
        <section style="position: relative; height:46vh;">
            <div class="row">
                <div class="titulo-principal">
                    <h5 class="titulo">Total de Fatura provisória</h5>
                    <div id="linha-longa">
                        <div id="linha-curta"></div>
                    </div>
                </div>

                <div class="chart-container" style="position: relative; height:35vh; ">
                    <canvas id="myChartNumeroFaturas"></canvas>
                </div>
                <section>

            </div>
    </div>



    <div class="col-md-6 co-xs-6">
        <section style="position: relative; height:46vh;">
            <div class="row">

                <div class="titulo-principal">
                    <h5 class="titulo">Valor de Honorário</h5>
                    <div id="linha-longa">
                        <div id="linha-curta"></div>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height:35vh; ">
                    <canvas id="valorHonorario"></canvas>
                </div>
            </div>
        </section>

    </div>
</div>




<div class="row">
    <div class="col-md-6">
        <section>
            <div class="titulo-principal">
                <h5 class="titulo">disponibilidade</h5>
                <div id="linha-longa">
                    <div id="linha-curta"></div>
                </div>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Disponibilidade</th>
                        <th>Caixa</th>
                        <th>Banco</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($disponilidadeFinanceira as $key => $value) : ?>
                    <tr>
                        <td><?= $value['sigla'] . ' - ' . $value['numero'] ?></td>
                        <td><?php
                                if ($value['tipo'] == 'CAIXA') {
                                    $total_caixa = $total_caixa + $value['saldo'];
                                    echo $formatter->asCurrency($value['saldo']);
                                }
                                ?>
                        </td>
                        <td><?php
                                if ($value['tipo'] == 'BANCO') {
                                    $total_banco = $total_banco + $value['saldo'];
                                    echo $formatter->asCurrency($value['saldo']);
                                }
                                ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="">Total</th>
                        <th><?= $formatter->asCurrency($total_caixa) ?></th>
                        <th><?= $formatter->asCurrency($total_banco) ?></th>
                    </tr>
                </tfoot>
            </table>
        </section>

    </div>




    <div class="col-md-6">
        <section>
            <div class="titulo-principal">
                <h5 class="titulo">valores do cliente</h5>
                <div id="linha-longa">
                    <div id="linha-curta"></div>
                </div>
            </div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Valores a Favor de cliente</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Processo</td>
                        <td><?= $formatter->asCurrency($valor_a_favor['processo']) ?></td>
                    </tr>
                    <tr>
                        <td>Adiantamento</td>
                        <td><?= $formatter->asCurrency($valor_a_favor['adiantamento']) ?></td>
                    </tr>
                    <tr>
                        <td>Aviso de Credito</td>
                        <td><?= $formatter->asCurrency($valor_a_favor['aviso_de_credito']) ?></td>
                    </tr>
                    <tr>
                        <td>Nota de Credito</td>
                        <td><?= $formatter->asCurrency($valor_a_favor['nota_de_credito']) ?></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <th><?= $formatter->asCurrency($valor_a_favor['processo'] + $valor_a_favor['adiantamento'] + $valor_a_favor['aviso_de_credito'] + $valor_a_favor['nota_de_credito']) ?>
                        </th>
                    </tr>
                    <tr>
                        <td>#</td>
                        <td>#</td>
                    </tr>
                    <tr>
                        <th>Valores a dever de cliente</th>
                        <th>Valor</th>
                    </tr>
                    <tr>
                        <td>Processo</td>
                        <td><?= $formatter->asCurrency($valor_a_dever_processo) ?></td>
                    </tr>
                </tbody>
            </table>
        </section>

    </div>
</div>



<script>
var UrlFinFaturaProvisoriaFaturasJason = "<?= Url::to(['/fin/fatura-provisoria/faturas-json']) ?>";

function float2dollar(value) {
    return (value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function renderChart(data, labels) {
    var ctx = document.getElementById("myChartNumeroFaturas").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: "Nº de Faturas Provisória",
                    // backgroundColor: "rgba(255,99,132,0.2)",
                    // borderColor: "rgba(255,99,132,1)",
                    // borderWidth: 2,
                    // hoverBackgroundColor: "rgba(255,99,132,0.4)",
                    // hoverBorderColor: "rgba(255,99,132,1)",
                    backgroundColor: 'rgba(93, 173, 226)',
                    data: data,
                },
                {
                    label: "Nº de Faturas Provisória",
                    type: 'line',
                    data: data,
                },
            ]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            legend: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                    }
                }]
            },
        }
    });
}




function renderChart2(data, labels) {
    console.log(data[1]);
    var ctx = document.getElementById("valorHonorario").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: "Nº de Faturas Provisória",
                    backgroundColor: "rgba(34, 153, 84)",
                    borderColor: "rgba(34, 153, 84)",
                    borderWidth: 2,
                    hoverBackgroundColor: "rgba(34, 153, 84)",
                    hoverBorderColor: "rgba(34, 153, 84)",
                    data: data,
                },
                {
                    label: "Nº de Faturas Provisória",
                    data: data,
                    type: 'line',
                },
            ]
        },
        options: {
            responsive: true,
            legend: false,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            return float2dollar(value);
                        }
                    }
                }]
            },
        }
    });
}

function getChartData() {
    $.ajax({
        url: UrlFinFaturaProvisoriaFaturasJason,
        success: function(result) {
            console.log(result);
            var data = [];
            data.push(result.chartData.dataNFaturaP);
            data.push(result.chartData.valorHonorario);
            var labelsNFaturas = result.chartData.labels;
            renderChart(data[0], labelsNFaturas);
            renderChart2(data[1], labelsNFaturas);
        },
        error: function(err) {
            $("#loadingMessage").html("Error");
        }
    });
}
$('document').ready(
    function() {
        getChartData();
    }
);
</script>
