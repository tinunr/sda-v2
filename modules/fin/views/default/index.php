<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
$this->title = 'Home';
?>
<?= Html::style('img {
        height: 550px;
        width: 100%;  
    }') ?>
<?= Html::img('@web/M&C.png', ['alt' => 'My logo']) ?>