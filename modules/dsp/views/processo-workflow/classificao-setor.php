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
            <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php echo $this->render('_searchClassificacaoSetor', ['model' => $searchModel, 'dspSetor' => $dspSetor]); ?>

    </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:10px;'],
                'header' => 'Ações',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i></span>', ['/dsp/processo-workflow/classificacao-user', 'user_id' => $model['user_id']], [
                            'title' =>  'View',
                            'class' => 'btn btn-xs',
                        ]);
                    },
                ],

            ],
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'label' => 'Nº Artigo',
                'attribute' => 'id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function ($model) use ($searchModel) {
                    return \app\modules\dsp\services\ProcessoWorkflowService::totalClassificacao($model['user_id'], $searchModel->dataInicio, $searchModel->dataFim);
                }
            ],

        ],
    ]); ?>
    </div>
</section>
