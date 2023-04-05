<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Receita | Documento por receber';
?>
<section>
    <div class="Curso-index">
        <?php  echo Html::a('<i class="fa   fa-chevron-left"></i> Voltar', ['index'], ['class' => 'btn btn-warning']) ?>

        <div class="titulo-principal">
            <h5 class="titulo"><?=Html::encode($this->title)?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>



        <?php  echo $this->render('_searchCreate', ['model' => $searchModel]); ?>

    </div>
    </div>
    <?=Html::beginForm(['/fin/recebimento/create']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'grid',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => '\yii\grid\CheckboxColumn',
               'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model->saldo > 0) {
                        return ['value' => $key];
                    }
                    return ['style' => ['display' => 'none']]; // OR ['disabled' => true]
                },
                'contentOptions' => [
                  'onclick' => 'updateForm(this)',
              ],
            ],
            [
              'label'=>'Nº FP | FD',
              'attribute'=>'faturaProvisoria',
              'format' => 'raw',
              'value'=>function($model){
                   if($model->fin_receita_tipo_id == 1){
                    return Html::a($model->faturaProvisoria->numero.'/'.$model->faturaProvisoria->bas_ano_id,['/fin/fatura-provisoria/view','id'=>$model->dsp_fataura_provisoria_id],['class'=>'btn-link','target'=>'_blanck']);
                  }else{
                  return Html::a($model->faturaDefinitiva->numero.'/'.$model->faturaDefinitiva->bas_ano_id,['/fin/fatura-definitiva/view','id'=>$model->fin_fataura_definitiva_id],['class'=>'btn-link','target'=>'_blanck']);
                  }
              }
            ],
            [
              'label'=>'Nº processo',
              'format' => 'raw',
              'value'=>function($model){
                if($model->fin_receita_tipo_id == 1){
                  return Html::a($model->faturaProvisoria->processo->numero.'/'.$model->faturaProvisoria->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->faturaProvisoria->processo->id],['class'=>'btn-link','target'=>'_blanck']);
                  }else{
                  return Html::a($model->faturaDefinitiva->processo->numero.'/'.$model->faturaDefinitiva->processo->bas_ano_id,['/dsp/processo/view','id'=>$model->faturaDefinitiva->processo->id],['class'=>'btn-link','target'=>'_blanck']);
                  }

              }
            ],
            'valor:currency',            
            'valor_recebido:currency',            
            'saldo:currency',            
            'faturaProvisoria.person.nome',

        ],
    ]); ?>
    <?php if($dataProvider->getTotalCount()>0):?>
    <div class="navbar-fixed-bottom">
        <div class="col-md-2">
            <p><strong>TOTAL SELECIONADO</strong></p>
        </div>
        <div class="col-md-8">
            <?= Html::textInput('total_valor',null,['id'=>'total_valor','class' => 'form-control','readonly'=> true]) ?>
        </div>
        <div class="col-md-2 ">
            <?=Html::submitButton(Yii::$app->ImgButton->Img('right').' Avançar', ['id'=>'avancar','class' => 'btn btn-info','style' => ['display' => 'none']]);?>
        </div>
    </div>
    <?php endif;?>
    </div>
</section>



<script type="text/javascript">
var urlFinRecebimentoValidarPerson = "<?=Url::to(['/fin/recebimento/validar-person'])?>";

function updateForm() {
    var keys = $('#grid').yiiGridView('getSelectedRows');
    if (keys == '') {
        document.getElementById("avancar").style.display = "none";
        document.getElementById("total_valor").value = 0;

    } else {
        $.getJSON(urlFinRecebimentoValidarPerson, {
            keys: keys
        }, function(data) {
            if (data.isValid == 1) {
                document.getElementById("avancar").style.display = "block";
                document.getElementById("total_valor").value = data.valor;
            } else {
                document.getElementById("avancar").style.display = "none";
                document.getElementById("total_valor").value = data.valor;
            }
        });
    }
}
</script>
