<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class BancoDocumentoPagamento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_banco_documento_pagamento';
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
            [['fin_banco_id','fin_documento_pagamento_id'], 'required'],
            [['fin_banco_id','fin_documento_pagamento_id'], 'integer'],   
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fin_banco_id' => 'Banco',
            'fin_documento_pagamento_id'=>'Forma de Pagamento'
        ];
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanco()
    {
       return $this->hasOne(Banco::className(), ['id' => 'fin_banco_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoPagamento()
    {
       return $this->hasOne(DocumentoPagamento::className(), ['id' => 'fin_documento_pagamento_id']);
    }



}
