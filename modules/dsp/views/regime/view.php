<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use  yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Regime '.$model->id;
?>

<section>
<div class="row">


    <p>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Novo Item', ['create-regime-item', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
    </p>
    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descricao',
        ],
    ]) ?>

    </div>


    </section>




    <section>

<div class="row">
<div class="titulo-principal"> <h5 class="titulo">REGIME ITEM</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
  

  <table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Item</th>
      <th scope="col">Valor</th>
      <th scope="col">Unidade</th>
      <th scope="col">Forma</th>
    </tr>
  </thead>
  <tbody>
  <?php $i=1; foreach ($regimeData as $key => $value):?>
    <tr>
      <th scope="row">
         <?= Html::a('<i class="fa fa-eye"></i>','#', ['class'=>' btn btn-xs','data-toggle'=>"modal",'data-target'=>'#'.$value->id])?>
         <?php 
 Modal::begin(['id'=>$value->id,
                'header' => 'DETALHES',
                'toggleButton' => false,
                'size'=>Modal::SIZE_LARGE
            ]);?>
            <?php 
            $model = \app\modules\dsp\models\RegimeItem::findOne(['id'=>$value->id]);?>

          <?= DetailView::widget([
              'model' => $model,
              'attributes' => [
                  'descricao',
                  //['label'=>'Tabela Anexa','value'=>'regimeItem.descricao'],
                  'valor',
                  'regimeUnidade.descricao',
                  'forma',
              ],
          ]) ?>  
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Valor</th>
      <th scope="col">Unidade</th>
      <th scope="col">Formula</th>
      <th scope="col">Condição</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($model->regimeItemItem as $key => $regimeItemItem):?>
    <tr>
      <td><?=$regimeItemItem->valor?></td>
      <td><?=$regimeItemItem->regimeUnidade->descricao?></td>
      <td><?=$regimeItemItem->forma?></td>
      <td><?=$regimeItemItem->condicao. ' '.$regimeItemItem->valor_produto?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
</table>



<?php Modal::end();?>

         <?= Html::a('<i class="fa fa-pencil-alt"></i>', ['update-regime-item', 'id' => $value->id], ['class' => 'btn btn-xs btn-primary']) ?>
      </th>
      <td><?=$value->descricao?></td>
      <td><?=$value->valor?></td>
      <td><?=$value->regimeUnidade->descricao?></td>
      <td><?=$value->forma?></td>
    </tr>
    <?php $i++; endforeach;?>
 
  </tbody>
</table>
  

</div>
</section>






