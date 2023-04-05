<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class PagamentoItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_pagamento_item';
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
            [['fin_pagamento_id','fin_despesa_id','valor'], 'required'],
            [['fin_pagamento_id','fin_despesa_id','id'], 'integer'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fin_pagamento_id'=>'Pagamento',
            'fin_despesa_id'=>'Despesa',
            'valor'=>'Valor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespesa()
    {
       return $this->hasOne(Despesa::className(), ['id' => 'fin_despesa_id']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagamento()
    {
       return $this->hasOne(Pagamento::className(), ['id' => 'fin_pagamento_id']);
    }

    


    
   

    



}
