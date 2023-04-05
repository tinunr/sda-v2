<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\bootstrap\Modal;

use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\modules\fin\models\DocumentoPagamento;
use app\modules\fin\models\CaixaOperacao;
use app\modules\fin\models\BancoConta;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */


$this->registerJs("(function($) {
      $('#modal').modal();
  })(jQuery);", yii\web\View::POS_END, 'lang');
?>

   <?php Modal::begin([
                          'id'=>'modal',
                          'header' => 'EXTRATO DE CONTA',
                          'toggleButton' => false,
                          'size'=>Modal::SIZE_DEFAULT,
                          'options' => [
                            // 'id' => 'kartik-modal',
                            'tabindex' => false // important for Select2 to work properly
                        ],
                      ]);
?>
    <?php $form = ActiveForm::begin([
        'action' => ['index-pdf'],
        'method' => 'get',
         'options'=>['target'=>'_blank'],
    ]); ?>

    <div class="col-md-6">
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
      <div class="col-md-6">
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
    <div class="col-md-6">
    <?=$form->field($model, 'fin_banco_id')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map((new \yii\db\Query())
          ->select(['fin_banco_conta.id AS id', "CONCAT(fin_banco.sigla, ' - ', fin_banco_conta.numero) AS numero"])
          ->from('fin_banco_conta')
          ->join('LEFT JOIN', 'fin_banco', 'fin_banco.id = fin_banco_conta.fin_banco_id')
          ->orderBy('fin_banco.sigla')
          ->all(), 'id', 'numero'),
          'options' => ['placeholder' => 'Conta'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);

      ?>
    </div>



    <div class="col-md-6">
    <?=$form->field($model, 'fin_caixa_operacao_id')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map(CaixaOperacao::find()->orderBy('descricao')->all(), 'id', 'descricao'),
          'options' => ['placeholder' => 'Operação'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);?>
      </div>
      <div class="row">
     
        <?= Html::submitButton('<i class="fas fa-print"></i> Imprimir', ['class' => 'btn btn-primary']) ?>
      </div>

        

    <?php ActiveForm::end(); ?>

    <?php Modal::end();?>