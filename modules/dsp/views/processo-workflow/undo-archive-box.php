<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\User;
use app\modules\dsp\models\Setor;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Desarquivar Processo em bloco';
?>
<section>
<div class="Curso-index">
     <?php  echo Html::a('<i class="fa   fa-chevron-left"></i> Voltar', ['arquivo'], ['class' => 'btn btn-warning']) ?>

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<?= $this->render('_searchUndoArchiveBox', ['model' => $searchModel]); ?>
</div> 

  <?=Html::beginForm(['/dsp/processo-workflow/undo-archive-box']);?>
 

     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'id' => 'grid',
        'columns' => [
            ['class' => '\yii\grid\CheckboxColumn',
               'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model->status == 5) {
                        return ['value' => $key];
                    }
                    return ['style' => ['display' => 'none']]; // OR ['disabled' => true]
                },
                'contentOptions' => [
                  'onclick' => 'updateForm(this)',
                  //'style' => 'cursor: pointer'
              ],
            ],
            ['class' => 'yii\grid\SerialColumn'],
            
            
            [
              'label'=>'Processo',
              'attribute'=>'processo',
              'encodeLabel' => false,
              'format' => 'raw',
              'value' => function($model){
                  return Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id.' | '.$model->processo->processoStatus->descricao,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);                  
              }
        ],  
          'classificacao',
          // 'user.name',
          'setor.descricao',
          'data_inicio:date',
          // 'data_fim:dateTime',
          [
              
              'label'=>'Estado',
              'attribute'=>'status',
              'encodeLabel' => false,
              'format' => 'raw',
              'value' => function($model){
                  return $model->workflowStatus->descricao;                  
              }
          ],
            

        ],
    ]); ?>
    <?php if($dataProvider->getTotalCount()>0):?>
    <div class="navbar-fixed-bottom">
          <div class="col-md-10">
            <p><strong></strong></p>
          </div>
         
          <div class="col-md-2 ">
            <?=Html::submitButton('<i class="fa fa-inbox"></i> Remover do arquivo', ['id'=>'avancar','class' => 'btn btn-info','style' => ['display' => 'none'],'data' => [
              'confirm' => 'Pretende realmente remover do arquivo este processo?',
              'method' => 'post',
          ]]);?>
          </div>
      </div>
<?php endif;?>

</div>
</section>



<script type="text/javascript">
  function updateForm() {
    var keys = $('#grid').yiiGridView('getSelectedRows');  
    if(keys ==''){
      document.getElementById("avancar").style.display = "none";     
    }else{
    document.getElementById("avancar").style.display = "block";
    }
  }
</script>



