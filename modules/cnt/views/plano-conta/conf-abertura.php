<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\modules\cnt\models\PlanoConta;
/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Configurar Abertura de Conta';
?>

<section>
<div class="row">
<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>

<?php $form = ActiveForm::begin(); ?>
    <div class="row">
    
    
     <div class="col-md-5">
      <?= $form->field($model, 'id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")]) ->where(['cnt_plano_conta_tipo_id'=>2])->orderBy('id')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Conta'],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])->label(false)
        ?>
    </div>
  
    <div class="col-md-5">
      <?= $form->field($model, 'cnt_plano_conta_abertura')->widget(Select2::classname(), [
          'data' => ArrayHelper::map((new yii\db\Query)->from('cnt_plano_conta')->select(['id', new \yii\db\Expression("CONCAT(`id`, ' - ', `descricao`) as descricao")]) ->where(['cnt_plano_conta_tipo_id'=>2])->orderBy('id')->all(), 'id','descricao'),
          'theme' => Select2::THEME_BOOTSTRAP,
          'options' => ['placeholder' => 'Abrir Em'],
          'pluginOptions' => [
                  'allowClear' =>true,
              ],
          ])->label(false);
        ?>
    </div>
        

        

        
        


    <div class="col-md-2">
        <?= Html::submitButton(Yii::$app->ImgButton->Img('save').' Salvar', ['class' => 'btn btn-success']) ?>
    </div>
 </div>
    <?php ActiveForm::end(); ?>


<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Codigo</th>
                <th>Conta</th>
                <th>Codigo</th>
                <th>Aberto Em</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($data as $value):?>
            <tr>
                <td>  <?= Html::a(Yii::$app->ImgButton->Img('delete'), ['conf-abertura-delete', 'id' => $value->id], [
            'class' => 'btn btn-xs','title'=>'Eliminar',
            'data' => [
                'confirm' => 'PRETENDE REALMENTE ELIMINAR ESTE ITEM ?',
                'method' => 'post',
            ],
        ]) ?></td>
                <td><?=$value->id?></td>
                <td><?=$value->descricao?></td>
                <td><?=$value->cnt_plano_conta_abertura?></td>
                <td><?=$value->abertura->descricao?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
</div>

    
</div>


    </section>






