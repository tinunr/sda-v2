<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pagamentos | documento por pagar';
?>
<section>
<div class="Curso-index">
     <?php  echo Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_searchCreate', ['model' => $searchModel]); ?>

  </div></div> 

  <?=Html::beginForm(['/fin/pagamento/create']);?>
    <?php Pjax::begin(['id' => 'countries']) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id'=>'gridP',
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\ActionColumn','template'=>'{view}'],
            ['class' => 'yii\grid\SerialColumn'],
            [
              'attribute'=>'numero',
              'format'=>'raw',
              'value'=>function($model){
                  return Yii::$app->ImgButton->Status($model->status).' '.$model->numero.'/'.$model->bas_ano_id;
              }
          ],
            ['class' => '\yii\grid\CheckboxColumn',
              'header' => Html::checkBox('selection_all', false, [
                    'id' => 'check-all',
                ]),
               'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model->saldo > 0) {
                        return ['value' => $key];
                    }
                    return ['style' => ['display' => 'none']]; // OR ['disabled' => true]
                },
                'contentOptions' => [
                  'onclick' => 'updateForm(this)',
                  'style' => 'cursor: pointer'
              ],
            ],
            
          [
            'label'=>'Processo',
            'attribute' => 'processo',
            'format' => 'raw',
            'value'=>function($model){
                return empty($model->processo->numero)?'':Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);    

            }
          ],
          ['label'=>'NORD','attribute'=>'processo.nord.id'],
          [
            'label'=>'FP',
            'format' => 'raw',
            'attribute' => 'faturaProvisoria',
            'value'=>function($model){
                return empty($model->dsp_fatura_provisoria_id)?'':Html::a($model->faturaProvisoria->numero.'/'.$model->faturaProvisoria->bas_ano_id,['/fin/fatura-provisoria/view','id'=>$model->dsp_fatura_provisoria_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);    

            }
          ],
            [
                //'label'=>'Image',
                //'format'=>'raw',
                'encodeLabel' => false,
                'format' => 'html',
               

                'headerOptions' => ['style'=>'text-align:center'],'value' => function($model){
                    if ($model->valor ==$model->valor_pago) {
                     return Html::img(Url::to('@web/img/greenBall.png'));                      # code...
                    }elseif($model->valor >$model->valor_pago&&$model->valor_pago>0){
                     return Html::img(Url::to('@web/img/gray-greenBall.png'));  
                    }else{
                     return Html::img(Url::to('@web/img/grayBal.png'));  
                     
                    }
                 }
            ],

            
            'valor:currency',
            'valor_pago:currency',
            'saldo:currency',
            'person.nome',

        ],
    ]); ?>
    <?php Pjax::end() ?>
    <?php if($dataProvider->getTotalCount()>0):?>
    <div class="navbar-fixed-bottom">
          <div class="col-md-2">
            <p><strong>TOTAL SELECIONADO</strong></p>
          </div>
          <div class="col-md-8">
            <?= Html::textInput('total_valor',null,['id'=>'total_valor','class' => 'form-control','readonly'=> true]) ?>
          </div>
          <div class="col-md-2 ">
            <?=Html::submitButton(Yii::$app->ImgButton->Img('right').' Avançar', ['id'=>'avancar','class' => 'btn btn-info','style' => ['display' => 'none']]);?>
          </div>
      </div>
<?php endif;?>
</div>
</section>


<script type="text/javascript">
  function updateForm() {
    var keys = $('#gridP').yiiGridView('getSelectedRows');  
    if(keys ==''){
      document.getElementById("avancar").style.display = "none";
      document.getElementById("total_valor").value = 0;
    }else{
      $.getJSON("/fin/pagamento/validar-person", { keys: keys}, function (data) {
        if (data.isValid == 1) {
            document.getElementById("avancar").style.display = "block"; 
            document.getElementById("total_valor").value = data.valor;          
        }else{
          document.getElementById("avancar").style.display = "none";
          document.getElementById("total_valor").value = data.valor; 
        }     
    });           
    }
  }  
</script>