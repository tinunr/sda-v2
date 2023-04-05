<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use  yii\widgets\Pjax;
use app\modules\dsp\models\Setor;
use app\models\User;
use app\modules\dsp\models\SetorUser;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = ' Workflow '.$model->id;
?>

<section>
<div class="row">


<div class="row button-bar">
<div class="row pull-left">
    <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar',['index'], ['class'=>'btn btn-warning']) ?>
    </div>
    <div class="row pull-right">

    
     
    <?php if($model->status != 5):?>
    <?php if($model->status == 2&&!empty($model->user_id)&&!empty($model->dsp_setor_id)){
        echo Html::a('<i class="fas fa-share"></i> Enviar para Stor','#', ['class'=>'btn','data-toggle'=>"modal",'data-target'=>'#modal-send-setor']);
    }
    ?>

<?php if($model->status == 4):?>
    <?= Html::a('<i class="fas fa-undo"></i> Cancelar Envio', ['undo', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Pretende cancelar o enviao?',
                'method' => 'post',
            ],
        ]) ?>
    <?php  endif;?>

    
    <?php if(!empty(SetorUser::findOne(['user_id'=>Yii::$app->user->identity->id,'dsp_setor_id'=>[setor::ARQUIVO_PROVISORIO_ID]])->user_id)&&$model->status==2):?>
     <?= Html::a('<i class="fas fa-archive"></i> Arquivar Provisório', ['send-arquivo-provisorio', 'dsp_processo_id' => $model->dsp_processo_id,'in_workflow_id'=>$model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Pretende realmente enviar para arquivo este processo?',
                'method' => 'post',
            ],
        ]) ?>
    <?php endif;?>
    <?php endif;?>
    
    <?= Html::a('<i class="fas fa-sticky-note"></i> Obs','#', ['class'=>'btn','data-toggle'=>"modal",'data-target'=>'#modalObs'])?>


  </div>  
  </div>
    
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
             
              [
                'attribute'=>'dsp_processo_id',
                'encodeLabel' => false,
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->processo->numero.'/'.$model->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->dsp_processo_id],['class'=>'btn-link','data-pjax' => 0,'target'=>'_blanck']);                  
                }
          ],
              'classificacao',
              'user.name',
              'setor.descricao',
              'data_inicio:dateTime',
              'data_fim:dateTime',
              'workflowStatus.descricao',
              'descricao',
        ],
    ]) ?>

    </div>



    
<div class="titulo-principal"> <h5 class="titulo">OBSERVAÇÕES</h5> <div id="linha-longa"><div id="linha-curta"></div></div> </div>


<table class="table table-hover">
<tr>
       <th>Acções</th>
       <th>Nº</th>
       <th>Estado</th>
       <th>OBS</th>
</tr>
<?php foreach ($model->processo->processoObs as  $obs):?>
<?php 


?>
<tr>
       <td>
       <?php if($obs->status ==0): ?>
       <?= Html::a('<i class="fa fa-check-circle"></i>', ['/dsp/processo/obs-status', 'id'=>$obs->id,'dsp_processo_id'=>$model->id], [
       'class' => 'btn btn-xs',
        'data' => [
               'confirm' => 'Marcar como resolvido?',
               'method' => 'post',
           ],
       ]) ?>
       <?php endif;?>
       </td>
       <td><?=$obs->id?></td>
       <td><?=($obs->status==0)?'Pendente':'Resolvido'?></td>
       <td><?=$obs->obs?></td>
</tr>
   <?php endforeach;?>
</table>




    </section>


