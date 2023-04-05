<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Documento de despachos';
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?=Html::encode($this->title)?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

        <?php  echo Html::a('<i class="fa  fa-plus-square"></i> Novo', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\ActionColumn','template'=>'{view}{update}'], 
            ['class' => 'yii\grid\SerialColumn'],
            'sigla',
            'descricao',           

        ],
    ]); ?>
    </div>
</section>