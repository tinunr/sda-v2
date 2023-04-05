<?php 
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\components\helpers\UploadFileHelper;

class Ficheiro extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bas_ficheiro';
    }
    public $file;
    public $name;
    public $dsp_processo_id;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
    return [
        'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            ],
            'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
                ],

        ];

    }

    /**
     * @var UploadedFile
     */

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png,jpg,pdf,xsl,xlsx,docx'],
            [['file_pacth','file_name','file_extenction'], 'string', 'max' => 405],
            [['name','dsp_processo_id'], 'safe'],

        ];
    }

    public function upload()
    {
        
        if ($this->validate()) {
            
           if ($this->file) {
                $this->file_name = $this->file->name;
                $this->file_extenction = $this->file->extension;
                if($this->save()){
                    $this->file_pacth = UploadFileHelper::setUrlFile($this->dsp_processo_id).$this->name.'.'.$this->file->extension;
                    $this->save();
                    
                    $this->file->saveAs(UploadFileHelper::setUrlFile($this->dsp_processo_id).$this->name.'.'.$this->file->extension);
                    #Yii::$app->getSession()->setFlash('success', 'Fichero anexado com sucesso');
                    return $this->id;
                }
                
            }

        } else {
            return false;
        }
    }
}


?>