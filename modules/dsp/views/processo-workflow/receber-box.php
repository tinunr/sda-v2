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

$this->title = 'Receber Processo em bloco';
?>
<section>
<div class="Curso-index">
     <?php  echo Html::a('<i class="fa   fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>


  <?php $form = ActiveForm::begin(['action'=>Url::toRoute(['/dsp/processo-workflow/receber-box']),
               ]);
           ?>
  <?=$form->field($model, 'dsp_setor_id')->widget(Select2::classname(), [
                 'data' =>ArrayHelper::map(Setor::find()->joinWith('setorUser')->where(['user_id'=>Yii::$app->user->identity->id])->andWhere(['arquivo'=>0])->orderBy('descricao')->all(), 'id','descricao'),
                 'options' => ['id' => 'dsp_setor_id'],
                 'pluginOptions' => [
                     'allowClear' => false
                 ],
             ])->label('Setor');?> 

     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'id' => 'grid',
        'columns' => [
            ['class' => '\yii\grid\CheckboxColumn',
               'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model->status == 1) {
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
              'attribute'=>'dsp_processo_id',
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
            <?=Html::submitButton('<i class="fa fa-inbox"></i> Receber', ['id'=>'avancar','class' => 'btn btn-info','style' => ['display' => 'none']]);?>
          </div>
      </div>
<?php endif;?>
<?php ActiveForm::end();?>

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



