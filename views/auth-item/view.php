<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use app\models\AuthItem;
use yii\bootstrap\Alert;

$this->title = 'Grupo '.$model->name;


/* @var $this yii\web\View */
/* @var $model backend\models\Desciplina */

?>

<section>
<div class="desciplina-view">


    <?php if($model->type ==2): ?>
      <?= Html::a('<i class="fa fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
    <?php endif;?>
    <?php if($model->type ==1): ?>
      <?= Html::a('<i class="fa fa-chevron-left"></i> Voltar', ['grupo'], ['class' => 'btn btn-warning']) ?>
    <?php endif;?>
    <?= Html::a('<i class="fa fa-pencil"></i> Atualizar', ['update', 'id'=>$model->name], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('<i class="fa fa-trash"></i> Eliminar', ['update', 'id'=>$model->name], ['class' => 'btn btn-danger']) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
             'name',
           'type',
           'description',
           'rule_name',
           'data', 
           'created_at',
           'updated_at', 
        ],
    ]) ?>



    
  
    <div class="row">
        
            <?php $i =1;  foreach ($modelAuthItemChilddata as $value) : ?>  
            <div class="col-md-4">                   
                <p> <?= $i.' - '.$value['child'];?></p>
            </div>
            <?php $i++; endforeach;?>
   </div><!-- /.mail-box-messages -->




   



</div>
</section>