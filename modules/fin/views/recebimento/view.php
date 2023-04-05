<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\fin\models\RecebimentoTipo;
$formatter = Yii::$app->formatter;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
$can_edit = true;
$this->title = 'Recebimento nº '.$model->numero;
?>

<section>
<div class="row">


    <p>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'Voltar']) ?>
        <?= Html::a('<i class="fas fa-print"></i> Imprimir', ['view-pdf', 'id'=>$model->id], ['class'=>'btn btn-warning','target'=>'_blanck','title'=>'Imprimir']) ?>
        <?php if($model->fin_recebimento_tipo_id == RecebimentoTipo::ADIANTAMENTO):?>
        <?= Html::a('<i class="fas fa-print"></i> Imprimir'.' Histórico', ['view-historico-pdf', 'id'=>$model->id], ['class'=>'btn btn-warning','target'=>'_blanck','title'=>'Imprimir']) ?>
        <?php endif;?>
        <?php if($model->status&&Yii::$app->user->can('fin/recebimento/undo')):?>
        <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
            'class' => 'btn btn-danger','title'=>'ANULAR',
            'data' => [
                'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>

    </p>
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
    
<div class="row">
  <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [            
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            'recebimentoTipo.descricao',
            'data:date',
            'person.nome',
            'descricao', 
            [
                'label'=>'Contabilidade',
                'format' => 'raw',
                'value'=>function($model){
                  // $documento_id = 0;
                  if($model->fin_recebimento_tipo_id === RecebimentoTipo::CONTA_CORENTE) {
                    $documento_id = \app\modules\cnt\models\Documento::RECEBIMENTO_FATURA_PROVISORIA;
                  }elseif($model->fin_recebimento_tipo_id === RecebimentoTipo::ADIANTAMENTO){
                    $documento_id = \app\modules\cnt\models\Documento::RECEBIMENTO_ADIANTAMENTO;

                  }elseif($model->fin_recebimento_tipo_id === RecebimentoTipo::TESOURARIA){
                    $documento_id = \app\modules\cnt\models\Documento::RECEBIMENTO_TESOURARIO;
                    
                  }elseif($model->fin_recebimento_tipo_id === RecebimentoTipo::REEMBOLSO){
                    $documento_id = \app\modules\cnt\models\Documento::RECEBIMENTO_REEMBOLSO; 
                  }
                    $id = Yii::$app->CntQuery->inContabilidade($documento_id, $model->id);
                    if ($id) {
                        return Html::a('Contabilidade',['/cnt/razao/view','id'=>$id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);   
                        $can_edit = false;
                    }
                }
            ],   
            
          ],

    ]) ?>
  </div>
  <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [            
            'bancos.descricao',
            'bancoConta.numero',
            'valor:currency',
            'documentoPagamento.descricao',
            'numero_documento',
            'data_documento',
          ],
    ]) ?>
  </div>
</div>
 

    </div>








<?php if (!empty($model->despesa)):?>
    <div class="titulo-principal"> <h5 class="titulo">despesas e pagamento</h5><div id="linha-loga"><div id="linha-curta"></div></div> </div> 

  <table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Tipo </th>
      <th scope="col">Numero</th>
      <th scope="col">Valor</th>
      <th scope="col">Valor Pago</th>
      <th scope="col">Saldo</th>
      <th scope="col">Data</th>
      <th scope="col">Fornecedor</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($model->despesa as $key => $despesa):  ?>
    <tr>
      <td>Despesa</td>
      <td><?=Html::a(Yii::$app->ImgButton->Status($despesa->status).$despesa->numero.'/'.$despesa->bas_ano_id,['/fin/despesa/view','id'=>$despesa->id],['class'=>'btn-link','target'=>'_balnk'])?></td>
      <td><?=$formatter->asCurrency($despesa->valor)?></td>
      <td><?=$formatter->asCurrency($despesa->valor_pago)?></td>
      <td><?=$formatter->asCurrency($despesa->saldo)?></td>
      <td><?=$formatter->asDate($despesa->data)?></td>
      <td><?=$despesa->person->nome?></td>
    </tr>  
    <?php if(!empty($despesa->pagamentoItem)):?>
    <?php foreach ($despesa->pagamentoItem as $key => $value):?>
        <tr>
            <td>Pagamento</td>
            <td><?=Yii::$app->ImgButton->Status($value->pagamento->status).' '.$value->pagamento->numero.'/'.$value->pagamento->bas_ano_id?></td>
            <td></td>
            <td><?=$formatter->asCurrency($value->valor)?></td>
            <td></td>
            <td><?=$formatter->asDate($value->pagamento->data)?></td>
            <td></td>
        </tr>

