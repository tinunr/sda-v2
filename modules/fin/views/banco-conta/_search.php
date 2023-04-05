<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\fin\models\Banco;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="col-md-4">
    <?= $form->field($model, 'fin_banco_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data'=>ArrayHelper::map(Banco::find()->orderBy('descricao')->all(), 'id', 'descricao'),
        'options' => ['placeholder' => 'Meio Financeiro'],
        'pluginOptions' => [
            'allowClear' => true,
            
        ],
        
    ])->label(false);?>
</div>
    
        <?= Html::submitButton(Yii::$app->ImgButton->img('filter'), ['class' => 'btn btn-primary','title'=>'FILTRAR']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

