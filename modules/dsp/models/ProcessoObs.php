<?php

namespace app\modules\dsp\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "Zona".
 *

 */
class ProcessoObs extends \yii\db\ActiveRecord
{
    public $file;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_processo_obs';
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
            [['dsp_processo_id','obs'], 'required'],
            [['dsp_processo_id','status'], 'integer'],
            [['obs'], 'string','max'=>'405'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dsp_processo_id' => 'Processo',
            'status' => 'Estado',
         ];
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
       return $this->hasone(Processo::className(), ['id' => 'dsp_processo_id']);
    }


     /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreated()
    {
       return $this->hasone(\app\models\User::className(), ['id' => 'created_by']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdated()
    {
       return $this->hasone(\app\models\User::className(), ['id' => 'updated_by']);
    }


    

    
}
