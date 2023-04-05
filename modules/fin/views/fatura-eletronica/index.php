<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\ButtonDropdown;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use  yii\widgets\Pjax; 
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fatura';
?>
<section>
    <div class="Curso-index">

        <div class="titulo-principal">
            <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
		
		<?= Html::a('<i class="fas fa-sync"></i> ENVIAR PARA PE', '#', ['class' => 'btn', 'data-toggle' => "modal", 'data-target' => '#modalSyncPe']) ?>

         
    </div>
    </div>
    <?php yii\widgets\Pjax::begin() ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'app\components\widgets\CustomActionColumn', 'template' => '{view}'],
            [
                'attribute' => 'numero',
                'label' => 'Número',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function ($model) {
                    return yii::$app->ImgButton->statusSend($model->status, $model->send) . $model->numero . '/' . $model->bas_ano_id;
                }
            ],
            [
                'label' => 'Nº Processo',
                'attribute' => 'processo',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->processo->numero . '/' . $model->processo->bas_ano_id, ['/dsp/processo/view', 'id' => $model->dsp_processo_id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']);
                }
            ],
            'data:date',
            'valor:currency',
            // 'processo.descricao',
            'person.nome',
             [
                'label' => 'PE',
                'attribute' => 'iud',
                'format' => 'html',
                'value' => function ($model) {
                    return $model->peStatus();
                }
            ],

        ],
    ]); ?>
    <?php yii\widgets\Pjax::end() ?>

    </div>
</section>



<?php
Modal::begin([
    'id' => 'modalSyncPe',
    'header' => 'Inicializar Workflow',
    'toggleButton' => false,
    'size' => Modal::SIZE_DEFAULT,
    'options' => [
        // 'id' => 'kartik-modal',
        'tabindex' => false // important for Select2 to work properly
    ],
]);
Pjax::begin(['timeout' => 5000]);
$form = ActiveForm::begin([ 
    'action' => Url::to(['/fin/fatura-eletronica/sync-to-pe'])
]);
?>
 <?php echo $form->field($searchModel, 'beginDate')->widget(DatePicker::classname(), [
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
			
			 <?php echo $form->field($searchModel, 'endDate')->widget(DatePicker::classname(), [
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
<div class="form-group">
    <?= Html::submitButton('Sincronizar', ['class' => 'btn']) ?>
    <?= Html::a('Cancelar', '#', ['class' => 'btn', 'onclick' => '$("#modal").modal("modalSyncPe");']) ?>
</div>
<?php
ActiveForm::end();
Pjax::end();
?>
