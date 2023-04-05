<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Receita ';
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    </div>
    </div>

    <?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      //'faturaProvisoria.processo.numero',
      #'faturaProvisoria.numero',
      [
        'label' => '',
        'attribute' => 'id',
        'encodeLabel' => false,
        'format' => 'html',
        'value' => function ($model) {
          return yii::$app->ImgButton->Status($model->status);
        }
      ],

      [
        'label' => 'Nº FP',
        'format' => 'raw',
        'value' => function ($model) {
          return empty($model->dsp_fataura_provisoria_id) ? '' : Html::a($model->faturaProvisoria->numero . '/' . $model->faturaProvisoria->bas_ano_id, ['/fin/fatura-provisoria/view', 'id' => $model->dsp_fataura_provisoria_id], ['class' => 'btn-link', 'target' => '_blanck']);
        }
      ],
      [
        'label' => 'Nº processo',
        'format' => 'raw',
        'value' => function ($model) {
          return empty($model->dsp_fataura_provisoria_id) ? '' : Html::a($model->faturaProvisoria->processo->numero . '/' . $model->faturaProvisoria->processo->bas_ano_id, ['/dsp/processo/view', 'id' => $model->faturaProvisoria->processo->id], ['class' => 'btn-link', 'target' => '_blanck']);
        }
      ],
      [
        //'label'=>'Image',
        //'format'=>'raw',
        'encodeLabel' => false,
        'format' => 'html',


        'headerOptions' => ['style' => 'text-align:center'], 'value' => function ($model) {
          if ($model->valor == $model->valor_recebido) {
            return Html::img(Url::to('@web/img/greenBall.png'));                      # code...
          } elseif (($model->valor > $model->valor_recebido) && ($model->valor_recebido > 0)) {
            return Html::img(Url::to('@web/img/gray-greenBall.png'));
          } else {
            return Html::img(Url::to('@web/img/grayBal.png'));
          }
        }
      ],
      'valor:currency',
      'valor_recebido:currency',
      'saldo:currency',

      'person.nome',

    ],
  ]); ?>
    </div>
</section>