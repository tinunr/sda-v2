<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii2mod\alert\Alert;
use app\components\helpers\MenuHelper;
$appModulId = ($this->context->module->id=='app')?'':'/'.$this->context->module->id;
AppAsset::register($this);  
$this->beginPage() 
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?=Url::to('@web/favicon.ico')?>" rel="icon" type="image">

    <?= Html::csrfMetaTags() ?>
    <title><?='MC DESP / '.strtoupper(Yii::$app->controller->module->id).' / '.Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="wrap">
        <?php
    NavBar::begin([
        'brandLabel' => '<img class="logo" src="'.Url::to('@web/logo.png').'" alt="logotipo unicv final" width="647" height="321" style="text-decoration: none;">',
        'brandUrl' =>'javascript:void(0)',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
            'onclick'=>'w3_open()',
        ],
        'renderInnerContainer' =>false,

    ]);

    echo Nav::widget([
        'encodeLabels' => false,  
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => [
            
            ['label' => '<i class="fa fa-home" ></i> / '.$this->context->module->id, 'url' => [$appModulId.'/default/index']],
            

        ],
    ]);


  
    echo Nav::widget([
        'encodeLabels' => false,  
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            [
                'label' => Html::img('@web/img/suport.png',['alt'=>'Suporte Tecnico']), 
                'url' =>'https://t.me/tinunr',
                'linkOptions' => ['target'=>'_blank'],
            ],
            [
                'label' => '<i class="fas  fa-plus-circle fa-2x"></i>', 
                'items' => MenuHelper::setAcessoRapido($this->context->module->id),
            ],
            [
                'label' => '<i class="fas  fa-th-large fa-2x"></i>', 'items' => [
                    ['label' => '<i class="fa  fa-tasks"></i> Operacional', 'url' => ['/dsp/default/index']],
                    ['label' => '<i class="fa  fa-search-dollar"></i> Financeiro', 'url' => ['/fin/default/index']],
                    ['label' => '<i class="fa  fa-copyright"></i> Contabilidade', 'url' => ['/cnt/default/index']],
                    ['label' => '<i class="fa  fa-cog"></i> Adminstrator', 'url' => ['/default/index']],
                ],
            ],
            
            
            [
                'label' => '<i class="fas  fa-cloud-upload-alt fa-2x"></i>',
                'url' => ['/file-browser/index'],
                'linkOptions' => ['target' => '_blank']
            ],
            

                ['label' => '<i class="fas  fa-user-circle fa-2x"></i>', 
                'items' => [
                    ['label' => '<i class="fa  fa-user-circle"></i> '.Yii::$app->user->identity->name],
                    ['label' => '<i class="fa  fa-envelope"></i> '.Yii::$app->user->identity->username],
                    '<li role="separator" class="divider"></li>',
                    ['label' => '<i class="fa  fa-lock"></i> Alterar Password', 'url' => ['/user/chang-password']],
                    [
                        'label' => '<i class="fas  fa-sign-out-alt"></i> Encerrar Sessão',
                        'url' => ['/site/logout'],
                        'linkOptions' => ['data-method' => 'post']
                    ],
                ],
            ],
        ],
    ]);
    ?>


        <?php NavBar::end();
    ?>
        <nav class="w3-sidebar w3-bar-block w3-collapse w3-large w3-theme-l5 w3-animate-left" id="mySidebar">
            <?=$this->render('menu/_'.$this->context->module->id); ?>

        </nav>




        <div class="w3-main" style="margin-left:250px">
            <div class="content">
                <?= Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) 
                ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>





        </div>

        <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
<script>
// Get the Sidebar
var mySidebar = document.getElementById("mySidebar");

// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");

// Toggle between showing and hiding the sidebar, and add overlay effect
function w3_open() {
    if (mySidebar.style.display === 'block') {
        mySidebar.style.display = 'none';
        overlayBg.style.display = "none";
    } else {
        mySidebar.style.display = 'block';
        overlayBg.style.display = "block";
    }
}

// Close the sidebar with the close button
function w3_close() {
    if (mySidebar.style.display === 'block') {
        mySidebar.style.display = 'none';
        overlayBg.style.display = "none";
    } else {
        mySidebar.style.display = 'block';
        overlayBg.style.display = "block";
    }
}
</script>