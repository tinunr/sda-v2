<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;


class FaturaDefinitivaProvisoria extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_fatura_definitiva_provisoria';
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

        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fin_fatura_definitiva_id','fin_fatura_provisoria_id'], 'required'],
            [['fin_fatura_definitiva_id','fin_fatura_provisoria_id'], 'integer'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
           'fin_fatura_definitiva_id' => 'Fatura definitiva',
           'fin_fatura_provisoria_id'=>'Fatura provisória',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaProvisoria()
    {
       return $this->hasOne(FaturaProvisoria::className(), ['id' => 'fin_fatura_provisoria_id']);
    }

    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDefinitiva()
    {
       return $this->hasOne(FaturaDefinitiva::className(), ['id' => 'fin_fatura_definitiva_id']);
    }


}
