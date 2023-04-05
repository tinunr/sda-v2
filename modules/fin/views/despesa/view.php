<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\editable\Editable;
use app\modules\fin\models\Despesa;
use app\modules\cnt\models\Documento;
use yii\bootstrap\ButtonDropdown;
$isInCountablel = false;
$formatter = \Yii::$app->formatter;
$total = 0;
$total_iva = 0;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'despesa nº ' . $model->numero.'/'.$model->bas_ano_id;
// $this->title = 'Despesa Nº'.$model->id;
?>

<section>
<div class="row">
<div class="row button-bar">
        <div class="row pull-left">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>
        </div>
        <div class="row pull-right">
        <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update','id'=>$model->id], ['class' => 'btn btn-warning','title'=>'ATUALIZAR ']) ?>
        <?php if($model->status):?>
        



        <?php if(Yii::$app->user->can('fin/despesa/lock-and-unlock')):?>
        <?= Html::a(!$model->is_lock?'<i class="fas fa-lock"></i> Bloquear':'<i class="fas fa-lock-open"></i> Desbloquear', ['lock-and-unlock','id'=>$model->id,'value' => $model->is_lock?Despesa::IS_UNLOCK:Despesa::IS_LOCK], [
            'class' => 'btn btn-danger','title'=>!$model->is_lock?'BLOQUER':'DESBLOQUEAR',
            'data' => [
                'confirm' => 'PRETENDE REALENTE ALTERAR ESTE REGISTO?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>

        <?= Html::a('<i class="fas fa-edit"></i> Anular', ['undo', 'id' => $model->id], [
            'class' => 'btn btn-danger','title'=>'ANULAR',
            'data' => [
                'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>

        <?= ButtonDropdown::widget([
    'label' => '<i class="fas fa-plus"></i> NOVO',
    'options'=>['class'=>'dropdown-btn dropdown-new'],
    'encodeLabel' => false,
    'dropdown' => [
        'encodeLabels' => false,
        'options'=>['class'=>'dropdown-new'],
        'items' => [
            ['label' =>'<i class="fas fa-plus">Desp. Cliente</i> ', 'url' => ['create']],
            ['label' =>'<i class="fas fa-plus">Desp. Agencia</i> ', 'url' => ['create-agencia']],
        ],
    ],
]);?>

    </div>
    </div>

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<div class="row">
    <div class="col-md-6">
         <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'numero',
                'format'=>'raw',
                'value'=>function($model){
                    return Yii::$app->ImgButton->Status($model->status).' '.$model->getNumber();
                }
            ],
            
            [
                'attribute'=>'Processo',
                'format'=>'raw',
                'value'=>function($model){
                    return empty($model->processo->numero)?null:$model->processo->getNumber() ;
                }
            ],
            [
                'attribute'=>'Fatura Provisória',
                'format'=>'raw',
                'value'=>function($model){
                    return empty($model->faturaProvisoria->numero)?null:$model->faturaProvisoria->getNumber();
                }
            ],
            [
                'label'=>'Contabilidade',
                'format' => 'raw',
                'value'=>function($model){
                  $documento_id = null;
                    if($model->cnt_documento_id = Documento::DESPESA_FATURA_FORNECEDOR) {
                      $documento_id = Documento::DESPESA_FATURA_FORNECEDOR;
                    }elseif($model->cnt_documento_id = Documento::FATURA_FORNECEDOR_INVESTIMENTO){
                      $documento_id = Documento::FATURA_FORNECEDOR_INVESTIMENTO;

                    }elseif($model->cnt_documento_id = Documento::FATURA_RECIBO){
                      $documento_id = Documento::FATURA_RECIBO;   
                    }
                    if (!empty($documento_id)) {
                        $id = Yii::$app->CntQuery->inContabilidade($documento_id, $model->id);
                        if ($id) {
                            return Html::a('Contabilidade',['/cnt/razao/view','id'=>$id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);   
                            $isInCountablel = true;
                        }
                    }
                }
            ],   
            
            'descricao',
            
        ],
    ]) ?>
        
    </div>
    <div class="col-md-6">

         <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'data:date',
            'data_vencimento:date',
            ['label'=>'Fornecedor', 'attribute'=>'person.nome'],
            'valor:currency',
            'valor_pago:currency',
            'saldo:currency',
        ],
    ]) ?>
        
    </div>
</div>


    </div>

