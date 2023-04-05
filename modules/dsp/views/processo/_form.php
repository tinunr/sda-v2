<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\modules\dsp\models\Person;
use app\modules\dsp\models\ProcessoStatus;
use app\models\Ano;
use app\modules\dsp\models\Desembaraco;
use app\modules\dsp\models\DespachoDocumento;
use app\modules\dsp\models\Tarefa;
use app\models\User;
use app\models\Pais;
use app\modules\dsp\models\Setor;
use kartik\select2\Select2;
use app\modules\fin\models\Currency;
use yii\bootstrap\Modal;
use  yii\widgets\Pjax;
use kartik\number\NumberControl;
use kartik\depdrop\DepDrop;

$initValueText = empty($model->user_id) ? '' : User::findOne($model->user_id)->name;
$initNomeValor = empty($model->nome_fatura) ? '' : Person::findOne($model->nome_fatura)->nome;
$initDspPersonId = empty($model->dsp_person_id) ? '' : Person::findOne($model->dsp_person_id)->nome;
/* @var $this yii\web\View */
/* @var $model backend\models\DesciplinaPerfil */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="desciplina-perfil-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'id')->hiddenInput(['id' => 'dsp_processo_id'])->label(false);
    }
    ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'numero')->textInput(['placeholder' => 'Nº Processo', 'readonly' => true, 'id' => 'numero']) ?>
        </div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                'type' => DatePicker::TYPE_INPUT,
                'removeButton' => ['icon' => 'trash'],
                'pickerButton' => true,
                'options' => ['id' => 'data', 'onchange' => 'updateFormNumero(this)'],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                    'todayHighlight' => true,
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'dsp_person_id')->widget(Select2::classname(), [
                'initValueText' => $initDspPersonId,
                'theme' => Select2::THEME_BOOTSTRAP,
                'options' => ['placeholder' => 'Selecione ...', 'id' => 'dsp_person_id', 'onchange' => 'updateForm(this)'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => Url::to(['/dsp/person/person-list']),
                        'dataType' => 'json',
                    ],
                ],
                'addon' => [
                    'append' => [
                        'content' => Html::a('<span class="fa fa-plus"></span>', '#', ['class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => '#modal']),
                        'asButton' => true
                    ]
                ]

            ]); ?>

        </div>
        <div class="col-md-6">

            <?= $form->field($model, 'nome_fatura')->widget(Select2::classname(), [
                'initValueText' => $initNomeValor,
                'theme' => Select2::THEME_BOOTSTRAP,
                'options' => ['placeholder' => 'Selecione ...', 'id' => 'nome_fatura', 'disabled' => $readonly],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => Url::to(['/dsp/person/person-list']),
                        'dataType' => 'json',
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'valor')->widget(NumberControl::classname(), [
            'maskedInputOptions' => [
                'groupSeparator' => '.',
                'radixPoint' => ','
            ],
        ]); ?>
    </div>
    <div class="col-md-6">

        <?= $form->field($model, 'fin_currency_id')->widget(Select2::classname(), [
            'theme' => Select2::THEME_BOOTSTRAP,
            'data' => ArrayHelper::map(Currency::find()->orderBy('descricao')->all(), 'id', 'descricao'),
            'options' => ['placeholder' => 'Selecione ...'],
            'pluginOptions' => [
                'allowClear' => true,

            ],

        ]); ?>
    </div>

    <?= $form->field($model, 'descricao')->textArea(['readonly' => $readonly]) ?>







    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'n_registro_tn')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'n_levantamento')->textInput(['id' => 'n_levantamento']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'n_levantamento_ano_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Ano::find()->orderBy('ano')->all(), 'id', 'ano'),
                'hideSearch' => false,
                'theme' => Select2::THEME_BOOTSTRAP,
                'options' => ['placeholder' => 'Ano'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

        </div>
        <div class="col-md-3">

            <?= $form->field($model, 'n_levantamento_desembarco_id')->widget(DepDrop::classname(), [
                'type' => DepDrop::TYPE_SELECT2,
                'options' => ['id' => 'n_levantamento_desembarco_id'],
                'data' => ArrayHelper::map(Desembaraco::find()->orderBy('descricao')->all(), 'id', 'code'),
                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                'pluginOptions' => [
                    'depends' => ['dsp_processo_id'],
                    'placeholder' => 'Selecione ...',
                    'url' => Url::to(['/dsp/nord/desembarco-pr']),
                    'initialize' => true,

                ]
            ]);
            ?>
        </div>
        <div class="row">
            <div class="col-md-4">

                <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
                    'initValueText' => $initValueText,
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => '', 'id' => 'user_id'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => Url::to(['/user/user-list']),
                            'dataType' => 'json',
                        ],
                    ],
                ]); ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'dsp_setor_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Setor::find()->all(), 'id', 'descricao'),
                    'options' => ['placeholder' => '', 'id' => 'dsp_setor_id'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]); ?>
            </div>
            <div class="col-md-4">


                <?= $form->field($model, 'status')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(ProcessoStatus::find()->all(), 'id', 'descricao'),
                    'options' => ['placeholder' => 'Selecione ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'disabled' => true,
                    ],
                ]);

                ?>
            </div>
        </div>


        <?= $form->field($model, 'processo_tce')->widget(Select2::classname(), [
            'options' => ['placeholder' => '', 'multiple' => true],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', ' '],
                'maximumInputLength' => 10
            ],
        ])->label('TCE'); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'despacho_documento')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(DespachoDocumento::find()->all(), 'id', 'descricao'),
                    'hideSearch' => true,
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => '', 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Documento'); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'processo_tarefa')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Tarefa::find()->all(), 'id', 'descricao'),
                    'hideSearch' => true,
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => '', 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Tarefa'); ?>
            </div>
        </div>






        <div class="form-group">
            <?= Html::submitButton('Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>









    <?php

    Modal::begin([
        'id' => 'modal',
        'header' => 'NOVO CLIENTE / FORNECEDOR',
        'toggleButton' => false,
        'size' => Modal::SIZE_LARGE,
        'options' => [
            // 'id' => 'kartik-modal',
            'tabindex' => false // important for Select2 to work properly
        ],
    ]);
    Pjax::begin(['timeout' => 5000]);
    $form = ActiveForm::begin();
    ?>




    <div class="row">

        <div class="col-md-12">
            <?= $form->field($modelCliente, 'nome')->textInput(['maxlength' => true])->label('Nome') ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelCliente, 'telefone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($modelCliente, 'telemovel')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($modelCliente, 'nif')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($modelCliente, 'email')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelCliente, 'is_cliente')->inline()->radioList(['1' => 'Sim', '0' => 'Não',]) ?>
        </div>
        <div class="col-md-6">

            <?= $form->field($modelCliente, 'is_fornecedor')->inline()->radioList(['1' => 'Sim', '0' => 'Não',]) ?>
        </div>
        <?= $form->field($modelCliente, 'bas_pais_id')->widget(Select2::classname(), [
            'theme' => Select2::THEME_BOOTSTRAP,
            'data' => ArrayHelper::map(Pais::find()->orderBy('name')->all(), 'id', 'name'),
            'options' => ['placeholder' => ''],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]); ?>
    </div>
    <?= $form->field($modelCliente, 'endereco')->textArea(['maxlength' => true]) ?>
</div>
<br>
<div class="form-group">
    <?= Html::submitButton('Salvar', ['class' => 'btn']) ?>
    <?= Html::a('Cancelar', '#', ['class' => 'btn', 'onclick' => '$("#modal").modal("hide");']) ?>
</div>
<?php
ActiveForm::end();
Pjax::end();
Modal::end();
?>


<script>
function updateForm(obj) {
    var dsp_person_id = $("#dsp_person_id").val();
    $("#nome_fatura").val(dsp_person_id).trigger('change');
    $.getJSON("/dsp/person/get-person", {
        q: dsp_person_id
    }, function(data) {
        if (data) {
            $("#nome_fatura").empty().append('<option value="' + data.id + '">' + data.text + '</option>').val(
                data.id).trigger('change');

        }

    });
}

function updateFormNumero(obj) {
    var data = $("#data").val();
    $.getJSON("/documento-numero/get-number", {
        bas_documento_id: 3,
        data: data
    }, function(data) {
        if (data) {
            $("#numero").val(data);
        }
    });
}
</script>
