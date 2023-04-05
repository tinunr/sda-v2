<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\ButtonGroup;
use kartik\export\ExportMenu;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Recibo';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_searchRelatorio', ['model' => $searchModel]); ?>





  </div></div>


<?php   $gridColumns = [
    ['class' => 'yii\grid\DataColumn'],
    'numero',
    'person.nome',
    'valor:currency',    
    'data:date',

];

// Renders a export dropdown menu
echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'exportConfig' => [
        ExportMenu::FORMAT_EXCEL => false,
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_PDF => false,
    ],
]);

// You can choose to render your own GridView separately
echo \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => $gridColumns
]);?>

</div>
</section>
