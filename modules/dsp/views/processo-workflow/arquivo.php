<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\dsp\models\SetorUser;
use app\modules\dsp\models\Setor;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Arquivo';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_searchArchive', ['model' => $searchModel]); ?>
  <?php if(!empty(SetorUser::findOne(['user_id'=>Yii::$app->user->identity->id,'dsp_setor_id'=>[Setor::ARQUIVO_ID]])->user_id)):?>
  <?= Html::a('<i class="fas fa-archive"></i> Arquivar',['archive-box'], ['class'=>'btn btn-warning']) ?>
<?php endif;?>
<?php if(!empty(SetorUser::findOne(['user_id'=>Yii::$app->user->identity->id,'dsp_setor_id'=>[Setor::ARQUIVO_ID]])->user_id)):?>
  <?= Html::a('<i class="fas fa-share"></i> Desarquivar',['undo-archive-box'], ['class'=>'btn btn-warning']) ?>
<?php endif;?>
  </div></div> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\ActionColumn','template'=>'{view}'], 
            // ['class' => 'yii\grid\SerialColumn'],
            
            [
                'attribute'=>'dsp_processo_id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id.' | '.$model->processo->processoStatus->descricao,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);                  
                }
          ], 
          'data_inicio:date',

          'processo.person.nome',
          'processo.descricao',
            // 'user.name',
            'setor.descricao',
            // 'data_fim:dateTime',
            

        ],
    ]); ?>
</div>
</section>