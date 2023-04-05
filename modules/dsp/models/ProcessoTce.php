<?php
namespace app\modules\dsp\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;
/**
 * This is the model class for table "Zona".
 *

 */
class ProcessoTce extends \yii\db\ActiveRecord
{
        
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_processo_tce';
    }

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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_processo_id','tce'], 'required'],
            [['dsp_processo_id'], 'integer'],
            [['tce'], 'string', 'max' => 405],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dsp_processo_id' => 'Processo',
            'tce' =>'TCE'  ,
         ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
       return $this->hasOne(Processo::className(), ['id' => 'dsp_processo_id']);
    }

    

   


    
}
