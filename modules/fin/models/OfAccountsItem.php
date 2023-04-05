<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class OfAccountsItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_of_accounts_item';
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
            [['fin_of_account_id','valor'], 'required'],
            [['fin_of_account_id','fin_despesa_id','fin_receita_id','fin_nota_debito_id'], 'integer'],
            [['valor'], 'double'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fin_of_account_id'=>'Encontro de Conta',
            'fin_despesa_id'=>'Despesa',
            'fin_receita_id'=>'Receita',
            'valor'=>'Valor',
            'fin_nota_debito_id'=>'Nota de Debito'
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
     public function getReceita()
     {
        return $this->hasOne(Receita::className(), ['id' => 'fin_receita_id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfAccounts()
    {
       return $this->hasOne(OfAccounts::className(), ['id' => 'fin_of_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotaDebito()
    {
       return $this->hasOne(NotaDebito::className(), ['id' => 'fin_nota_debito_id']);
    }

    


    
   

    



}
