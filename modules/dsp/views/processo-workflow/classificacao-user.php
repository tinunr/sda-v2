<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\dsp\models\SetorUser;
use app\modules\dsp\models\Setor;
use app\modules\dsp\services\NordService;
$formatter = Yii::$app->formatter;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
// print_r($dataProvider);die();
$this->title = 'Classificação | '.$user->name;
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?=Html::encode($this->title)?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>
        <?php  echo $this->render('_searchClassificacaoUser', ['model' => $searchModel]); ?>

    </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter'=>TRUE,
        'columns' => [
            // ['class' => 'yii\grid\ActionColumn','template'=>'{view}'], 
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>'Processo',
                'attribute'=>'id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model['numero'].'/'.$model['bas_ano_id'],['/dsp/processo/view','id'=>$model['id']],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);                  
                },
            ],  
           [
                'headerOptions' => ['style'=>'text-align:center; width:60%;'],
                'attribute'=> 'mercadoria',
                'footer' =>'TOTAL'
            ],
            [
                'label'=>'Funcionário',
                'attribute'=>'name',
                'footer'=>'Total',
            ],
            [   
                'attribute'=>'data_faturacao',
                'value' =>function($model) use ($formatter){
                    return $formatter->asDate($model['data_faturacao']);                  
                },   
                'footer'=> $formatter->asDate($searchModel->dataInicio).' / '.$formatter->asDate($searchModel->dataFim),
            ],
            [
                'label'=>'Nº Artigo',
                'attribute'=>'id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model){
                    return NordService::totalNumberOfItems($model['nord']);                  
                },
                'footer' => \app\modules\dsp\services\ProcessoWorkflowService::totalClassificacao($searchModel->user_id,$searchModel->dataInicio, $searchModel->dataFim),
            ], 

        ],
    ]); ?>
    </div>
</section>