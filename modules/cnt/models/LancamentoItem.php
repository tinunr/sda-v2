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
class LancamentoItem extends \yii\db\ActiveRecord
{

    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_lancamento_item';
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
            [['cnt_lancamento_id','origem_id','destino_id'], 'required'],            
            [['cnt_lancamento_id'], 'integer'],            
            [['origem_id','destino_id'], 'string','max'=>'405'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cnt_lancamento_id' => 'Lançamento',
            'origem_id'=>'Origem',
            'destino_id'=>'Destino',
         ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
     public function getLancamento()
     {
         return $this->hasOne(Lancamento::className(), ['id' => 'cnt_lancamento_id']);
     }


     /**
     * @return \yii\db\ActiveQuery
     */
     public function getOrigem()
     {
         return $this->hasOne(PlanoConta::className(), ['id' => 'origem_id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getDestino()
     {
         return $this->hasOne(PlanoConta::className(), ['id' => 'destino_id']);
     }

    
}
