<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CursoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Grupo';
?>
<section>
<div class="Curso-index">



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

  <?php  echo Html::a('<i class="fa  fa-plus-square"></i> Novo', ['create-groupe'], ['class' => 'btn btn-success']) ?>
  </div></div>





    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'columns' => [

        ['class' => 'yii\grid\ActionColumn'],
           'name',
           'type',
           #'description',
           #'rule_name',
           #'data',
           #'created_at',
           'updated_at',
        ],
    ]); ?>

</div>
</section>