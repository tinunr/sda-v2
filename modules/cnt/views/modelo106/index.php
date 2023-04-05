<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use  yii\widgets\Pjax;
use app\models\Ano;
use app\models\Mes;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Modelo 106';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>




  <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
  
 <?= Html::a('<i class="fas fa-plus"></i>','#', ['class'=>'btn','data-toggle'=>"modal",'data-target'=>'#modal'])?>


  </div></div> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}'],
           'anos.ano',
           'mess.descricao',
           'nif',
           'designacao_social',
           //    'data:date',
           'reparticao_financa',
           'nif_representante_legal',
           'representante_legal',
        ]
    ]); ?>
</div>
</section>
















 <?php 
     Modal::begin([
                        'id'=>'modal',
                        'header' => 'MODELO 106',
                        'toggleButton' => false,
                        'size'=>Modal::SIZE_DEFAULT
                    ]);
              Pjax::begin(['timeout'=>5000]);
                  $form = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Url::toRoute(['/cnt/modelo106/create']),
                    'method'=>'post'
                  ]);
              ?>
     <?= $form->field($model, 'ano')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Ano::find()->orderBy('id')->all(), 'id','ano'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ]);
        ?>

        <?= $form->field($model, 'mes')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
        
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