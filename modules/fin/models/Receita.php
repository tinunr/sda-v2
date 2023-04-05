<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\ReceitaBehavior;

class Receita extends \yii\db\ActiveRecord
{
    public $selecionado;
    const FATURA_PROVISORIA = 1;
    const FATURA_DEFINITIVA = 2;

    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_receita';
    }
    /**
    * @inheritdoc
    */
    public function behaviors()
    {
    return [
        'timestamp' => [
                'class' =>TimestampBehavior::className(),
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
            'receitabehavior' => [
                'class' => ReceitaBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_person_id','valor','valor_recebido','saldo','fin_receita_tipo_id'], 'required'],
            [['dsp_fataura_provisoria_id','dsp_person_id','id','status','fin_fataura_definitiva_id','fin_receita_tipo_id'], 'integer'],
            [['valor','valor_recebido','saldo'],'double', 'min'=>0],
            [['descricao'],'string', 'max'=>999],
            ['fin_receita_tipo_id', 'default', 'value' => self::FATURA_PROVISORIA],
            ['fin_receita_tipo_id', 'in', 'range' => [self::FATURA_PROVISORIA, self::FATURA_DEFINITIVA]],

            // ['dsp_fataura_provisoria_id', 'required', 'when' => function ($model) {
            //     return $model->fin_receita_tipo_id == 1;
            //  }, 'whenClient' => "function (attribute, value) { return $('#fin_receita_tipo_id').val()==1; }"],
            // ['fin_fataura_definitiva_id', 'required', 'when' => function ($model) {
            //     return $model->fin_receita_tipo_id == 2;
            //  }, 'whenClient' => "function (attribute, value) { return $('#fin_receita_tipo_id').val() ==2; }"]            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dsp_fataura_provisoria_id' => 'Fatura Provisória',
            'dsp_person_id'=>'Cliente',
            'descricao'=>'Descrição',
            'valor'=>'Valor',
            'valor_recebido'=>'Valor Recebido',
            'saldo'=>'Saldo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaProvisoria()
    {
       return $this->hasOne(FaturaProvisoria::className(), ['id' => 'dsp_fataura_provisoria_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDefinitiva()
    {
       return $this->hasOne(FaturaDefinitiva::className(), ['id' => 'fin_fataura_definitiva_id']);
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
     public function getRecebimentoItem()
     {
        return $this->hasMany(RecebimentoItem::className(), ['fin_receita_id' => 'id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getOfAccounts()
     {
        return $this->hasMany(OfAccounts::className(), ['fin_receita_id' => 'id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getOfAccountsItem()
     {
        return $this->hasMany(OfAccountsItem::className(), ['fin_receita_id' => 'id']);
     }
   


}
