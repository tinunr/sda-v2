<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class FileBrowser extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;
    public $path;

    public function rules()
    {
        return [
            [['files'], 
            'file', 'skipOnEmpty' => false, 'extensions' => 'png,jpg,pdf,doc,docx,xsl,xlsx,zip,rar', 'maxFiles' =>10 ,'maxSize' => 51200000, 'tooBig' => 'Limit is 50000KB'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            foreach ($this->files as $file) {
                $file->saveAs($this->path . $file->baseName . '.' . $file->extension);
            }
            return true;
        } else {
            return false;
        }
    }
}