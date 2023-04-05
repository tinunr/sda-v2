<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Ano;
use app\modules\dsp\models\Person;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-2">
     <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
        'options' => ['placeholder' => 'Selecione ...'],
        'pluginOptions' => [
            'allowClear' => true,
            
        ],
        
    ])->label(false);?> 
    </div>
    <div class="col-md-5">
        <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map((new Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_nota_credito A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_person_id'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label(false);?>

    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'globalSearch')->textInput(['placeholder'=>'Procurar ...'])->label(false) ?>
    </div>
        <?= Html::submitButton(Yii::$app->ImgButton->Img('filter'), ['class' => 'btn btn-primary','title'=>'FILTAR']) ?>
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>

