<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class BancoConta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_banco_conta';
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
            [['numero','fin_banco_id','saldo','descoberta','status','cnt_diario_id'], 'required'],
            [['fin_banco_id','status','cnt_diario_id','cnt_plano_conta_id','cnt_plano_fluxo_caixa_id'], 'integer'],          
            [['saldo'], 'double'],            
            [['descoberta'], 'double','min'=>0],
            [['numero','color'],'string', 'max' => 62]


            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fin_banco_id' => 'Meio Financeiro',
            'saldo' => 'Saldo',
            'numero' => 'Nº de Conta',
            'descoberta' => 'Descoberta',
            'status'=>'Estado',
            'cnt_diario_id'=>'Diário',
            'cnt_plano_conta_id'=>'Conta',
            'cnt_plano_fluxo_caixa_id'=>'Fluxo de caixa',
            'color'=>'Cor',
        ];
    }


     /**
     * @return \yii\db\ActiveQuery
     */
     public function getBanco()
     {
        return $this->hasOne(Banco::className(), ['id' => 'fin_banco_id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getDiario()
     {
        return $this->hasOne(\app\modules\cnt\models\Diario::className(), ['id' => 'cnt_diario_id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getPlanoConta()
     {
        return $this->hasOne(\app\modules\cnt\models\PlanoConta::className(), ['id' => 'cnt_plano_conta_id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getPlanoFluxoCaixa()
     {
        return $this->hasOne(\app\modules\cnt\models\PlanoFluxoCaixa::className(), ['id' => 'cnt_plano_fluxo_caixa_id']);
     }

    /**
     * Ataulizar saldo de uma conta
     * @var int $fin_banco_conta_id
     * @var float $new_saldo
     * @return \yii\db\ActiveQuery [BancoConta] | false
     */
    public static function atualizarSaldo($fin_banco_conta_id, $new_saldo)
    {  
        if(($model = BancoConta::findOne(['id'=>$fin_banco_conta_id]))!=null){
            $model->saldo = $new_saldo;
            return $model->save(false);
        }
        return false;
    }



}
