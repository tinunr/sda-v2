<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DesciplinaPerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Processos';

?>
<section>
<div class="desciplina-perfil-index">
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
 
  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>


  <?php  
  if(Yii::$app->user->can('dsp/processo/create')):
    echo Html::a('<i class="fas fa-plus-square"></i>', ['create'], ['class' => 'btn btn-success']); 

  endif;
  ?>
 </div></div>
 <div class="row">
   
<?php Pjax::begin(); ?>

     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'filterPosition'=>  GridView::FILTER_POS_HEADER,
        'columns' => [
           
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
        // ['class' => 'yii\grid\ActionColumn','template'=>'{view}{update}'],
          [
            // 'label'=>'Nº PROC.',
            'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return $model->numero.'/'.$model->bas_ano_id;                  
                }
          ], 
          // 'nord.id',
          [
            'label'=>'NORD',
            'attribute'=>'nord.id',
            'format' => 'raw',
            'value'=>function($model){
                return empty($model->nord->id)?'':Html::a($model->nord->id,['/dsp/nord/view','id'=>$model->nord->id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);    

            }
          ],
          [
            //'label'=>'Estado do prcesso',
            'attribute'=>'status',
            'format' => 'raw',    
            'headerOptions' => ['style'=>'width:90px'],
            'value' => function($model){
               return $model->processoStatus->descricao;
              //  Yii::$app->ImgButton->ProcessStatus($model->status).
            }
        ],
            [
              'attribute'=>'data',
            'format' => 'raw',    
            'headerOptions' => ['style'=>'width:80px'],
            'value' => function($model){
               return Yii::$app->formatter->asDate($model->data);
            }],

            [
              'attribute'=>'dsp_person_id',
            'format' => 'raw',    
            'headerOptions' => ['style'=>'width:330px'],
            'value' => function($model){
               return $model->person->nome;
            }],
          // ['attribute'=>'dsp_person_id','value'=>'person.nome'],
          'descricao',
          //'data:date',
           
          [
            'attribute'=>'user_id',
            'value'=>function($model){
              return( empty($model->user_id)?'':$model->user->name).(empty($model->dsp_setor_id)?'':' / '.$model->setor->sigla);
           },
            'label'=>'localização Actual'
          ],            

            
        ],
    ]); ?>
    <?php Pjax::end(); ?>
 </div>
 

</section>