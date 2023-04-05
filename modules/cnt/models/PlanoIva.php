<?php

namespace app\modules\cnt\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "Zona".
 *

 */
class PlanoIva extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_plano_iva';
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
            [['descricao','cnt_tipologia_id'], 'required'],
            [['cnt_tipologia_id','cnt_plano_conta_id'], 'integer'],
            [['taxa','deducao'], 'double','max'=>1, 'min'=>0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Plano de IVA',
            'cnt_tipologia_id' => 'Tipologia',
            'deducao' => 'Dedução',
            'taxa' => 'Taxa',
            'cnt_plano_conta_id'=>'Conta',
         ];
    }


     
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipologia()
    {
        return $this->hasOne(Tipologia::className(), ['id' => 'cnt_tipologia_id']);
    }

    
}
