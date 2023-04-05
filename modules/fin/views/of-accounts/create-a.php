<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'faturas Provisória';
?>
<section>
<div class="Curso-index">
     <?php  echo Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning','title'=>'VOLTAR']) ?>

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_searchCreate', ['model' => $searchModel]); ?>

  </div></div> 

  <?=Html::beginForm(['/fin/of-accounts/create']);?>
    <?php Pjax::begin(['id' => 'countries']) ?>
    <?= GridView::widget([
        'dataProvider' => $receitaDataProvider,
        'id'=>'grid',
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\ActionColumn','template'=>'{view}'],
            // ['class' => 'yii\grid\SerialColumn'],
            // 'faturaProvisoria.numero',
            // [
                // 'attribute'=>'faturaProvisoria.numero',
                // 'encodeLabel' => false,
                // 'format' => 'html',
                // 'value' => function($model){
                    // return yii::$app->ImgButton->Status($model->faturaProvisoria->status).$model->faturaProvisoria->numero.'/'.$model->faturaProvisoria->bas_ano_id;
                    // }
            // ], 
			[
              'label'=>'Nº FP | FD',
              'attribute'=>'faturaProvisoria',
              'format' => 'raw',
              'value'=>function($model){
                   if($model->fin_receita_tipo_id == 1){
                    return Html::a($model->faturaProvisoria->numero.'/'.$model->faturaProvisoria->bas_ano_id,['/fin/fatura-provisoria/view','id'=>$model->dsp_fataura_provisoria_id],['class'=>'btn-link','target'=>'_blanck']);
                  }else{
                  return Html::a($model->faturaDefinitiva->numero.'/'.$model->faturaDefinitiva->bas_ano_id,['/fin/fatura-definitiva/view','id'=>$model->fin_fataura_definitiva_id],['class'=>'btn-link','target'=>'_blanck']);
                  }
              }
            ],			
                

            ['class' => '\yii\grid\CheckboxColumn',
               'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model->saldo > 0) {
                        return ['value' => $key];
                    }
                    return ['style' => ['display' => 'none']]; // OR ['disabled' => true]
                },
                'contentOptions' => [
                  'onclick' => 'updateForm(this)',
                  //'style' => 'cursor: pointer'
              ],
            ],
            // 'numero',
            [
                //'label'=>'Image',
                //'format'=>'raw',
                'encodeLabel' => false,
                'format' => 'html',
               

                'headerOptions' => ['style'=>'text-align:center'],'value' => function($model){
                    if ($model->valor ==$model->valor_recebido) {
                     return Html::img(Url::to('@web/img/greenBall.png'));                      # code...
                    }elseif($model->valor >$model->valor_recebido&&$model->valor_recebido>0){
                     return Html::img(Url::to('@web/img/gray-greenBall.png'));  
                    }else{
                     return Html::img(Url::to('@web/img/grayBal.png'));  
                     
                    }
                 }
            ],

            
            'valor:currency',
            'valor_recebido:currency',
            'saldo:currency',
            'person.nome',

        ],
    ]); ?>



    <?php Pjax::end() ?>
    
</div>
<div class="navbar-fixed-bottom">
          <div class="col-md-2">
          </div>
          <div class="col-md-8">
          </div>
          <div class="col-md-2 ">
            <?=Html::submitButton(Yii::$app->ImgButton->Img('right').' Avançar', ['id'=>'avancar','class' => 'btn btn-info','style' => ['display' => 'none']]);?>
          </div>
      </div>
</section>


<script type="text/javascript">
  function updateForm() {
    var keys = $('#grid').yiiGridView('getSelectedRows');  
    if(keys ==''){
      document.getElementById("avancar").style.display = "none";
    }else{
      $.getJSON("/fin/of-accounts/validar-person", { keys: keys}, function (data) {
        if (data==1) {
            document.getElementById("avancar").style.display = "block";           
        }else{
          document.getElementById("avancar").style.display = "none";
        }     
    });      //alert(keys);      
    }
  }
  // window.setInterval(updateForm,1000);
</script>