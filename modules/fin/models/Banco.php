<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Banco extends \yii\db\ActiveRecord
{
    public $fin_documento_pagamento_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_banco';
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
            [['descricao','sigla'], 'required'],
            [['sigla'], 'string', 'max' => 5],
            [['descricao'], 'string', 'max' => 405],
             ['fin_documento_pagamento_id', 'each', 'rule' => ['integer']],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Meio Financeiro',
            'sigla' => 'Sigla',
            'fin_documento_pagamento_id'=>'Forma de Pagar / Recber'
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoDocumentoPagamento()
    {
       return $this->hasMany(BancoDocumentoPagamento::className(), ['fin_banco_id' => 'id']);
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoConta()
    {
       return $this->hasMany(BancoConta::className(), ['fin_banco_id' => 'id']);
    }



}
