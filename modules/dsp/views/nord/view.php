<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use  yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */

$this->title = 'N.O.R.D  '.$model->id;


?>
<section>
<div class="Curso-index">
<div class="row button-bar">
<div class="row pull-left">
    <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class'=>'btn btn-warning']) ?>
    </div>
    <div class="row pull-right">

    <?= Html::a('<i class="fas fa-file-code"></i>  Upload xml','#', ['class'=>'btn','data-toggle'=>"modal",'data-target'=>'#modal'])?>

    <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id'=>$model->id], ['class' => 'btn btn-primary']) ?>



<?php if($model->status):?>
        <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
            'class' => 'btn btn-danger','title'=>'ANULAR',
            'data' => [
                'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                'method' => 'post',
            ],
        ]) ?>
    <?php else:?>
        <?= Html::a('<i class="fas fa-undo"></i> Desanular', ['ativar', 'id' => $model->id], [
            'class' => 'btn btn-danger','title'=>'ATIVAR NORDE',
            'data' => [
                'confirm' => 'PRETENDE REALENTE CONTINUAR?',
                'method' => 'post',
            ],
        ]) ?>
    <?php endif;?>


  </div>  
  </div>

 
    
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->desembaraco->code.'/'.$model->bas_ano_id;                  
                }
          ], 
          [
            'label'=>'Nº Processo',
            'format' => 'raw',
            'value'=>function($model){
                return Html::a(empty($model->processo->numero)?'':$model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);    

            }
          ],
            'desembaraco.code',
            'desembaraco.descricao',
             [
                'label'=>'XML',
                //'format'=>'raw',
                'encodeLabel' => false,
                'format' => 'raw',
                'headerOptions' => ['style'=>'text-align:center'],'value' => function($model){
                   if (file_exists(Yii::getAlias('@nords/').$model->id.'.xml')) {
                      return Html::img(Url::to('@web/img/greenBall.png')).' - '.$model->id.'.xml';  
                    }
                }
            ],

            'processo.person.nome',
            'processo.descricao',

        ],
    ]) ?>

</div>
</section>
<?php if(!empty($data)):?>
<section>
<div class="titulo-principal"> <h5 class="titulo">XML </h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
 
            <?= Html::a('<i class="fas fa-trash-alt"></i> Eliminar', ['delete-xml', 'id' => $model->id], [
            'class' => 'btn btn-xs','title'=>'ELIMINAR XML',
            'data' => [
                'confirm' => 'PRETENDE REALENTE CONTINUAR?',
                'method' => 'post',
            ],
        ]) ?>
            <?= Html::a('<i class="fas fa-download"></i> Baixar', ['baixar-xml', 'id' => $model->id], [
            'target' => '_blank','class' => 'btn btn-xs','title'=>'baixar  XML',
            
        ]) ?>

</section>
    <?php endif;?>

<?php  Modal::begin([
                        'id'=>'modal',
                        'header' => 'Adicionar XML',
                        'toggleButton' => false,
                        'size'=>Modal::SIZE_DEFAULT
                    ]);
              Pjax::begin(['timeout'=>5000]);
                  $form2 = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Url::toRoute(['upload-xml','id'=>$model->id]),
                    'method'=>'post'
                  ]);
              ?>
                 
              <?= $form2->field($model,'xmlFile')->fileInput()->label(false)?>
                  <br>    
                  <div class="form-group">
                      <?= Html::submitButton('Salvar',['class'=>'btn'])?>
                      <?= Html::a('Cancelar','#',['class'=>'btn','onclick'=>'$("#modal").modal("hide");'])?>
                  </div>
                  <?php
                  ActiveForm::end();
              Pjax::end();
          Modal::end();
?>

