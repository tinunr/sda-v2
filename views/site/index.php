<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* @var $this yii\web\View */
$this->title = 'Home';
?>


<div class="home-page">

    <?php foreach ($modules as $key => $module):?>
    <?php if (1==1):?>
    <div class="col-sm-12 col-md-5 col-ofset-md-1">
        <div id="item" class="ms-depth-4">
            <a href="<?=Url::toRoute([$module->url])?>">

                <div class="ca-content">
                    <div class="col-xs-22col-sm-4 col-md-2">
                        <div class="ca-icon">
                            <i class="fas fa-<?=$module->icon?> fa-2x"></i>
                        </div>
                    </div>
                    <div class="col-xs-10 col-sm-10 col-md-10">
                        <h2 class="ca-main"><?=$module->id?> </h2>
                        <h2 class="ca-main-detal"><?=$module->descricao?></h2>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
</div>