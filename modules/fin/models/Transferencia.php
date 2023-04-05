<?php

namespace app\modules\fin\models;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\TransferenciaBehavior;

class Transferencia extends \yii\db\ActiveRecord
{
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
        return 'fin_transferencia';
    }
    /**
    * @inheritdoc
    */
    public function behaviors()
    {
    return [
        'timestamp' => [
                'class' => TimestampBehavior::className(),
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
            'transferencia' => [
                'class' => TransferenciaBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','fin_banco_conta_id_origem','fin_banco_conta_id_destino','referencia','valor','data','fin_banco_origem_id','fin_banco_destino_id'], 'required'],
            [['fin_documento_pagamento_id','fin_banco_conta_id_origem','fin_banco_conta_id_destino','fin_banco_origem_id','fin_banco_destino_id','numero','bas_ano_id'], 'integer'],
            [['descricao','referencia'], 'string', 'max' => 405],
            [['valor'], 'double'],
            [['data'],'date', 'format'=>'yyyy-mm-dd'],
            [['fin_banco_conta_id_destino'], 'compare', 'compareAttribute' => 'fin_banco_conta_id_origem','operator'=>'!='], 
            // ['referencia', 'unique','on'=>'create'],
            ['status', 'default', 'value' => self::STATUS_ATIVO],
            ['status', 'in', 'range' => [self::STATUS_ANULADO, self::STATUS_ATIVO]],
            [['numero', 'status'], 'unique', 'targetAttribute' => ['numero', 'status'],'on'=>'create'],


            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descrição',
            'referencia' => 'Referência',
            'fin_banco_origem_id'=>'Banco de Origem',
            'fin_banco_destino_id'=>'Banco do Desrino',
            'fin_documento_pagamento_id'=>'Modalidade de Pagamento',
            'fin_banco_conta_id_origem'=>'Conta Bancária de Origem',
            'fin_banco_conta_id_destino'=>'Conta Bancária do Destino',
            'valor'=>'Valor',
            'data'=>'Data',
            'bas_ano_id'=>'Ano',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoContaOrigem()
    {
       return $this->hasOne(BancoConta::className(), ['id' => 'fin_banco_conta_id_origem']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
     public function getBancoContaDestino()
     {
        return $this->hasOne(BancoConta::className(), ['id' => 'fin_banco_conta_id_destino']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getDocumentoPagamento()
     {
        return $this->hasOne(DocumentoPagamento::className(), ['id' => 'fin_documento_pagamento_id']);
     }

    


}
