<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Edificios */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Origem', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<section>
<div class="Curso-index">


    <p>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
    </p>
    


  <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descricao',
        ],
    ]) 

    

    ?>
</section>




  

</div>
