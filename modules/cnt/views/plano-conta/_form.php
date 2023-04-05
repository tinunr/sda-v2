<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\Natureza;
use app\modules\cnt\models\PlanoContaTipo;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <div class="col-md-2">
        <?= $form->field($model, 'id')->textInput() ?>
    </div>
    <div class="col-md-5">
        <?= $form->field($model, 'descricao')->textInput() ?>
    </div> 
     <div class="col-md-5">
      <?= $form->field($model, 'cnt_plano_conta_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->orderBy('id')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
    </div>
  </div>
    <div class="col-md-6">
     <?= $form->field($model,'cnt_natureza_id')->inline()->radioList([ 'D'=>'Debito','C'=>'Credito']) ?>
    </div>
    <div class="col-md-6">
    	<?= $form->field($model,'tem_plano_externo')->inline()->radioList([ '1'=>'Sim','0'=>'Não',]) ?> 
    </div>
    <div class="col-md-6">
    	<?= $form->field($model, 'cnt_plano_conta_tipo_id')->inline()->radioList([ '1'=>'Controlo','2'=>'Operacional']) ?>
    </div>
     <div class="col-md-6">
      <?= $form->field($model, 'is_plano_conta_iva')->inline()->radioList([ '1'=>'Sim','0'=>'Não']) ?>
    </div>
    <div class="col-md-6">
      <?= $form->field($model, 'tem_plano_fluxo_caixa')->inline()->radioList([ '1'=>'Sim','0'=>'Não']) ?>
    </div> 
 </div>
        

        
        


    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</section>