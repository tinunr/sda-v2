<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\LoginAsset;
use yii\helpers\Url;


LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?=Url::to('@web/favicon.ico')?>" type="image/x-icon">
    <?= Html::csrfMetaTags() ?>
    <title>Login</title>
    <?php $this->head() ?>
</head>


<body class="hold-transition login-page">
    <?php $this->beginBody() ?>
    <div class="login" id="login">
        <div class="login-box">

            <div class="login-box-body">
                <div>
                    <div class="logo">
                        <img src="<?=Url::to('@web/logo.png')?>">
                    </div>
                    <div id="line"></div>
                </div>
                <!-- /.login-logo -->
                <?= $content ?>
                <div class="login_footer_wrap">
                    <p> Morais & Cruz, Lda &copy; <?=date('Y')?></p>
                </div>
            </div>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
    </div>

    <p id="copyright"></p>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>