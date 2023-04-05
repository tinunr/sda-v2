<?php

use yii\helpers\Url;
use yii\widgets\ListView;
use yii\data\SqlDataProvider;
use yii\helpers\Html;

?>

<html lang="<?= Yii::$app->language ?>">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <link rel="apple-touch-icon-precomposed" href="#">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="#">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="#">
    <link href="#" type="text/css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
</head>
<body style="font-family: 'Open Sans', sans-serif !important;">



   
    <p style="text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-university"></i> LISTA CANDIDATURAS ADMITIDAS</strong>
    </p>

            

            <table style="width: 100%; height: 67px; 1px solid #eee; color:#666">
            <tbody>
                <tr>
                    <td style="width: 10%; padding: 5px; border: 1px solid #eee; color:#666"><strong>Ação</strong>
                    </td>
                    <td style="width: 20%; padding: 5px; border: 1px solid #eee; color:#666"><strong>Faculdade</strong>
                    </td>
                </tr>
                <?php $disciplinaData = Yii::$app->MyQuery->cpiListarDisciplinaPorLocalProva($candidatura_prova_ingresso_id,$cpi_local_prova_id);?>
                  <?php foreach ($disciplinaData->models as $disciplina):?>
                <tr>
                    <td style="width: 10%; padding: 5px; border: 1px solid #eee; color:#666">
                    <?= Html::a('<i class="fa fa-print"></i> Imprimir', ['/cpi-report/lista-candidatos','departamento_id'=>$disciplina['id'],'candidatura_prova_ingresso_id'=>$candidatura_prova_ingresso_id], ['class' => 'btn btn-xs', 'target'=>'_blank']) ?></td>

                    <td style="width: 20%; padding: 5px; border: 1px solid #eee; color:#666 "><?=$disciplina['nome']?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

       


</body>
</html>
