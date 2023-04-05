<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\modules\dsp\models\Setor;
use app\models\User;
use app\modules\dsp\models\SetorUser;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Enviar Processo em bloco';
?>
<section>
    <div class="Curso-index">
        <?php echo Html::a('<i class="fa   fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>

        <div class="titulo-principal">
            <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>


        <?php $form = ActiveForm::begin([
      'action' => Url::toRoute(['/dsp/processo-workflow/enviar-box']),
    ]);
    ?>
        <div class="row">
            <div class="col-md-4">

                <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(User::find()->where(['status' => 10])->all(), 'id', 'name'),
          'options' => ['placeholder' => '', 'id' => 'user_id'],
          'pluginOptions' => [
            'allowClear' => false
          ],
        ])->label('Enviar Para'); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'dsp_setor_id')->widget(DepDrop::classname(), [
          'type' => DepDrop::TYPE_SELECT2,
          'options' => ['id' => 'dsp_setor_id'],
          'select2Options' => ['pluginOptions' => ['allowClear' => false]],
          'pluginOptions' => [
            'depends' => ['user_id'],
            'placeholder' => 'Selecione ...',
            'url' => Url::to(['/dsp/setor/setor-user']),
            'initialize' => true,

          ]
        ])->label('Setor');
        ?>
            </div>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'descricao')->textArea()->label('Mensagem'); ?>
        </div>

        <?= GridView::widget([
      'dataProvider' => $dataProvider,
      //'filterModel' => $searchModel,
      'id' => 'grid',
      'columns' => [
        [
          'class' => '\yii\grid\CheckboxColumn',
          'checkboxOptions' => function ($model, $key, $index, $column) {
            if ($model->status == 2) {
              return ['value' => $key];
            }
            return ['style' => ['display' => 'none']]; // OR ['disabled' => true]
          },
          'contentOptions' => [
            'onclick' => 'updateForm(this)',
            //'style' => 'cursor: pointer'
          ],
        ],
        ['class' => 'yii\grid\SerialColumn'],

        [
          'attribute' => 'dsp_processo_id',
          'encodeLabel' => false,
          'format' => 'raw',
          'value' => function ($model) {
            return Html::a($model->processo->numero . '/' . $model->processo->bas_ano_id . ' | ' . $model->processo->processoStatus->descricao, ['/dsp/processo/view', 'id' => $model->dsp_processo_id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blanck']);
          }
        ],
        'classificacao',
        // 'user.name',
        'setor.descricao',
        'data_inicio:dateTime',
        'data_fim:dateTime',
        'workflowStatus.descricao'


      ],
    ]); ?>
        <?php if ($dataProvider->getTotalCount() > 0) : ?>
        <div class="navbar-fixed-bottom">
            <div class="col-md-10">
                <p><strong></strong></p>
            </div>

            <div class="col-md-2 ">
                <?= Html::submitButton('<i class="fa fa-share"></i> Enviar', ['id' => 'avancar', 'class' => 'btn btn-info', 'style' => ['display' => 'none']]); ?>

            </div>
        </div>
        <?php endif; ?>
        <?php ActiveForm::end(); ?>
    </div>
</section>



<script type="text/javascript">
function updateForm() {
    var keys = $('#grid').yiiGridView('getSelectedRows');
    if (keys == '') {
        document.getElementById("avancar").style.display = "none";
    } else {
        document.getElementById("avancar").style.display = "block";
    }
}
</script>
