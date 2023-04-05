<?php

namespace app\modules\dsp\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\dsp\behaviors\NordBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * This is the model class for table "Zona".
 *

 */
class Nord extends \yii\db\ActiveRecord
{
    public $xmlFile;
    
    const STATUS_ATIVO = 1;
    const STATUS_ANULADO= 0;

    const STATUS_TEXTO = [
        self::STATUS_ANULADO => 'Anulado',
        self::STATUS_ATIVO => 'Ativado',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_nord';
    }

    /**
    * @inheritdoc
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
            'Noord'=>[
                'class'=>NordBehavior::className(),
            ],

        ];

    }

    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','dsp_desembaraco_id','numero','dsp_processo_id'], 'required'],
            [['dsp_desembaraco_id','dsp_processo_id','bas_ano_id'], 'integer'],
            [['id','numero'], 'safe'],
            [['dsp_processo_id'], 'unique', 'targetClass' => '\app\modules\dsp\models\Nord', 'message' => 'Já existe um NORD para este processo.'],
            [['id'], 'unique', 'targetClass' => '\app\modules\dsp\models\Nord', 'message' => 'Já existe um NORD com este numero.'],
            [['numero', 'bas_ano_id','dsp_desembaraco_id'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id','dsp_desembaraco_id']],
            ['status', 'default', 'value' => self::STATUS_ATIVO],
            ['status', 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_ANULADO]],

            [['xmlFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xml'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'NORD',
            'numero' => 'NORD',
            'dsp_processo_id' => 'Processo',
            'bas_ano_id' => 'Ano', 
            'dsp_desembaraco_id' =>'Estância' ,
         ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero.'/'.$this->desembaraco->code.'/'.$this->bas_ano_id, ['/dsp/nord/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function upload()
    {
        $path = 'nords/' . $this->id. '.' . $this->xmlFile->extension;
        if ($this->validate()) {
            \yii\helpers\FileHelper::unlink($path);
            $this->xmlFile->saveAs($path);
            return true;
        } else {
            return false;
        }
    }

     
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getDesembaraco()
    {
        return $this->hasOne(Desembaraco::className(), ['id' => 'dsp_desembaraco_id']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
        return $this->hasOne(Processo::className(), ['id' => 'dsp_processo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespacho()
    {
        return $this->hasOne(Despacho::className(), ['bas_ano_id' => 'bas_ano_id','nord' => 'numero','dsp_desembaraco_id'=>'dsp_desembaraco_id']);
    }

    
}
