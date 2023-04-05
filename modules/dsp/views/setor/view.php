<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use  yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = $model->id;
?>

<section>
<div class="row">
    
<div class="row button-bar">
<div class="row pull-left">
    <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class'=>'btn btn-warning']) ?>
    </div>
    <div class="row pull-right">
    <?= Html::a('<i class="fas fa-edit"></i> Atualizar', ['update', 'id'=>$model->id], ['class' => 'btn btn-primary']) ?>  
    
    
    <?= Html::a('<i class="fas fa-plus"></i> Adicionar utilizador','#', ['class'=>'btn','data-toggle'=>"modal",'data-target'=>'#modal'])?>

  </div>  
  </div>   
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'sigla',
            'descricao',
        ],
    ]) ?>
    </div>



    <div class="titulo-principal"> <h5 class="titulo">Utilizadores</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>


<table class='table table-responsive'>
<tr>
        <th>#</th>
        <th>Nome</th>
        <th>Data Adesão</th>
</tr>
<?php foreach ($model->setorUser as $key => $value):?>
<tr>
        <td><?= Html::a('<i class="fas fa-trash-alt"></i>', ['delete-setor-user', 'dsp_setor_id' => $value->dsp_setor_id,'user_id'=>$value->user_id], [
            'class' => 'btn btn-xs',
            'data' => [
                'confirm' => 'Pretende realmente eleminar este registro?',
                'method' => 'post',
            ],
        ]) ?></td>
        <td><?=$value->user->name?></td>
        <td><?=$value->created_at?></td>
</tr>
<?php endforeach ;?>

</table>


    </section>
    <?php       

       Modal::begin([
                     'id'=>'modal',
                     'header' => 'Adicionar Utilizador',
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
                 'action'=>Url::toRoute(['/dsp/setor/create-setor-user','dsp_setor_id'=>$model->id]),
                 'method'=>'post'
               ]);
           ?>
               <?=$form2->field($SetorUser, 'user_id')->widget(Select2::classname(), [
                 'data' =>ArrayHelper::map(User::find()->orderBy('name')->all(), 'id','name'),
                 'options' => ['placeholder' => ''],
                 'pluginOptions' => [
                     'allowClear' => true
                 ],
             ]);?>
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





