<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Aviso de credito Nº'.$model->id;
?>

<section>
<div class="row">

<div class="row button-bar">
<div class="row pull-left">
<?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>
    </div>
    <div class="row pull-right">
        <?php if($model->status):?>
        <?= Html::a('<i class="fas fa-print"></i> Documento', ['view-pdf', 'id'=>$model->id], ['class'=>'btn btn-warning','target'=>'_blanck','title'=>'IMPRIMIR']) ?>         
        <?= Html::a('<i class="fas fa-print"></i> Histórico', ['historico-pdf', 'id'=>$model->id], ['class'=>'btn btn-warning','target'=>'_blanck','title'=>'IMPRIMIR']) ?>         

        <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
            'class' => 'btn btn-danger','title'=>'ANULAR',
            'data' => [
                'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>

</div>
</div>
<div class="titulo-principal"> <h5 class="titulo">NOTA DE CREDITO</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
           [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            [
                'label'=>'Processo',
                'format' => 'raw',
                'value'=>function($model){
                    return empty($model->dsp_processo_id)?'':Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','target'=>'_blanck']);
                }
            ],
            [
                'label'=>'Fatura Definitiva',
                'format' => 'raw',
                'value'=>function($model){
                    return empty($model->fin_fatura_defenitiva_id)?'':Html::a($model->faturaDefinitiva->numero.'/'.$model->faturaDefinitiva->bas_ano_id,['/fin/fatura-definitiva/view','id'=>$model->fin_fatura_defenitiva_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);   

                }
            ],
            'valor:currency',
            'person.nome',
            'descricao',
        ],
    ]) ?>

    </div>






    <div class="titulo-principal"> <h5 class="titulo">Despesa</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>


<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        // '',
        [
            'attribute'=>'despesa.numero',
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function($model){
                return Html::a(yii::$app->ImgButton->Status($model->despesa->status).$model->despesa->numero.'/'.$model->despesa->bas_ano_id,['/fin/despesa/view','id'=>$model->despesa->id],['class'=>'btn-link','target'=>'_blanck']);
                ;                  
            }
        ],
        'despesa.valor:currency',
        'despesa.saldo:currency',
    ],
]) ?>


   
<div class="titulo-principal"> <h5 class="titulo">encontro de contas / Pagamentos</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<table class="table table-hover">
    <tbody>
        <tr>
            <th>Documento</th>
            <th>Nº</th>
            <th>Valor</th>
            <th>descricao</th>
        </tr>
        <?php foreach ($model->despesa->ofAccounts as $key => $value):?>
        <tr>
            <td>Encontro de Conta</td>
            <td><?=$value->numero.'/'.$value->bas_ano_id ?></td>
            <td><?=$value->valor ?></td>
            <td><?=$value->descricao ?></td>
        </tr>
        <?php endforeach ;?>
        <?php foreach ($model->despesa->ofAccountsItem as $key => $value_b):?>
        <tr>
            <td>Encontro de Conta</td>
            <td><?=$value_b->ofAccounts->numero.'/'.$value_b->ofAccounts->bas_ano_id ?></td>
            <td><?=$value_b->valor ?></td>
            <td><?=$value_b->ofAccounts->descricao ?></td>
        </tr>
        <?php endforeach ;?>
        <?php foreach ($model->despesa->pagamentoItem as $key => $value_c):?>
        <tr>
            <td>Pagamento</td>
            <td><?=$value_c->pagamento->numero.'/'.$value_c->pagamento->bas_ano_id ?></td>
            <td><?=$value_c->valor ?></td>
            <td><?=$value_c->pagamento->descricao ?></td>
        </tr>
        <?php endforeach ;?>
    </tbody>
    
</table>

    </section>

