<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class BancoTransacao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_banco_transacao';
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
            [['fin_banco_transacao_tipo_id','fin_banco_id','fin_banco_conta_id','fin_documento_pagamento_id','descricao','saldo'], 'required'],
            [['fin_banco_transacao_tipo_id','fin_banco_id','fin_banco_conta_id','fin_documento_pagamento_id','numero_documento'], 'integer'],            
            [['descricao'], 'string', 'max' => 405],
            [['valor','valor_saida'], 'double'],
            [['fin_banco_id','fin_banco_conta_id'], 'default', 'value' => 1],

            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fin_banco_transacao_tipo_id'=>'Tipo tansação',
            'descricao' => 'Banco Transação',
            'fin_banco_id' => 'Banco',
            'valor' => 'Valor de Entrada',
            'valor_saida' => 'Valor de Saida',
            'fin_documento_pagamento_id'=>'Documento'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoTransacaoTipo()
    {
       return $this->hasOne(BancoTransacaoTipo::className(), ['id' => 'fin_banco_transacao_tipo_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoConta()
    {
       return $this->hasOne(BancoConta::className(), ['id' => 'fin_banco_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoPagamento()
    {
       return $this->hasOne(DocumentoPagamento::className(), ['id' => 'fin_documento_pagamento_id']);
    }



}