<?php Modal::begin([
                     'id'=>'modal',
                     'header' => 'ACEITAR',
                     'toggleButton' => false,
                     'size'=>Modal::SIZE_DEFAULT,
                     'options' => [
                         // 'id' => 'kartik-modal',
                         'tabindex' => false // important for Select2 to work properly
                     ],
                 ]);
           Pjax::begin(['timeout'=>5000]);
               $form2 = ActiveForm::begin([
                 'options' => ['enctype' => 'multipart/form-data'],
                 'action'=>Url::toRoute(['/dsp/processo-workflow/update','id'=>$model->id]),
                 'method'=>'post'
               ]);
           ?>
               <?=$form2->field($model, 'dsp_setor_id')->widget(Select2::classname(), [
                 'data' =>ArrayHelper::map(Setor::find()->joinWith('setorUser')->where(['user_id'=>$model->user_id])->andWhere(['arquivo'=>0])->orderBy('descricao')->all(), 'id','descricao'),
                 'options' => ['placeholder' => ''],
                 'pluginOptions' => [
                     'allowClear' => true
                 ],
             ])->label('Setor');?>   
               <div class="form-group">
                   <?= Html::submitButton('Aceitar',['class'=>'btn'])?>
                   <?= Html::a('Cancelar','#',['class'=>'btn','onclick'=>'$("#modal").modal("hide");'])?>
               </div>
               <?php
               ActiveForm::end();
           Pjax::end();
       Modal::end();
?>




<?php Modal::begin([
                'id'=>'modal-send',
                'header' => 'ENVIAR PARA PESSOA',
                'toggleButton' => false,
                'size'=>Modal::SIZE_DEFAULT,
                'options' => [
                    // 'id' => 'kartik-modal',
                    'tabindex' => false // important for Select2 to work properly
                ],
            ]);
           Pjax::begin(['timeout'=>5000]);
               $form = ActiveForm::begin([
                 'options' => ['enctype' => 'multipart/form-data'],
                 'action'=>Url::toRoute(['/dsp/processo-workflow/create','dsp_processo_id'=>$model->dsp_processo_id,'in_workflow_id'=>$model->id]),
                 'method'=>'post'
               ]);
           ?>
               <?=$form->field($newModel, 'user_id')->widget(Select2::classname(), [
                 'data' =>ArrayHelper::map(User::find()->where(['status'=>10])->all(), 'id','name'),
                 'options' => ['placeholder' => ''],
                 'pluginOptions' => [
                     'allowClear' => true
                 ],
             ])->label('Funcionário');?>   
               <div class="form-group">
                   <?= Html::submitButton('Enviar',['class'=>'btn'])?>
                   <?= Html::a('Cancelar','#',['class'=>'btn','onclick'=>'$("#modal").modal("hide");'])?>
               </div>
               <?php
               ActiveForm::end();
           Pjax::end();
       Modal::end();
?>






<?php Modal::begin([
                'id'=>'modal-send-setor',
                'header' => 'ENVIAR PARA STOR',
                'toggleButton' => false,
                'size'=>Modal::SIZE_DEFAULT,
                'options' => [
                    // 'id' => 'kartik-modal',
                    'tabindex' => false // important for Select2 to work properly
                ],
            ]);
           Pjax::begin(['timeout'=>5000]);
               $form2 = ActiveForm::begin([
                 'options' => ['enctype' => 'multipart/form-data'],
                 'action'=>Url::toRoute(['/dsp/processo-workflow/create','dsp_processo_id'=>$model->dsp_processo_id,'in_workflow_id'=>$model->id]),
                 'method'=>'post'
               ]);
           ?>
               <?=$form2->field($newModel, 'dsp_setor_id')->widget(Select2::classname(), [
                 'data' =>ArrayHelper::map(Setor::find()->where(['caixa'=>1])->all(), 'id','descricao'),
                 'options' => ['id' => 'dsp_setor_id'],
                 'pluginOptions' => [
                     'allowClear' => true
                 ],
             ])->label('Setor');?>   
               <div class="form-group">
                   <?= Html::submitButton('Enviar',['class'=>'btn'])?>
                   <?= Html::a('Cancelar','#',['class'=>'btn','onclick'=>'$("#modal").modal("hide");'])?>
               </div>
               <?php
               ActiveForm::end();
           Pjax::end();
       Modal::end();
?>

<?php
          Modal::begin([
            'id'=>'modalObs',
            'header' => 'OBS',
            'toggleButton' => false,
            'size'=>Modal::SIZE_DEFAULT
        ]);
  Pjax::begin(['timeout'=>5000]);
      $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'action'=>Url::toRoute(['/dsp/processo/create-obs','id'=>$model->dsp_processo_id]),
        'method'=>'post'
      ]);
  ?>
    
  <?= $form->field($modelObs,'obs')->textArea()?>
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






