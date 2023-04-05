<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\dsp\models\SetorUser;
use app\modules\dsp\models\Setor;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Workflow';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
   <?php if(Yii::$app->user->can('dsp/processo-workflow/realocar-box')):?>
        <?= Html::a('<i class="fas fa-user-edit"></i> Recolocação',['realocar-box'], ['class'=>'btn btn-warning']) ?>
    <?php endif;?>
  <?= Html::a('<i class="fas fa-inbox"></i> Receber',['receber-box'], ['class'=>'btn btn-warning']) ?>
  <?= Html::a('<i class="fas fa-share"></i> Enviar',['enviar-box'], ['class'=>'btn btn-warning']) ?>

  </div></div> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\ActionColumn','template'=>'{view}'], 
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'attribute'=>'dsp_processo_id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id.' | '.$model->processo->processoStatus->descricao,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);                  
                }
          ],  
          'processo.person.nome',
          'processo.descricao',
            // 'user.name',
            'setor.descricao',
            'data_inicio:date',
            // 'data_fim:dateTime',
            [
                
                'label'=>'Estado',
                'attribute'=>'status',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model){
                    return $model->workflowStatus->descricao;                  
                }
            ],
            // [
            //     'label'=>'Agua. Recebimento',
            //     'attribute'=>'status',
            //     'encodeLabel' => false,
            //     'format' => 'raw',
            //     'value' => function($model){
            //         return $model->status==4?((!empty($model->outWorkflow->user_id)?$model->outWorkflow->user->name:(empty($model->outWorkflow->dsp_setor_id)?'':$model->outWorkflow->setor->descricao))):'';                  
            //     }
            // ]

        ],
    ]); ?>
</div>
</section>