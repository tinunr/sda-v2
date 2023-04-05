<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\CaixaBehavior;

class Caixa extends \yii\db\ActiveRecord
{
    const OPEN_CAIXA = 1;
    const CLOSE_CAIXA = 2;
    const STATUS_TEXTO = [
        self::OPEN_CAIXA =>'Aberto',
        self::CLOSE_CAIXA =>'Fechado',
    ]; 
       
    /**>
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_caixa';
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
            'tansacao'=>[
                'class'=>CaixaBehavior::className(),
            ]

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','user_open_id','fin_banco_conta_id'], 'required'],
            [['status','user_open_id','user_close_id','fin_banco_conta_id'], 'integer'],
            [['saldo_inicial','saldo_fecho'],'double'],
            [['descricao'],'string', 'max'=>450],
            [['data'],'date', 'format'=>'yyyy-mm-dd'],
            [['data_abertura','data_fecho'],'safe'],


            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'saldo_inicial' => 'Saldo Inicial',
            'saldo_fecho'=>'Saldo Fecho',
            'descricao'=>'Descrição',
            'user_open_id'=>'Aberto Por',
            'user_colse_id'=>'fechado Por',
            'fin_banco_conta_id'=>'Conta Bancária',
            'data_abertura'=>'Data de Abertura',
            'data_fecho'=>'Data de Fecho',
            'status'=>'Estado',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaixaTransacao()
    {
       return $this->hasMany(CaixaTransacao::className(), ['fin_caixa_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserOpen()
    {
       return $this->hasOne(\app\models\User::className(), ['id' => 'user_open_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaixaOperacao()
    {
       return $this->hasMany(\app\models\User::className(), ['fin_caixa_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoConta()
    {
       return $this->hasOne(BancoConta::className(), ['id' => 'fin_banco_conta_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserClose()
    {
       return $this->hasOne(\app\models\User::className(), ['id' => 'user_colse_id']);
    }

    
   


}
