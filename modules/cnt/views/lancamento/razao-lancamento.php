<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\cnt\models\Razao;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IlhasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lançamento Fecho & Abertura';

?>


<section>
    <div class="titulo-principal">
        <h5 class="titulo"><?=Html::encode($this->title)?></h5>
        <div id="linha-longa">
            <div id="linha-curta"></div>
        </div>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Ano</th>
                <th>Lancamento</th>
                <th>Data Execução</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($razaLancamento as $key => $value):?>
            <tr>
                <td>
                    <?php   Modal::begin(['id'=>$value->id,
                        'header' => 'DETALHES DO LANÇAMENTO',
                        'toggleButton' => ['label' => '<i class="fa fa-eye"></i>','class' => 'btn btn-xs btn-eye'],
                            'size'=>Modal::SIZE_LARGE,
                    ]);
            $lancamento = Razao::find()->where(['operacao_fecho'=>$value->id])->all();
            ?>

                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Numero</th>
                                <th>Diario</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lancamento as $key => $data):?>
                            <tr>
                                <td><?= Html::a('<i class="fa fa-eye"></i>', ['/cnt/razao/view','id'=>$data->id], ['class' => 'btn btn-xs']) ?>
                                </td>
                                <td><?=$data->numero?></td>
                                <td><?=$data->cnt_diario_id.' - '.$data->diario->descricao?></td>
                                <td><?=$data->descricao?></td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>

                    <?php Modal::end();?>

                    <?= Html::a('<i class="fa fa-trash-alt"></i>', ['delete-lancamento', 'id' => $value->id], [
                        'class' => 'btn btn-xs','title'=>'ANULAR',
                        'data' => [
                            'confirm' => 'PRETENDE REALMENTE ELIMINAR ESTE LANÇAMENTO?',
                            'method' => 'post',
                        ],
                    ]) ?>

                </td>
                <td><?=$value->ano?></td>
                <td><?=$value->descricao?></td>
                <td><?=$value->created_at?></td>
            </tr>


            <?php endforeach;?>
        </tbody>
    </table>
</section>