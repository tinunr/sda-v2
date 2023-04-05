<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\PerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>



 <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="my-menu">
    <?= Yii::$app->MyMenu->geteMenuCPI($id)?>
</div>

    <div class="page-header">
      <h4>LISTA GERAL DOS CANDIDATOS |</h4>
    </div>

    <?= Html::a('<i class="fa fa-chevron-left"></i> Voltal', ['/cpi-candidatura-prova-ingresso/processo-curso', 'id'=>$id], ['class' => 'btn btn-warning']) ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'columns' => [
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
                  $url = Url::to(['/cpi-user-candidatura-prova-ingresso/processo', 'user_id'=>$model['user_id'],'candidatura_prova_ingresso_id'=>$model['candidatura_prova_ingresso_id']]);
                  return $url;
          }}

      ],
      ['class' => 'yii\grid\SerialColumn'],
      'nome_completo',
      'condicional' ,
      'valor_pago',
      'cpi_validate',
      'cpi_causa' ,

        ],
    ]);
    ?>
