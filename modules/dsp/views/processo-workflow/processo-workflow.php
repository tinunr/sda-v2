<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\dsp\services\ProcessoWorkflowService;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$id = 0;
$this->title = 'Workflow';
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?=Html::encode($this->title)?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="action-column">Ações</th>
                    <th>Processo</th>
                    <th>Setor</th>
                    <th>Cliente</th>
                    <th>Mercadoria</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dataProvider->getModels() as $key => $model):?>
                <tr>
                    <td><?php if(($key==0 && !ProcessoWorkflowService::temProcessoExecucao(Yii::$app->user->identity->id)) || $model->prioridade == 1):?>
                        <?=Html::a('<i class="fa fa-check"></i>', Url::to(['/dsp/processo-workflow/get-setor','id'=>$model->id]), [
                        'title' => 'Atualizar',
                        'class'=>'btn  btn-xs',
                        'data' => [
                            'confirm' => 'PRETENDE REALENTE ACEITAR ESTE PROCESSO?',
                            'method' => 'post',
                            ]
                    ])?>
                    <?php endif;?>
                    <?php if($key>0 && $model->prioridade == 0 && Yii::$app->user->can('dsp/processo-workflow/set-prioridade')):?>
                    <?=Html::a('<i class="fa fa-sort-amount-up"></i>', Url::to(['/dsp/processo-workflow/set-prioridade','id'=>$model->id]), [
                        'title' => 'Atualizar',
                        'class'=>'btn  btn-xs',
                        'data' => [
                            'confirm' => 'PRETENDE DAR PRIORIDADE E ESTE PROCESSO?',
                            'method' => 'post',
                            ]
                    ]);?>
                    <?php endif;?>
                    </td>
                    <td>
                        <?=Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id.' | '.$model->processo->processoStatus->descricao,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);?>
                    </td>
                    <td><?=!empty($model->setor->descricao)?$model->setor->descricao:''?></td>
                    <td><?=$model->processo->person->nome?></td>
                    <td><?=!empty($model->processo->descricao)?$model->processo->descricao:''?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    </div>
</section>