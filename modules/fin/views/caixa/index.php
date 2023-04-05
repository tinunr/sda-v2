<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\fin\models\Caixa;
use app\modules\fin\services\CaixaService;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Diário de Tesouraria';
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
            ['class' => 'app\components\widgets\CustomActionColumn', 'template' => '{view}'],
            ['class' => 'yii\grid\SerialColumn'],
            //'id',

            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return CaixaService::ImgCaixaStaus($model) . ' ' . Caixa::STATUS_TEXTO[$model->status];;
                }
            ],
            'bancoConta.banco.descricao',
            'bancoConta.numero',
            // 'descricao',
            // 'saldo_inicial:currency',
            // 'saldo_fecho:currency',
            [
                'attribute' => 'saldo_inicial',
                'value' => function ($model) {
                    return Yii::$app->formatter->asCurrency($model->saldo_inicial);
                },
                'encodeLabel' => false,
                'contentOptions' => function ($model, $key, $index, $column) {
                    return ['style' => $model->status == 1 ? 'background-color:' . $model->bancoConta->color . '; color: #FFF ' : '#FFF'];
                },
            ],
            'data_abertura',

            [
                'attribute' => 'saldo_fecho',
                'value' => function ($model) {
                    return Yii::$app->formatter->asCurrency($model->saldo_fecho);
                },
                'encodeLabel' => false,
                'contentOptions' => function ($model, $key, $index, $column) {
                    return ['style' => $model->status == 2 ? 'background-color:' . $model->bancoConta->color . '; color: #FFF ' : '#FFF'];
                },
            ],
            'data_fecho',



        ],
    ]); ?>
    </div>
</section>
