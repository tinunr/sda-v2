<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use  yii\widgets\Pjax;
use app\models\Ano;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\Documento;
use app\modules\cnt\models\LAncamento;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Diário';
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?=Html::encode($this->title)?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= Html::a('<i class="fas fa-sync"></i>','#', ['class'=>'btn','data-toggle'=>"modal",'data-target'=>'#modal'])?>

        <?php  echo Html::a('<i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>

        <?php 
    if(Yii::$app->user->can('cnt/razao/create-a')){
      echo Html::a('<i class="fas fa-list"></i>','#', ['class'=>'btn','data-toggle'=>"modal",'data-target'=>'#modalLancamento']);
    }
 ?>
    </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}','header'=>'Ações'],
           [
            'attribute'=>'numero',
            'format' => 'raw',
            'value'=>function($model){
                return  yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->cnt_diario_id.'/'.$model->bas_mes_id.'/'.$model->bas_ano_id;    

            }
          ],
            [
                'attribute'=>'Diário',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return $model->diario->id.' - '.$model->diario->descricao;                  
                }
            ],
            // 'diario.descricao',
            [
              'label'=>'Valor Documento',
              'attribute'=>'valor_debito',
              'value'=>'valor_debito',
              'format'=>'currency',
            ],
            [
                'label'=>'Data',
                'format' => 'raw',
                'value'=>function($model){
                    return  $model->mes->descricao.'-'.$model->ano->ano;    
    
                }
              ],
              'data:date',
            [
                'label'=>'Origem',
                'attribute'=>'documento_origem_id',
                'format' => 'raw',
                'value'=>function($model){
                  if (!empty($model->documento_origem_id)) { 
                    return Html::a($model->documento->codigo,$model->origemUrl(),['class'=>'btn-link','data-pjax' => 0,'target'=>'_blank']);    

                }}
            ],
        ],
    ]); ?>
    </div>
</section>




<?php 
     Modal::begin([
                        'id'=>'modal',
                        'header' => 'ATUALIZAR LIVRO DE LANÇAMENTO DIAÁRIO AUTOMATICO',
                        'toggleButton' => false,
                        'size'=>Modal::SIZE_DEFAULT
                    ]);
              Pjax::begin(['timeout'=>5000]);
                  $form = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Url::toRoute(['/cnt/razao/create-auto']),
                    'method'=>'post'
                  ]);
              ?>
<div class="col-md-6">
    <?php echo $form->field($razaoAutoCreate, 'dataInicio')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);?>
</div>
<div class="col-md-6">
    <?php echo $form->field($razaoAutoCreate, 'dataFim')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);
    ?>
</div>

<div class="col-md-12">

    <?= $form->field($razaoAutoCreate, 'cnt_documento_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Documento::find()->where(['cnt_documento_tipo_id'=>1])->orderBy('descricao')->all(), 'id', 'descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => '','multiple' => true],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?>
</div>
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



























<?php 
     Modal::begin([
                        'id'=>'modalLancamento',
                        'header' => 'LANÇAMENTO AGENDADOS',
                        'toggleButton' => false,
                        'size'=>Modal::SIZE_DEFAULT
                    ]);
              Pjax::begin(['timeout'=>5000]);
                  $form = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data'],
                    'action'=>Url::toRoute(['/cnt/razao/create-a']),
                    'method'=>'post'
                  ]);
              ?>



<div class="col-md-12">
    <?= $form->field($lancamentoCreate, 'bas_ano_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);?>
</div>
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
