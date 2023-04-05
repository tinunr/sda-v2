<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Receita';
$this->params['breadcrumbs'][] = $this->title;
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_searchCreate', ['model' => $searchModel]); ?>

  </div></div> 
  <?=Html::beginForm(['/fin/recebimento-recibo/create']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\ActionColumn','template'=>'{view}'],
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => '\yii\grid\CheckboxColumn',
               'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model->status == 1) {
                        return ['value' => $key];
                    }
                    return ['style' => ['display' => 'none']]; // OR ['disabled' => true]
                },
            ],
            'valor:currency',
            'fatauraProvisoria.processo.numero',
            'fatauraProvisoria.numero',
            'person.nome',

        ],
    ]); ?>
    <?php if($dataProvider->getTotalCount()>0):?>
  <?=Html::submitButton('Confirmar Recebimento', ['class' => 'btn btn-info',]);?>
<?php endif;?>
</div>
</section>

