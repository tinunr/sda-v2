<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\dsp\models\SetorUser;
use app\modules\dsp\models\Setor;
use app\modules\dsp\services\NordService;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
// print_r($dataProvider);die();
$this->title = 'Classificação';
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?=Html::encode($this->title)?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php  echo $this->render('_searchClassificacaoSetor', ['model' => $searchModel,'dspSetor'=>$dspSetor]); ?>

    </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showFooter' => true,
        'columns' => [
           
            ['class' => 'yii\grid\SerialColumn'],
            
            'name',
            [
                'label'=>'Nº Artigo',
                'attribute'=>'id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model , $key, $index, $obj) use ($searchModel){
                    $valor = \app\modules\dsp\services\ProcessoWorkflowService::totalClassificacaoSetor($searchModel->dsp_setor_id,$model['user_id'],$searchModel->dataInicio, $searchModel->dataFim);
                    $obj->footer += $valor;
                    return $valor;                  
                }
            ], 
            [
                'label'=>'Valor %',
                'attribute'=>'id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model, $key, $index, $obj) use ($searchModel ,$tottalClassificacao){
                    $valor = \app\modules\dsp\services\ProcessoWorkflowService::totalClassificacaoSetor($searchModel->dsp_setor_id,$model['user_id'],$searchModel->dataInicio, $searchModel->dataFim); 
                    $return  =  ($valor/$tottalClassificacao )*100 ; 
                    $obj->footer += $return; 
                    return $return;      
                }
            ], 
            [
                'label'=>'Valor Subsidio',
                'attribute'=>'id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model, $key, $index, $obj) use ($searchModel ,$tottalClassificacao,$dspSetor){
                    $valor = \app\modules\dsp\services\ProcessoWorkflowService::totalClassificacaoSetor($searchModel->dsp_setor_id,$model['user_id'],$searchModel->dataInicio, $searchModel->dataFim); 
                    $percentagem =   ($valor/$tottalClassificacao )*100 ;   
                    $return    = round($percentagem*$dspSetor->valor_subcidio,2) ;
                    $obj->footer += $return; 
                    return $return;      

                }
            ], 

        ],
    ]); ?>
    </div>
</section>
