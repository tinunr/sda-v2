<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AuthAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<section>
<div class="auth-assignment-index">



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

  <?php  echo Html::a('<i class="fa  fa-plus-square"></i> Novo', ['create'], ['class' => 'btn btn-success']) ?>
  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,

        'columns' => [
            
            ['class' => 'yii\grid\ActionColumn'],
            #['class' => 'yii\grid\SerialColumn'],            
            'user_id',
            'user.name',
            'item_name',
            #'created_at',

            
        ],
    ]); ?>

</div>
</section>