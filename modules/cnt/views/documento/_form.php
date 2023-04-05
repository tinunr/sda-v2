<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\PlanoIva;
use app\modules\cnt\models\Documento;
use app\modules\cnt\models\Natureza;
use app\modules\cnt\models\DocumentoTipo;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-2">
        <?= $form->field($model, 'codigo')->textInput() ?>
    </div>
    <div class="col-md-10">
        <?= $form->field($model, 'descricao')->textInput() ?>
    </div>
    <div class="col-md-6">
      <?= $form->field($model, 'cnt_natureza_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Natureza::find()->orderBy('descricao')->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
    </div>
    <div class="col-md-6">
      <?= $form->field($model, 'cnt_documento_tipo_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(DocumentoTipo::find()->orderBy('descricao')->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
    </div>
    <div class="col-md-6">
    	<?= $form->field($model, 'cnt_diario_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Diario::find()->orderBy('descricao')->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
    </div>
    <div class="col-md-6">    	
    <?= $form->field($model, 'cnt_plano_conta_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->orderBy('codigo')->all(), 'id','descricao'),
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
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</section>