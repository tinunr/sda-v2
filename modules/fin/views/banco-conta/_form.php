<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\number\NumberControl;

use app\modules\fin\models\Banco;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\PlanoConta;

                

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

        <?= $form->field($model, 'fin_banco_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data'=>ArrayHelper::map(Banco::find()->orderBy('descricao')->all(), 'id', 'descricao'),
        'options' => ['placeholder' => 'Selecione banco ...'],
        'pluginOptions' => [
            'allowClear' => true,
            
        ],
        
    ]);?>
    <?= $form->field($model, 'numero')->textInput() ?>
    
    <?=$form->field($model, 'descoberta')->widget(NumberControl::classname(), [
        'maskedInputOptions' => [
            'groupSeparator' => '.',
            'radixPoint' => ','
        ],
    ]);?>
    
    <?=$form->field($model, 'saldo')->widget(NumberControl::classname(), [
        'maskedInputOptions' => [
            'groupSeparator' => '.',
            'radixPoint' => ','
        ],
    ]);?>

<?= $form->field($model, 'cnt_plano_conta_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->where(['cnt_plano_conta_tipo_id'=>2])->orderBy('codigo')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])
        ?>
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

<?= $form->field($model, 'cnt_plano_fluxo_caixa_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_fluxo_caixa')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->orderBy('codigo')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' => true,
              ],
          ])
        ?>
<?= $form->field($model,'status')->inline()->radioList([ '1'=>'Ativo','0'=>'Não Ativo']) ?>


<?= $form->field($model,'color')->textInput([ 'placeolder'=>'Em exadecimal Ex. #E98980']) ?>
       

     
   
        



    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->img('save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>