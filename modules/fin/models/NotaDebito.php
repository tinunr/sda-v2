<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\NotaDebitoBehavior;

class NotaDebito extends \yii\db\ActiveRecord
{
    public $selecionado;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_nota_debito';
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
            'notadebitobehavior' => [
                'class' => NotaDebitoBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','dsp_person_id','data','valor','valor_pago','saldo'], 'required'],
            [['dsp_person_id','numero','bas_ano_id','status','id'], 'integer'],
            [['descricao'], 'string', 'max' => 405],
            [['numero', 'bas_ano_id'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id']],
            ['data', 'safe'],
            [['valor','valor_pago','saldo'], 'safe'],

            
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
            'dsp_person_id' => 'Cliente',
            'data'=>'Data',
            'descricao'=>'Descrição'
        ];
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
    public function getOfAccountsItem()
    {
       return $this->hasMany(OfAccountsItem::className(), ['fin_nota_debito_id' => 'id']);
    }



}
