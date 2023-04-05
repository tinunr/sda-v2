<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DesciplinaPerfilSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Processos';
?>
<section>
    <div class="desciplina-perfil-index">
        <div class="titulo-principal">
            <h5 class="titulo"><?= Html::encode($this->title) ?></h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>

        <?php echo $this->render('_processoSearch', ['model' => $searchModel]); ?>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_items',
            'options' => ['class' => 'row'],
            'itemOptions' => ['class' => 'row row-grid'],
            'pager' => [
                'class' => LinkPager::class
            ],
            'layout' => '{items}{pager}'
        ]); ?>

    </div>

</section>