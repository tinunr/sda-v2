<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DesciplinaPerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Relatorio Fatura Provisórias';
?>
<section>
<div class="desciplina-perfil-index">
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

 
  <?php  echo $this->render('_searchRelatorio', ['model' => $searchModel]); ?>


 
   </div></div>
 <div class="row">
   



<?php yii\widgets\Pjax::begin() ?>
     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
         'columns' => [           
          [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
          ], 
          [
            'label'=>'Nº Processo',
            'attribute'=>'processo',
            'format' => 'raw',
            'value'=>function($model){
                return Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);    

            }
          ],
          [
            'attribute'=>'nord',
            'format' => 'raw',
            'value'=>function($model){
                return empty($model->nord)?'':Html::a($model->nord,['/dsp/nord/view','id'=>$model->nord],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);    

            }
          ],
          [
                'encodeLabel' => false,
                 'format' => 'html',
                 'value' => function($model){
                  if (!empty($model->receita->id ) ){                    
                   if ($model->receita->valor == $model->receita->valor_recebido) {
                    return Html::img(Url::to('@web/img/greenBall.png'));                      # code...
                   }elseif(($model->receita->valor > $model->receita->valor_recebido)&&($model->receita->valor_recebido >0)){
                    return Html::img(Url::to('@web/img/gray-greenBall.png'));  
                   }else{
                    return Html::img(Url::to('@web/img/grayBal.png'));  

                   }
                }
              }
            ],
          'valor:currency',
          'receita.valor_recebido:currency',
          'receita.saldo:currency',
          'data:date', 
           'person.nome',
          // 'mercadoria',             
        ],
    ]); ?>
<?php yii\widgets\Pjax::end() ?>



 </div>
 

</div>
</section>