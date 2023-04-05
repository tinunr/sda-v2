<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use app\models\Ano;
use app\models\Mes;

$this->title = 'Balancete';

$this->registerJs("(function($) {
      $('#modal').modal();
  })(jQuery);", yii\web\View::POS_END, 'lang');
?>

   <?php Modal::begin([
                          'id'=>'modal',
                          'header' => 'atualizar livro razão',
                          'toggleButton' => false,
                          'size'=>Modal::SIZE_DEFAULT,
                          'options' => [
                            // 'id' => 'kartik-modal',
                            'tabindex' => false // important for Select2 to work properly
                        ],
                      ]);
?>


    <?php $form = ActiveForm::begin([
        'action' => ['create'],
        'method' => 'get',
        // 'options'=>['target'=>'_blank'],
    ]); ?>

    <div class="row">
<div class="col-md-12">
    
     <div class="col-md-12">
      <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id','ano'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ])->label('Ano');?>
      </div>
    <div class="col-md-12">
      <?= $form->field($model, 'bas_mes_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id','descricao'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ])->label('Mês');?>
      </div>
    

   
        <?= Html::submitButton(Yii::$app->ImgButton->Img('update').' ataulizar razão', ['class' => 'btn btn-primary']) ?>
        
        

    <?php ActiveForm::end(); ?>
</div>
   <?php Modal::end();?>