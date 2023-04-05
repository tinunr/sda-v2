<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "concelhos".
 *

 */
class Documento extends \yii\db\ActiveRecord
{
    const PROCESSO_ID = 3;
    const FATURA_PROVISORIA_ID = 1;
    const FATURA_DEFINITIVA_ID = 2;
    const NORD_ID = 4;
    const NORD_ID_A = 5;
    const RECEBIMENTO_ID = 6;
    const PAGAMENTO_ID = 7;
    const AVISO_CREDITO = 8;
    const DESPESA = 9;
    const OFACCOUNTS_ID = 10;
    const NOTA_CREDITO_ID = 11;
    const NOTA_DEBITO = 12;
    const PAGAMENTO_ORDEM_ID = 13;

    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bas_documento';
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
            [['descricao'], 'required'],
            [['descricao'], 'string', 'max' => 405],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Documento',
        ];
    }
}
