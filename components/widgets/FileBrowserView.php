<?php

namespace app\components\widgets;

use yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\models\FileBrowser;

class FileBrowserView extends Widget
{

    public $dir;
    public $dir_complemento;
    public $data;
    public $data_complemento;
    public $action_id;
    public $action;
    public $item_id;
    public $can_edit;
    public array $folders;


    public function init()
    {
        parent::init(); 
        switch ($this->action_id) {
            case 'dsp_processo':
                $this->action = Url::toRoute(['/dsp/processo/file-browser', 'dsp_processo_id' => $this->item_id]);
                break;
            case 'fin_transferencia':
                $this->action = Url::toRoute(['/fin/transferencia/file-browser', 'fin_transferencia_id' => $this->item_id]);
                break;
            case 'fin_despesa':
                $this->action = Url::toRoute(['/fin/despesa/file-browser', 'fin_despesa_id' => $this->item_id]);
                break;
            case 'fin_recebimento':
                $this->action = Url::toRoute(['/fin/recebimento/file-browser', 'fin_recebimento_id' => $this->item_id]);;
                break;
            case 'fin_pagamento':
                $this->action = Url::toRoute(['/fin/pagamento/file-browser', 'fin_pagamento_id' => $this->item_id]);;
                break;
            case 'fin_pagamento_ordem':
                $this->action = Url::toRoute(['/fin/pagamento-ordem/file-browser', 'fin_pagamento_ordem_id' => $this->item_id]);;
                break;
            case 'cnt_razao':
                $this->action = Url::toRoute(['/cnt/razao/file-browser', 'cnt_razao_id' => $this->item_id]);;
                break;
        }
    }

    public function run()
    {
        $model = new FileBrowser();
        $html = '<div class="titulo-principal">
            <h5 class="titulo">Anexos</h5>
            <div id="linha-longa">
                <div id="linha-curta"></div>
            </div>
        </div>';
        $html .= Html::beginTag('div', ['class' => 'file-manager']);
        $html .= Html::beginTag('ul', ['class' => 'data animated']);
        // $this->data_complemento = \app\components\helpers\UploadFileHelper::scanData($this->dir_complemento);
        if(!empty($this->folders) ):
            foreach($this->folders as $folder) :
                $contents = \app\components\helpers\UploadFileHelper::scanData($folder); 
                foreach ($contents as $f) {
                    $html .= Html::beginTag('li', ['class' => 'files']);
                    $html .= Html::tag('span', '.' . self::fileExtension($f['name']), ['class' => 'icon file f-' . self::fileExtension($f['name'])]);
                    $html .= Html::tag('span',  $f['name'], ['class' => 'name']);
                    $html .= Html::tag('span', empty($f['size']) ? (empty($f['items']) ? '' : count($f['items'])) : self::bytesToSize($f['size']), ['class' => 'details']);
                    $html .= Html::tag('span', $f['last_modified'], ['class' => 'last_modified']);
                    $html .= Html::beginTag('div', ['class' => 'row']);
                    $html .= Html::a('', Url::to('@web/' . $f['path']), ['class' => 'btn-eye', 'target' => '_blanck']);
                    $html .= Html::endTag('div');
                    $html .= Html::endTag('li');
                }
            endforeach;
        endif;
        if(!empty($this->dir_complemento) ): 
        $data_complemento = \app\components\helpers\UploadFileHelper::scanData($this->dir_complemento); 
         foreach ($data_complemento as $f) {
                $html .= Html::beginTag('li', ['class' => 'files']);
                $html .= Html::tag('span', '.' . self::fileExtension($f['name']), ['class' => 'icon file f-' . self::fileExtension($f['name'])]);
                $html .= Html::tag('span',  $f['name'], ['class' => 'name']);
                $html .= Html::tag('span', empty($f['size']) ? (empty($f['items']) ? '' : count($f['items'])) : self::bytesToSize($f['size']), ['class' => 'details']);
                $html .= Html::tag('span', $f['last_modified'], ['class' => 'last_modified']);
                $html .= Html::beginTag('div', ['class' => 'row']);
                $html .= Html::a('', Url::to('@web/' . $f['path']), ['class' => 'btn-eye', 'target' => '_blanck']);
                $html .= Html::endTag('div');
                $html .= Html::endTag('li');
            }
        endif;
        if(!empty($this->dir) ): 
            $data = \app\components\helpers\UploadFileHelper::scanData($this->dir); 
            foreach ($data as $f) {
                $html .= Html::beginTag('li', ['class' => 'files']);
                $html .= Html::tag('span', '.' . self::fileExtension($f['name']), ['class' => 'icon file f-' . self::fileExtension($f['name'])]);
                $html .= Html::tag('span',  $f['name'], ['class' => 'name']);
                $html .= Html::tag('span', empty($f['size']) ? (empty($f['items']) ? '' : count($f['items'])) : self::bytesToSize($f['size']), ['class' => 'details']);
                $html .= Html::tag('span', $f['last_modified'], ['class' => 'last_modified']);
                $html .= Html::beginTag('div', ['class' => 'row']);
                if ($this->can_edit) {
                    $html .= Html::a('<i class="fas fa-trash"></i>', ['/file-browser/delete', 'path' => $f['path']], [
                        'class' => 'btn-trash',
                        'data' => [
                            'confirm' => 'Pretende realmente eliminar este ficheiro?',
                            'method' => 'post',
                        ],
                    ]);
                }
                $html .= Html::a('', Url::to('@web/' . $f['path']), ['class' => 'btn-eye', 'target' => '_blanck']);
                $html .= Html::endTag('div');
                $html .= Html::endTag('li');
            }
        endif;
        $html .= Html::endTag('ul');
        $html .= Html::endTag('div');
        // if ($this->can_edit) {

        $html .= Html::a('<i class="fas fa-plus"></i> Adicionar Anexo', '#', ['class' => 'btn btn-xs', 'data-toggle' => "modal", 'data-target' => '#modal-file-browser']);


        Modal::begin([
            'id' => 'modal-file-browser',
            'header' => 'Adicionar Anexo',
            'toggleButton' => false,
            'size' => Modal::SIZE_DEFAULT
        ]);
        $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'action' => $this->action,
            'method' => 'post'
        ]);

        echo $form->field($model, 'files[]')->fileInput(['multiple' => true, 'accept' => '*']);


        echo Html::submitButton('Salvar', ['class' => 'btn']);

        ActiveForm::end();
        Modal::end();
        // }
        return $html;
    }

    /**
     * Convert bytes to human readable format
     *
     * @param integer bytes Size in bytes to convert
     * @return string
     */
    protected static function bytesToSize($bytes, $precision = 2)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            return $bytes . ' B';
        } elseif (($bytes >= $kilobyte) && ($bytes <
            $megabyte)) {
            return round($bytes / $kilobyte, $precision) . ' KB';
        } elseif (($bytes >= $megabyte) && ($bytes <
            $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MB';
        } elseif (($bytes >= $gigabyte) &&
            ($bytes < $terabyte)
        ) {
            return round($bytes / $gigabyte, $precision) . ' GB';
        } elseif ($bytes >= $terabyte) {
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }

    protected static function fileExtension(string $text)
    {
        return pathinfo($text, PATHINFO_EXTENSION);
    }
}
