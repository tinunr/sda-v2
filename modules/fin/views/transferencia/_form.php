<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;

use app\modules\fin\models\Banco;
use app\modules\fin\models\BancoConta;
use app\modules\fin\models\DocumentoPagamento;


/* @var $this yii\web\View */
/* @var $model app\models\Edificios */
/* @var $form yii\widgets\ActiveForm */
?>
<section>


    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model); ?>
<div class="row">
<div class="col-md-6">
<?= $form->field($model, 'referencia')->textInput() ?>
</div>
<div class="col-md-6">
<?php echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ]);
    ?>
</div>
</div>
<div class="row">

  <div class="col-md-6">
    <?=$form->field($model, 'fin_banco_origem_id')->widget(Select2::classname(), [
          'theme'=>Select2::THEME_BOOTSTRAP,
          'data' =>ArrayHelper::map(Banco::find()->orderBy('descricao')->all(), 'id', 'descricao'),
          'options' => ['placeholder' => '','id'=>'fin_banco_origem_id'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);

      ?>
               
    </div>
     <div class="col-md-6">
        <?=$form->field($model, 'fin_banco_conta_id_origem')->widget(DepDrop::classname(), [
            'type'=>DepDrop::TYPE_SELECT2,
            'data' => ArrayHelper::map(BancoConta::find()->orderBy('numero')->all(), 'id', 'numero'),
            'options' => ['id'=>'fin_banco_conta_id_origem'],
            'pluginOptions'=>[
                'depends'=>['fin_banco_origem_id'],
                'allowClear' => true,
                'placeholder' => 'Select...',
                'url' => Url::to(['/fin/banco-conta/conta-list'])
            ]
        ]);?> 
    </div>
  </div>

  <div class="row">

  <div class="col-md-6">
    <?=$form->field($model, 'fin_banco_destino_id')->widget(Select2::classname(), [
          'theme'=>Select2::THEME_BOOTSTRAP,
          'data' =>ArrayHelper::map(Banco::find()->orderBy('descricao')->all(), 'id', 'descricao'),
          'options' => ['placeholder' => '','id'=>'fin_banco_destino_id'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);

      ?>
               
    </div>
     <div class="col-md-6">
        <?=$form->field($model, 'fin_banco_conta_id_destino')->widget(DepDrop::classname(), [
            'type'=>DepDrop::TYPE_SELECT2,
            'data' => ArrayHelper::map(BancoConta::find()->orderBy('numero')->all(), 'id', 'numero'),
            'options' => ['id'=>'fin_banco_conta_id_destino'],
            'pluginOptions'=>[
                'depends'=>['fin_banco_destino_id'],
                'allowClear' => true,
                'placeholder' => 'Select...',
                'url' => Url::to(['/fin/banco-conta/conta-list'])
            ]
        ]);?> 
    </div>
  </div>


<div class="row">
<div class="col-md-6">
<?= $form->field($model, 'valor')->textInput() ?>
</div>

</div>
<?= $form->field($model, 'descricao')->textArea() ?>


</div>

    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</section>