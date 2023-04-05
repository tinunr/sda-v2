<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use app\models\Ano;
use app\models\Mes;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\dsp\models\Person;
use yii\db\Query;

$this->title = 'Ectrato de fluxo de caixa';
$this->registerJs("(function($) {
      $('#modal').modal();
  })(jQuery);", yii\web\View::POS_END, 'lang');
?>

   <?php Modal::begin([
                          'id'=>'modal',
                          'header' => 'EXTRATO de fluxo de caixa',
                          'toggleButton' => false,
                          'size'=>Modal::SIZE_DEFAULT,
                          'options' => [
                            // 'id' => 'kartik-modal',
                            'tabindex' => false // important for Select2 to work properly
                        ],
                      ]);
?>


    <?php $form = ActiveForm::begin([
        'action' => ['extrato-fluxo-caixa-pdf'],
        'method' => 'get',
        'options'=>['target'=>'_blank'],
    ]); ?>

    <div class="row">

    <div class="col-md-12">
    <?= $form->field($model,'bas_template_id')->inline()->radioList(['1'=>'Por Conta'])->label('Template'); ?>
    </div>
     <div class="col-md-4">
      <?= $form->field($model, 'begin_ano')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id','ano'),
        'options' => ['placeholder' => 'ANO INICIO'],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ])->label('Ano');?>
      </div>
    <div class="col-md-4">
      <?= $form->field($model, 'begin_mes')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id','descricao'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ])->label('Mês Inicio');?>
      </div>
      <div class="col-md-4">
      <?= $form->field($model, 'end_mes')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id','descricao'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ])->label('Mês Fim');?>
      </div>

    <div class="col-md-6">
        <?= $form->field($model, 'cnt_plano_fluxo_caixa_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(PlanoFluxoCaixa::find()->orderBy('codigo')->all(), 'id','codigo'),
        'options' => ['placeholder' => '','multiple' => false],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ])->label('Conta');?>
</div>

    <div class="col-md-6">
        <?= $form->field($model, 'cnt_plano_terceiro_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new Query)
                     ->select(['B.id',new \yii\db\Expression("CONCAT(B.id, ' - ',B.nome) as nome")])
                     ->from('cnt_razao_item A')
                     ->leftJoin('dsp_person B', 'B.id=A.cnt_plano_terceiro_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ])->label('Terceiro');?>
    </div>
    <div class="col-md-12">
    <?= $form->field($model,'bas_formato_id')->inline()->radioList(['1'=>'PDF'])->label('Formato'); ?>
    </div>
    
        <?= Html::submitButton('<i class="fas fa-print"></i> Imprimir', ['class' => 'btn btn-primary']) ?>
        
        

    <?php ActiveForm::end(); ?>
</div>
   <?php Modal::end();?>
