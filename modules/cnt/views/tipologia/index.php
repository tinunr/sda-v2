<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IlhasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipologia';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>




  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  <?php  echo Html::a('<i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>

  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
            ['class' => 'yii\grid\SerialColumn'],
            'abreviatura',
            'descricao',
            'numero_destino',
            'numero_destino_b',
            
        ],
    ]); ?>
</div>
