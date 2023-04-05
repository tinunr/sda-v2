<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\PerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title ='Candidatura Condicional'
?>



 <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="my-menu">
    <?= Yii::$app->MyMenu->geteMenuCPI($id)?>
</div>

   


   <?php 
    

   $gridColumns = [
                [  
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:10px;'],
                'header'=>'Ações',
                'template' => '{view}',
                'buttons' => [

                    //view button
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-fw fa-edit"></i></span>  Processo', $url, [
                                    'title' => Yii::t('app', 'View'),
                                    'class'=>'btn btn-xs',                                  
                        ]);
                    },
                ],

                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = Url::to(['/cpi-user-candidatura-prova-ingresso/processo', 'user_id'=>$model->user_id,'candidatura_prova_ingresso_id'=>$model->candidatura_prova_ingresso_id]);
                        return $url;
                }}

            ],
            ['class' => 'yii\grid\SerialColumn'],
            'user.nome_completo', 
            #'cpiCandidaturaProvaIngresso.nome', 
            #'nota_nuclear', 
            'cpiLocalProva.local',
            #'condicional' , 
            #'datatime',
            #'valor_pago:currency',

            #'cpi_validate',
            [  
               'label' => 'Adimitido',
               'attribute' => 'cpi_validate',
               'format' => 'html',
               'encodeLabel' => false,
               #'headerOptions' => ['style'=>'text-align:center'],
               'contentOptions' => function ($model) {
                    return ['class' => ($model->cpi_validate == 'NÃO') ? 'status-error':(($model->cpi_validate == 'SIM') ? 'status-success':'')] ;
                },
               /*'value' => function ($model) {
                    if ($model->cpi_validate == 'SIM') {
                     return Html::img(Url::to('@web/img/success.png'),['width' => '25px']);
                    } elseif ($model->cpi_validate == 'NÃO') {
                     
                        return Html::img(Url::to('@web/img/error.png'),['width' => '25px']);
                    }
                },*/
             ], 
             [  
               'label' => 'Notas',
               'attribute' => 'condicional',
               'format' => 'html',
               'encodeLabel' => false,
                #'headerOptions' => ['style'=>'text-align:center'],
               'contentOptions' => function ($model) {
                    return ['class' => ($model->condicional == 'SIM'||!empty($model->obs)) ? 'status-error':''] ;
                },
               'value' => function ($model) {
                     return $model->obs;
                },
             ],
       ];

// // Renders a export dropdown menu
// echo ExportMenu::widget([
//    'dataProvider' => $dataProvider,
//    'columns' => $gridColumns,
// ]);

// You can choose to render your own GridView separately
echo \kartik\grid\GridView::widget([
   'dataProvider' => $dataProvider,
   #'filterModel' => $searchModel,
   'columns' => $gridColumns,
   
]);

?>