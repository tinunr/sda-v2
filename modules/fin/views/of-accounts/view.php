<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\fin\models\Receita;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = 'Pagamento nº '.$model->id;
?>

<section>
    <div class="row">

        <p>
            <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar', ['index'], ['class' => 'btn','title'=>'Voltar']) ?>
            <?php if($model->status ==1 ):?>
            <?= Html::a('<i class="fas fa-print"></i> Imprimir', ['view-pdf', 'id'=>$model->id], ['class'=>'btn','target'=>'_blanck','title'=>'Imprimir']) ?>
            <?= Html::a('<i class="fas fa-undo"></i> Anular', ['undo', 'id' => $model->id], [
            'class' => 'btn','title'=>'ANULAR',
            'data' => [
                'confirm' => 'PRETENDE REALENTE ANULAR ESTE REGISTO?',
                'method' => 'post',
            ],
        ]) ?>
            <?php endif;?>

        </p>
        <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
             [
                'attribute'=>'numero',
                'encodeLabel' => false,
                'format' => 'html',
                'value' => function($model){
                    return yii::$app->ImgButton->Status($model->status).$model->numero.'/'.$model->bas_ano_id;                  
                }
            ],
            [
              'attribute'=>'Gerado',
              'encodeLabel' => false,
              'format' => 'html',
              'value' => function($model){
                  return empty($model->fin_receita_id)?'A Partir de Despesa':' A partir de Fatura';         
                }
            ],
            [
              'attribute'=>'Numero',
              'encodeLabel' => false,
              'format' => 'html',
              'value' => function($model){
                  return empty($model->fin_receita_id)?$model->despesa->numero.'/'.$model->despesa->bas_ano_id:$model->receita->faturaProvisoria->numero.'/'.$model->receita->faturaProvisoria->bas_ano_id;         
                }
            ], 
            
          'person.nome',
          'valor:currency',            
          'descricao',        

        ],
    ]) ?>

    </div>


</section>

<section>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Fatura / Despesa</th>
                    <th scope="col">Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; foreach ($model->item as $key => $value):?>
                <tr>
                    <th scope="row"><?=$i?></th>
                    <td>
                        <?php if (!empty($value->fin_despesa_id)):?>
                        <?=$value->despesa->numero.'/'.$value->despesa->bas_ano_id.' - '.$value->despesa->descricao?>
                        <?php elseif(!empty($value->fin_receita_id)):?>
                        <?=$value->receita->fin_receita_tipo_id == Receita::FATURA_PROVISORIA?$value->receita->faturaProvisoria->numero.'/'.$value->receita->faturaProvisoria->bas_ano_id.' - '.$value->receita->descricao:$value->receita->faturaDefinitiva->numero.'/'.$value->receita->faturaDefinitiva->bas_ano_id.' - '.$value->receita->descricao?>
                        <?php ?>
                        <?php elseif(!empty($value->fin_nota_debito_id)):?>
                        <?=$value->notaDebito->numero.'/'.$value->notaDebito->bas_ano_id.' - '.$value->notaDebito->descricao?>
                        <?php ?>
                        <?php endif;?>

                    </td>

                    <td><?=$value->valor?></td>
                </tr>
                <?php  $i++; endforeach;?>

            </tbody>
        </table>
    </div>
</section>
