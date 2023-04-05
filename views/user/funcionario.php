<?php

use yii\helpers\Html;
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


    
    <p style="margin: 10px 0px; text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-users"></i> Funcionarios  </strong></p>
  <?php  echo $this->render('_searchFuncionario', ['model' => $searchModel]); ?>


    </div> 
    </div> 
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,

        'columns' => [
            
            ['class' => 'yii\grid\SerialColumn'],            
            'name',
            'username',
            'status',

            
        ],
    ]); ?>

</div>
</section>