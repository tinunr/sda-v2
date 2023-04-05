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

<div class="row">

    <?php $form = ActiveForm::begin(); ?>

<div class="row">
<div class="col-md-6">
<?= $form->field($model, 'descricao')->textInput() ?>
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

<div class="col-md-6">
<?= $form->field($model, 'saldo_inicial')->textInput() ?>
</div>

</div>



</div>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</section>