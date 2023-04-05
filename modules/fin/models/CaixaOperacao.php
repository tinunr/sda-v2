<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class CaixaOperacao extends \yii\db\ActiveRecord
{
    const RECEBIMENTO = 1;
    const PAGAMENTO = 2;
    const TRANSFERENCIA = 3;
    const OPEN_CAIXA = 4;
    const CLOSE_CAIXA = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_caixa_operacao';
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
            [['descricao'], 'required'],
            [['descricao'],'string', 'max'=>450],

            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao'=>'Operação',
        ];
    }

    
   


}
