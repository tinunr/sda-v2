<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Ano;
use app\models\Mes;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\bootstrap\Modal;

$this->title = 'Modelo 106 Anexo Cliente';


$this->registerJs("(function($) {
      $('#modal').modal();
  })(jQuery);", yii\web\View::POS_END, 'lang');
?>

   <?php Modal::begin([
                          'id'=>'modal',
                          'header' => 'modelo 106 anexo cliente',
                          'toggleButton' => false,
                          'size'=>Modal::SIZE_DEFAULT,
                          'options' => [
                            // 'id' => 'kartik-modal',
                            'tabindex' => false // important for Select2 to work properly
                        ],
                      ]);
?>

<section>

    <?php $form = ActiveForm::begin([
        'action' => ['modelo106-anexo-fornecedor'],
        'method' => 'get',
        'options'=>['target'=>'_blank'],
    ]); ?>


    <div class="row">
    <div class="row">
     <div class="col-md-6">
      <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Ano ...'],
          'pluginOptions' => [
              'allowClear' => false
          ],
      ])->label('ANO');?> 
    </div>
      <div class="col-md-6">
       <?= $form->field($model, 'bas_mes_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id', 'descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Mês ...'],
          'pluginOptions' => [
              'allowClear' => false
          ],
      ])->label('MÊS');?> 
      </div>

       <?= $form->field($model, 'bas_formato_id')->widget(Select2::classname(), [
          'data' => ['1'=>'PDF','2'=>'XML'],
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Formato de focheiro ...'],
          'pluginOptions' => [
              'allowClear' => false
          ],
      ])->label('FORMATO DO FIECHEIRO');?> 
    

    
        <?= Html::submitButton('<i class="fa fa-filter"></i> Gerar Relatório', ['class' => 'btn btn-primary']) ?>
        
        

    <?php ActiveForm::end(); ?>

   <?php Modal::end();?>

</section>