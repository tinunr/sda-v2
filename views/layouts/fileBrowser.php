<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\components\helpers\MenuHelper;
use app\assets\FileBrowserAsset;

/* @var $this \yii\web\View */
/* @var $content string */

FileBrowserAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= Url::to('@web/favicon.ico') ?>" rel="icon" type="image">
    <?= Html::csrfMetaTags() ?>
    <title><?= 'MC DESP / ' . strtoupper(Yii::$app->controller->module->id) . ' / ' . Html::encode($this->title) ?>
    </title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
        NavBar::begin([
            'brandLabel' => '<img class="logo" src="' . Url::to('@web/logo.png') . '" alt="logotipo unicv final" width="647" height="321" style="text-decoration: none;">',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
            'renderInnerContainer' => true,
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
                // ['label' => app\components\widgets\NotificationWidget::widget()],
                [
                    'label' => '<i class="fas  fa-cloud-upload-alt fa-2x"></i>',
                    'url' => ['/default/documentacao'],
                    // 'linkOptions' => ['target' => '_blank']
                ],
                ['label' => '<i class="fas  fa-user-circle fa-2x"></i>', 'items' => [
                    ['label' => '<i class="fa  fa-user-circle"></i> ' . Yii::$app->user->identity->name],
                    ['label' => '<i class="fa  fa-envelope"></i> ' . Yii::$app->user->identity->username],
                    '<li role="separator" class="divider"></li>',
                    ['label' => '<i class="fa  fa-lock"></i> Alterar palavra passe', 'url' => ['/user/change-password']],
                    [
                        'label' => '<i class="fas  fa-sign-out-alt"></i> Encerrar Sessão',
                        'url' => ['/site/logout'],
                        'linkOptions' => ['data-method' => 'post']
                    ],
                ]],
            ],
        ]);

        NavBar::end(); ?>
        <div class="container">
            <div class="content">
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-center"> Morais & Cruz, Lda &copy; <?= date('Y') ?></p>
            </div>
        </footer>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>