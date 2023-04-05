<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\modules\fin\models\DocumentoPagamento;
use app\modules\fin\models\BancoTransacaoTipo;

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

    <div class="col-md-4">
    <?=$form->field($model, 'fin_banco_id')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map((new \yii\db\Query())
          ->select(['fin_banco_conta.id AS id', "CONCAT(fin_banco.sigla, ' - ', fin_banco_conta.numero) AS numero"])
          ->from('fin_banco_conta')
          ->join('LEFT JOIN', 'fin_banco', 'fin_banco.id = fin_banco_conta.fin_banco_id')
          ->orderBy('fin_banco.sigla')
          ->all(), 'id', 'numero'),
          'options' => ['placeholder' => 'conta bancário...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);

      ?>
    </div>
    <div class="col-md-4">
    <?=$form->field($model, 'fin_documento_pagamento_id')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map(DocumentoPagamento::find()->orderBy('descricao')->all(), 'id', 'descricao'),
          'options' => ['placeholder' => 'Modalidade pagamento...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);

      ?>
    </div>


    <div class="col-md-4">
    <?=$form->field($model, 'fin_banco_transacao_tipo_id')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map(BancoTransacaoTipo::find()->orderBy('descricao')->all(), 'id', 'descricao'),
          'options' => ['placeholder' => 'Tipo de transação ...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?>
      </div>
      </div>
      <div class="row">
      <div class="col-md-4">
      <?php echo $form->field($model, 'dataInicio')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);?>
      </div>
      <div class="col-md-4">
      <?php echo $form->field($model, 'dataFim')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'removeButton' => ['icon' => 'trash'],
                        'pickerButton' => true,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayHighlight' => true,
                        ]
                ])->label(false);
    ?>
      </div>
        <?= Html::submitButton('<i class="fa fa-filter"></i>', ['class' => 'btn btn-primary']) ?>
      </div>

        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

