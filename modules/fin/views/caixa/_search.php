<?php

use yii\helpers\Html;
use yii\helpers\url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;

use yii\helpers\ArrayHelper;
use app\modules\fin\models\Banco;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\BancoConta;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="row">
    <div class="col-md-2">
    <?= $form->field($model, 'status')->widget(Select2::classname(), [
         'data' => [Caixa::OPEN_CAIXA => Caixa::STATUS_TEXTO[Caixa::OPEN_CAIXA],Caixa::CLOSE_CAIXA =>Caixa::STATUS_TEXTO[Caixa::CLOSE_CAIXA]],          
          'hideSearch' => false,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Estado'],
          'pluginOptions' => [
              'allowClear' => true,
          ],
      ])->label(false);?>
      </div> 

     <div class="col-md-4">
    

<?= $form->field($model, 'fin_banco_conta_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(BancoConta::find()->orderBy('numero')->all(), 'id','numero','banco.descricao'),
        'options' => ['placeholder' => 'Nº de Conta'],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ])->label(false);?>
    </div>
    
        <?= Html::submitButton(Yii::$app->ImgButton->Img('filter'), ['class' => 'btn' ,'title'=>'FILTAR']) ?>
        
      </div>

        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