<?php endforeach;?>
<?php endif;?>    

   
    <?php  endforeach;?>
  </tbody>
</table> 

<?php endif;?>    







<?php if ($model->fin_recebimento_tipo_id != RecebimentoTipo::ADIANTAMENTO) :?>
    <div class="titulo-principal"> <h5 class="titulo">item recebidos </h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>


    <div class="row">
     <table class="table table-hover" >
   <thead >

    <tr >
      <th scope="col">Receita</th>
      <th scope="col">Valor</th>
      <th scope="col">Valor Recebido</th>
      <th scope="col">Saldo</th>
      <th scope="col">Terceiro</th>
    </tr>
  </thead>
  <tbody>
    
 <?php $i=1; foreach ($model->recebimentoItem as $key => $item):?>

    <tr>      
      <td > <?php if(!empty($item->receita->faturaProvisoria->id)):?>
    <?=Html::a($item->descricao_item,['/fin/fatura-provisoria/view','id'=>$item->receita->faturaProvisoria->id],['class'=>'btn-link','target'=>'_blanck'])?>
      <?php else:?>
      <?=$item->descricao_item?>
      <?php endif;?>
    </td>
      <td > <?= $formatter->asCurrency($item->valor)?> </td>
      <td > <?= $formatter->asCurrency($item->valor_recebido)?> </td>
      <td > <?=  $formatter->asCurrency($item->saldo)?> </td>
      <td > <?=empty($item->dsp_person_id)?'':$item->person->nome?> </td>
    </tr>
   
   

    <?php $i++; endforeach;?>

  </tbody>
</table>
</div>
<?php else:?>


   <div class="titulo-principal"> <h5 class="titulo">despesas / encontro de contas</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div> 

  <table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Tipo</th>
      <th scope="col">Numero </th>
      <th scope="col">Valor</th>
      <th scope="col">Valor Pago</th>
      <th scope="col">Saldo</th>
      <th scope="col">Data</th>
      <th scope="col">Fornecedor</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($model->despesa as $key => $despesa):  ?>
    <tr>
      <td>Despesa</td>
      <td><?=yii::$app->ImgButton->Status($model->status).$despesa->numero.'/'.$despesa->bas_ano_id?></td>
      <td><?=$formatter->asCurrency($despesa->valor)?></td>
      <td><?=$formatter->asCurrency($despesa->valor_pago)?></td>
      <td><?=$formatter->asCurrency($despesa->saldo)?></td>
      <td><?=$formatter->asDate($despesa->data)?></td>
      <td><?=$despesa->person->nome?></td>
    </tr>  
    
<?php if(!empty($despesa->ofAccounts) ):?>
    <?php foreach ($despesa->ofAccounts as $key => $value):?>
        <tr>
          <td>Encontro de conta</td>
            <td><?=Yii::$app->ImgButton->Status($value->status).' '.$value->numero.'/'.$value->bas_ano_id?></td>
            <td></td>
            <td><?=$formatter->asCurrency($value->valor)?></td>
            <td></td>
            <td><?=$formatter->asDate($value->data)?></td>
            <td></td>
        </tr>

<?php endforeach;?>
<?php endif;?>
<?php if(!empty($despesa->ofAccountsItem)):?>
<?php foreach ($despesa->ofAccountsItem as $key => $value):?>
        <tr>
          <td>Encontro de conta</td>
            <td><?=Yii::$app->ImgButton->Status($value->ofAccounts->status).' '.$value->ofAccounts->numero.'/'.$value->ofAccounts->bas_ano_id?></td>
            <td></td>
            <td><?=$formatter->asCurrency($value->valor)?></td>
            <td></td>
            <td><?=$formatter->asDate($value->ofAccounts->data)?></td>
            <td></td>
        </tr>
<?php endforeach;?>
<?php endif;?>   

   
    <?php  endforeach;?>
  </tbody>
</table> 
<?php endif;?>   

</section>
<section>
    <?php $ano = \app\models\Ano::findOne($model->bas_ano_id)->ano;
    echo \app\components\widgets\FileBrowserView::widget([
        'item_id' => $model->id,
        'action_id' => 'fin_recebimento',
        'dir' => 'data/recebimentos/' . $ano . '/' . $model->numero,
        'can_edit'=>$can_edit,
    ]); ?>
</section>