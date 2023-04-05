<?php

namespace app\components\widgets;

use yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\dsp\models\Processo;

class FileBrowser extends Widget
{

    public $dir;



    public function init()
    {
        parent::init();
        if (empty($this->dir)) {
            $this->dir = "data";
        }
    }

    public function run()
    {
        $html = '<div class="row">';
        $html .= Html::hiddenInput('dir', $this->dir, ['id' => 'dir']);
        $html .= '<div class="filemanager">
                <div class="search">
                    <input type="search" placeholder="Find a file.." />
                </div>    
                <div class="breadcrumbs"></div>    
                <ul class="data"></ul>    
                <div class="nothingfound">
                    <div class="nofiles"></div>
                    <span>No files here.</span>
                </div></div>';
        $html .= '</div>';
        return $html;
    }
}