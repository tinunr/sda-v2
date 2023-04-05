<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\dsp\models\Person;
use app\models\Ano;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */


?>

<?php yii\widgets\Pjax::begin(['id' => 'new_country']) ?>
    <?php $form = ActiveForm::begin([
        'action' => ['create-a'],
        'method' => 'get',
    ]); ?>

    <div class="row">
    <div class="col-md-6">
     <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
         'theme' => Select2::THEME_BOOTSTRAP,
        'data' => ArrayHelper::map((new Query)
                     ->select(['B.id','B.nome'])
                     ->from('fin_receita A')
                     ->leftJoin('dsp_person B', 'B.id=A.dsp_person_id')
                     ->where(['>','A.saldo' ,0])
                     ->groupBy(['B.id', 'B.nome'])
                     ->orderBy('B.nome')
                     ->all(), 'id','nome'),
        'options' => ['placeholder' => 'Selecione ...','id'=>'dsp_person_id','onchange'=>'this.form.submit()'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        
    ])->label(false);?>

    </div>
   
        <div class="row pull-right">
        

    <?php ActiveForm::end(); ?>
<?php yii\widgets\Pjax::end() ?>
