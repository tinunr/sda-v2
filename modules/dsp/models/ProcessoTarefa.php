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
class ProcessoTarefa extends \yii\db\ActiveRecord
{
        
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_processo_tarefa';
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
            [['dsp_processo_id','dsp_tarefa_id'], 'required'],
            [['dsp_processo_id','dsp_tarefa_id','status'], 'integer'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dsp_processo_id' => 'Processo',
            'dsp_tarefa_id' =>'Tarefa'  ,
         ];
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
    public function getTarefa()
    {
       return $this->hasOne(Tarefa::className(), ['id' => 'dsp_rarefa_id']);
    }
    

   


    
}
