<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IlhasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Confirução de Lançamento';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>




  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?=Html::a('<i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success','title'=>'NOVO']);?>


  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
            ['class' => 'yii\grid\SerialColumn'],
            'lancamentoTipo.descricao',
            'mes.descricao',
            'diario.descricao',
            'descricao',
        ],
    ]); ?>
</div>
