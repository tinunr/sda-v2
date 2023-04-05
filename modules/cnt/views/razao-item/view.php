<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = ' Razão nº '.$model->numero.'/'.$model->bas_ano_id;
?>

<section>
<div class="Curso-index">


    <p>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Yii::$app->ImgButton->Img('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
    </p>
    
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>


  <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'numero',
                'format' => 'raw',
                'value'=>function($model){
                    return $model->numero.'/'.$model->cnt_diario_id.'/'.$model->bas_mes_id.'/'.$model->bas_ano_id;    

                }
              ],
            [
                'label'=>'Origem',
                'attribute'=>'documento_origem_id',
                'format' => 'raw',
                'value'=>function($model){
                    return Html::a($model->documento->descricao,Yii::$app->CntQuery->origem($model->id),['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);    

                }
              ],
            'diario.descricao',
            'data:date',
            'valor:currency',
            'descricao',
             
        ],
    ]) 

    

    ?>
<div class="titulo-principal"> <h5 class="titulo">RAZÃO ITEM</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

      <?= GridView::widget([
        'dataProvider' => $providerRazaoItem,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'planoConta.descricao',
            'natureza.descricao',
            'valor:currency',
            'cnt_plano_terceiro_id',
            'planoTerceiro.nome',
            'planoFluxoCaixa.descricao',
            'planoIva.descricao',
            
        ],
    ]); ?>
</div>
</section>




  


