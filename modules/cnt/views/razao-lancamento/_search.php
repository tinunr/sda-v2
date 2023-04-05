<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\bootstrap\Modal;
use app\models\Ano;
use app\models\Mes;
use app\modules\cnt\models\PlanoConta;
use app\modules\dsp\models\Person;
use yii\db\Query;
$initNomeValor = $model->cnt_plano_conta_id;

$this->title = 'Razão';
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">

     <div class="col-md-2">
      <?= $form->field($model, 'bas_ano_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id','ano'),
        'options' => ['placeholder' => 'ANO INICIO'],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ])->label('Ano');?>
      </div>
    <div class="col-md-2">
      <?= $form->field($model, 'bas_mes_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map(Mes::find()->orderBy('id')->all(), 'id','descricao'),
        'options' => ['placeholder' => 'MES INICIO'],
        'pluginOptions' => [
            'allowClear' => false,
        ],        
    ])->label('De Mês');?>
      </div>
    <div class="col-md-4">
        <?= $form->field($model, 'cnt_plano_conta_id')->widget(Select2::classname(), [
         'initValueText' => $initNomeValor,
         'theme' => Select2::THEME_BOOTSTRAP,
        'options' => ['placeholder' => 'Selecione ...','id'=>'cnt_plano_conta_id'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' =>1,
            'ajax' => [
                'url' => Url::to(['/cnt/plano-conta/plano-conta-list']),
                'dataType' => 'json',
            ],
        ],  
    ])->label('Plano de Conta');?>
    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'cnt_plano_terceiro_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
         'data' =>ArrayHelper::map((new Query)
                     ->select(['B.id',new \yii\db\Expression("CONCAT(B.id, ' - ',B.nome) as nome")])
                     ->from('cnt_razao_item A')
                     ->leftJoin('dsp_person B', 'B.id=A.cnt_plano_terceiro_id')
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => [
            'allowClear' => true,
        ],        
    ])->label('Terceiro');?>
    </div>
    
        <?= Html::submitButton(Yii::$app->ImgButton->Img('filter'), ['class' => 'btn btn-primary']) ?>
        
        

    <?php ActiveForm::end(); ?>
<div class="row pull-right">

