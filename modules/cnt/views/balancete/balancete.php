<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use  yii\widgets\Pjax;
use app\modules\cnt\models\Diario;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EdificiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Balancete';
?>
<section>
<div class="Curso-index">

<div class="titulo-principal"> <h5 class="titulo"><?=Html::encode($this->title)?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>



  <?php  echo $this->render('_searchBalancete', ['model' => $searchModel]); ?>

  </div></div> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
           // ['class' => 'app\components\widgets\CustomActionColumn','template'=>'{view}','header'=>'Ações'],
            'razao.data:date',
            // 'cnt_natureza_id',
            [
                'label'=>'D/C',
                'attribute'=>'cnt_natureza_id',
            ],
            'valor:currency',
            'planoConta.descricao',
            'planoTerceiro.nome',
            'planoFluxoCaixa.descricao',
            'planoIva.descricao',
            
        ],
    ]); ?>
</div>
</section>