<div class="titulo-principal"> <h5 class="titulo">item da despesa</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <table class="table table-hover">
        <tr>
            <th>Cod.Item</th>
            <th>Item Descrição</th>
            <th>Valor Total</th>
            <th>IVA</th>
            <th>Terceiro</th>
        </tr>
    <?php foreach ($model->despesaItem as $key => $value):?>
        <?php 
            $total = $total+$value->valor;
            $total_iva = $total_iva+$value->valor_iva;
        ?>
        <tr>
            <td><?=str_pad($value->item_id,4,'0',STR_PAD_LEFT)?></td>
            <td>
             <?php $editable = Editable::begin([
                    'name'=>$value->id,
                    'attribute'=>'item_descricao',
                    'asPopover' => true,
                    'size'=>'md',
                    'displayValue' =>$value->item_descricao,
                    'options'=>['placeholder'=>'Enter location...']
                ]);
                $form = $editable->getForm();
                echo Html::hiddenInput('item_id', $value->id);
                Editable::end();?>
                    
                </td>
            <td><?=$formatter->asCurrency($value->valor)?></td>
            <td><?=$formatter->asCurrency($value->valor_iva)?></td>
            <td><?=!empty($value->cnt_plano_terceiro_id)?str_pad($value->cnt_plano_terceiro_id,6,'0',STR_PAD_LEFT).' - '.$value->person->nome:null?></td>
        </tr>
       

<?php endforeach;?>
        <tr>
            <th colspan="2">Total</th>
            <th><?=$formatter->asCurrency($total)?></th>
            <th><?=$formatter->asCurrency($total_iva)?></th>
            <th></th>
        </tr>
    </table>


<div class="titulo-principal"> <h5 class="titulo">Ordem de pagamento | Papamento</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
 <table class="table table-hover">
    
<?php if(!empty($model->pagamentoOrdemItem)):?>
    <tr>
        <th>Nº Ordem Pagamento</th>
        <th>Valor</th>
        <th>Data</th>
    </tr>
    <?php foreach ($model->pagamentoOrdemItem as $key => $value):?>
        <tr>
            <td><?=Yii::$app->ImgButton->Status($value->pagamentoOrdem->status).' '.$value->pagamentoOrdem->numero.'/'.$value->pagamentoOrdem->bas_ano_id?></td>
            <td><?=$formatter->asCurrency($value->valor)?></td>
            <td><?=$formatter->asDate($value->pagamentoOrdem->data)?></td>
        </tr>

<?php endforeach;?>
<?php endif;?>

<?php if(!empty($model->pagamentoItem)):?>
    <tr>
        <th>Nº Pagamento</th>
        <th>Valor</th>
        <th>Data</th>
    </tr>
    <?php foreach ($model->pagamentoItem as $key => $value):?>
        <tr>
            <td><?=Yii::$app->ImgButton->Status($value->pagamento->status).' '.$value->pagamento->getNumber()?></td>
            <td><?=$formatter->asCurrency($value->valor)?></td>
            <td><?=$formatter->asDate($value->pagamento->data)?></td>
        </tr>

<?php endforeach;?>
<?php endif;?>

    </table>


<?php if(!empty($model->ofAccounts) || !empty($model->ofAccountsItem)):?>
<div class="titulo-principal"> <h5 class="titulo">encontro de conta</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
 <table class="table table-hover">
    <tr>
        <th>Numero</th>
        <th>Origem </th>
        <th>Valor</th>
        <th>Data</th>
    </tr>
<?php if(!empty($model->ofAccounts) ):?>
    <?php foreach ($model->ofAccounts as $key => $value):?>
        <tr>
            <td><?=Yii::$app->ImgButton->Status($value->status).' '.$value->getNumber() ?></td>
            <td><?=empty($value->fin_receita_id)?'Despesa nº '.$value->despesa->getNumber():'fatura Provisória nº'.$value->receita->faturaProvisoria->getNumber();?></td>
            <td><?=$formatter->asCurrency($value->valor)?></td>
            <td><?=$formatter->asDate($value->data)?></td>
        </tr>

<?php endforeach;?>
<?php endif;?>
<?php if(!empty($model->ofAccountsItem)):?>
<?php foreach ($model->ofAccountsItem as $key => $value):?>
         <tr>
            <td><?=Yii::$app->ImgButton->Status($value->ofAccounts->status).' '.$value->ofAccounts->getNumber() ?></td>
            <td><?=empty($value->ofAccounts->fin_receita_id)?'Despesa nº '.$value->ofAccounts->despesa->getNumber() :'fatura Provisória nº'.$value->ofAccounts->receita->faturaProvisoria->getNumber();?></td>
            <td><?=$formatter->asCurrency($value->valor)?></td>
            <td><?=$formatter->asDate($value->ofAccounts->data)?></td>
        </tr>

<?php endforeach;?>
<?php endif;?>
    </table>
<?php endif;?>





</section>



<section>
    <?php $ano = \app\models\Ano::findOne($model->bas_ano_id)->ano;
    echo \app\components\widgets\FileBrowserView::widget([
        'item_id' => $model->id,
        'action_id' => 'fin_despesa',
        'dir' => 'data/despesas/' . $ano . '/' . $model->id,
        'can_edit'=>!$isInCountablel,
    ]); ?>
</section>