<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Item  da Fatura Provisória';
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php echo $this->render('_search_item', ['model' => $searchModel]); ?>

        <?php echo Html::a('<i class="fa  fa-plus-square"></i> Novo', ['create-item'], ['class' => 'btn btn-success']) ?>
    </div>
    </div>
    <?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => [
      [
        'class' => 'yii\grid\ActionColumn',
        'visibleButtons' =>
        [
          'view' => Yii::$app->user->can('dsp/item/view'),
          'update' => Yii::$app->user->can('dsp/item/update'),
          'delete' => Yii::$app->user->can('dsp/item/delete')
        ]
      ],

      'id',
      'descricao',
      //'person.nome',
      #'valor',
    ],
  ]); ?>
    </div>
</section>