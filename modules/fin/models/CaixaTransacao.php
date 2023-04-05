<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\models\CaixaOperacao;
use app\modules\fin\behaviors\CaixaTransacaoBehavior;

class CaixaTransacao extends \yii\db\ActiveRecord
{
    CONST STATUS_UNCKECKED = 1;
    CONST STATUS_CKECKED = 2;
    CONST TEXTO = [
        self::STATUS_CKECKED => 'Verificado',
        self::STATUS_UNCKECKED => 'Por Verificar',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_caixa_transacao';
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
            'caixatransacaobehavior'=>[
                'class'=>CaixaTransacaoBehavior::className(),
            ]

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','fin_caixa_id','fin_caixa_operacao_id','saldo'], 'required'],
            [['fin_caixa_id','fin_caixa_operacao_id','fin_recebimento_id','fin_pagamento_id','fin_documento_pagamento_id','fin_transferencia_id'], 'integer'],
            [['descricao','numero_documento'], 'string', 'max' => 405],
            [['valor_entrada','valor_saida','saldo'], 'double'],
            ['status', 'default', 'value' => self::STATUS_UNCKECKED],
            ['status', 'in', 'range' => [self::STATUS_UNCKECKED, self::STATUS_CKECKED]],
            ['valor_entrada', 'required', 'when' => function ($model) {return $model->valor_saida ==NULL;}],
            ['valor_saida', 'required', 'when' => function ($model) {return $model->valor_entrada ==NULL;}],
            ['fin_recebimento_id', 'required', 'when' => function ($model) {$model->fin_caixa_operacao_id==CaixaOperacao::RECEBIMENTO;}],
            ['fin_pagamento_id', 'required', 'when' => function ($model) {$model->fin_caixa_operacao_id==CaixaOperacao::PAGAMENTO;}],
            ['fin_transferencia_id', 'required', 'when' => function ($model) {$model->fin_caixa_operacao_id==CaixaOperacao::TRANSFERENCIA;}],
            [['data_documento','data'],'safe'],

            
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
            'numero_documento' => 'Nº Doc.',
            'data_documento' => 'Data Doc.',
            'fin_caixa_id'=>'Caixa',
            'valor_entrada'=>'Entrada',
            'valor_saida'=>'Saida',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaixa()
    {
       return $this->hasOne(Caixa::className(), ['id' => 'fin_caixa_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
     public function getCaixaOperacao()
     {
        return $this->hasOne(CaixaOperacao::className(), ['id' => 'fin_caixa_operacao_id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getDocumentoPagamento()
     {
        return $this->hasOne(DocumentoPagamento::className(), ['id' => 'fin_documento_pagamento_id']);
     }


}
