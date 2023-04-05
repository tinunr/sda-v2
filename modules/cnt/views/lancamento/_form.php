<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Mes;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\LancamentoTipo;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\Ilhas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ilhas-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= $form->errorSummary($model); ?>

    <div class="col-md-6">
         <?= $form->field($model, 'cnt_lancamento_tipo_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(LancamentoTipo::find()->orderBy('descricao')->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' => false,
              ],
          ])
        ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'descricao')->textInput(['placeholder'=>'']) ?>
    </div>
    <div class="col-md-6">
    <?= $form->field($model, 'bas_mes_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id','descricao'),
          'hideSearch' => true,
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' => false,
              ],
          ])
        ?>
        </div>
        <div class="col-md-6">
    <?= $form->field($model, 'cnt_diario_id')->widget(Select2::classname(), [
          'theme' => Select2::THEME_BOOTSTRAP,
          'data' =>ArrayHelper::map((new yii\db\Query)->from('cnt_diario')->select(['id', new \yii\db\Expression("CONCAT(`id`, '-', `descricao`) as nome")])->orderBy('descricao')->all(), 'id','nome'),
          'hideSearch' => true,
          'options' => ['placeholder' => ''],
          'pluginOptions' => [
                  'allowClear' => false,
              ],
          ])
        ?>
        </div>
        


<div class="row">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 25, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $modelsLancamentoItem[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id_origem',
                    'id_destino',
                ],
            ]); ?>

            <input id ="item-control" type="hidden" name="_method" value=0 />

                <table class="container-items table table-hover">
                 <?php foreach ($modelsLancamentoItem as $i => $item): ?>
                        <tr class="item" >
                            <?php
                            // necessary for update action.
                            if (! $item->isNewRecord) {
                                echo Html::activeHiddenInput($item, "[{$i}]id");
                            }
                        ?>
                          <td>
                            <button type="button" id="removeitem" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            
                          </td>   
                          <td style="width:45%;">
                          <?= $form->field($item, "[{$i}]origem_id")->widget(Select2::classname(), [
                            'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->orderBy('id')->all(), 'id','descricao'),
                            'theme' => Select2::THEME_BOOTSTRAP,
                            'options' => ['placeholder' => ''],
                            'pluginOptions' => [
                                    'allowClear' =>true,
                                ],
                            ])->label(false)
                            ?></td>

                            <td style="width:45%;">
                            <?= $form->field($item, "[{$i}]destino_id")->widget(Select2::classname(), [
                            'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")])->orderBy('id')->all(), 'id','descricao'),
                            'theme' => Select2::THEME_BOOTSTRAP,
                            'options' => ['placeholder' => ''],
                            'pluginOptions' => [
                                    'allowClear' =>true,
                                ],
                            ])->label(false)
                            ?></td>
                        </tr>
                        
                
            <?php endforeach; ?>
                     
            
            </table>
            <button id="add-item" type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
            

            <?php DynamicFormWidget::end(); ?>
    </div>
       

   

    <div class="form-group">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
