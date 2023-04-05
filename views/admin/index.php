<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Administração';
?>
<section>
<div class="user-index">


    
    <p style="margin: 10px 0px; text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-users"></i> ADMINISTRAÇÃO  </strong></p>
    <table class="table">
      <tr>
        <td>Atualizar valor recebido</td>
        <td><?= Html::a('<i class="fa fa-dollar-sign"></i>', ['update-valor-recebido'], [
            'class' => 'btn btn-success','title'=>'ATUALIZAR VALOR DO RECEBIMENTO',
            'data' => [
                'confirm' => 'PRETENDE CCONTINUAR?',
                'method' => 'post',
            ],
        ]) ?></td>
      </tr>
    </table>

  
</div>   
</section>