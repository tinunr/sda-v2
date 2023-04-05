<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Utilizadores';
?>
<section>
<div class="user-index">


    
    <p style="margin: 10px 0px; text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-users"></i> UTILIZADORES  </strong></p>
  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>


  <?=Html::a('<i class="fa fa-user-plus"></i> Novo', ['signup'], ['class' => 'btn btn-success']); ?>
    </div> 
    </div> 
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,

        'columns' => [
            
            ['class' => 'yii\grid\ActionColumn',
            'visibleButtons' =>
              [
                  'view' => Yii::$app->user->can('app/user/view'),
                  'update' => Yii::$app->user->can('app/user/update'),
                  'delete' => Yii::$app->user->can('app/user/delete')
              ]
            ],
            ['class' => 'yii\grid\SerialColumn'],            
            'name',
            'username',
            //'status',
            [
                'encodeLabel' => false,
                'format' => 'raw',
                'headerOptions' => ['style'=>'text-align:center'],'value' => function($model){
                   if ($model->status==10) {
                      return Html::img(Url::to('@web/img/greenBall.png'));  
                    }else{
                    return Html::img(Url::to('@web/img/redBal.png'));  

                   }
                }
            ],

            
        ],
    ]); ?>

</div>
</section>