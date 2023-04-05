<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AuthAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Nº documentos';


?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,

        'columns' => [
            
            //['class' => 'yii\grid\ActionColumn'],
           ['class' => 'yii\grid\ActionColumn','template'=>'{update}'],         
            'ano',
            'mes',
            'diario.descricao',
            'numero',

            
        ],
    ]); ?>

</div>
</section>