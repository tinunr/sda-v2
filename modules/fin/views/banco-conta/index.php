<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contas Bancárias';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

  <?php  echo Html::a(Yii::$app->ImgButton->img('plus'), ['create'], ['class' => 'btn btn-success','title'=>'NOVO']) ?>
  </div></div> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}{update}'],
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            // 'banco.descricao',
            [
                'attribute'=>'numero',
                'format'=>'raw',
                'value'=>function($model){
                    return Yii::$app->ImgButton->Status($model->status).$model->banco->sigla.' - '.$model->numero;
                }
            ],
            // 'banco.sigla',
            // 'numero',
            // 'descoberta:currency',
            // 'saldo:currency',
            'diario.descricao',
            'planoConta.descricao',
            'planoFluxoCaixa.descricao',

            

        ],
    ]); ?>
</div>
</section>