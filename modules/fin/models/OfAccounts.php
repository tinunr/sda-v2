<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;

class OfAccounts extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_of_accounts';
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
            [['dsp_person_id','valor','numero'], 'required'],
            [['dsp_person_id','status','bas_ano_id','numero','fin_receita_id','fin_despesa_id'], 'integer'],
            [['data'],'safe'],
            // [['valor'],'double'],
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
           'numero'=>'Número',
           'fin_receita_id'=>'Receita',
           'fin_despesa_id'=>'Despesa',
           'dsp_person_id'=>'Cliente',
           'valor'=>'Valor',
           'status'=>'Estado',
           'descricao'=>'Descrição',
        ];
    }


    
  /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero . '/' . $this->bas_ano_id, ['/fin/of-accounts/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
       return $this->hasOne(\app\modules\dsp\models\Person::className(), ['id' => 'dsp_person_id']);
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
    public function getItem()
    {
       return $this->hasMany(OfAccountsItem::className(), ['fin_of_account_id' => 'id']);
    }

    



}
