<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\ItemTipo;

use kartik\select2\Select2;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoIva;
use app\modules\dsp\models\Item;
$this->registerJsFile(Url::to('@web/js/dsp_item_form.js'),['position' => \yii\web\View::POS_END]);

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>


    <div class="col-md-6">
 <?= $form->field($model, 'dsp_item_type_id')->widget(Select2::classname(), [
                 'theme' => Select2::THEME_BOOTSTRAP,
                 'data' =>ArrayHelper::map(ItemTipo::find()->orderBy('descricao')->all(), 'id', 'descricao'),
                'options' => ['placeholder' => '','id'=>'dsp_item_type_id','onchange'=>'disableFields(this)'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],                
            ]);?> 
            </div>
            <div class="col-md-6">


            <?= $form->field($model, 'id')->textInput(['id'=>'id']) ?> 
</div>
<div class="col-md-6">

            <?= $form->field($model, 'descricao')->textInput() ?> 
            <?= $form->field($model, 'protocolo_processo')->inline()->radioList(['0' => 'Não', '1' => 'Sim'])->label('Ativo em Protocolo Processo') ?>
            
</div>
<div class="col-md-6">



          <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Person::find()->orderBy('nome')->all(), 'id', 'nome'),
        'options' => ['placeholder' => '','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ]);?> 
</div>

<div class="row">
<div class="titulo-principal"> <h5 class="titulo">Contabilidade</h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



     <div class="col-md-6">
     <?= $form->field($model, 'cnt_plano_conta_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->where('cnt_plano_conta_tipo_id=2')->orderBy('codigo')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
    </div>
    <div class="col-md-6">
      
         <?= $form->field($model, 'cnt_plano_iva_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_iva')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->orderBy('id')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
    </div>

    <div class="col-md-6">
      
         <?= $form->field($model, 'cnt_plano_fluxo_caixa_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_fluxo_caixa')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->orderBy('codigo')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
    </div>
     



</div>


   



    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
