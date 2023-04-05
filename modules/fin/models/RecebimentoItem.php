<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\RecebimentoItemBehavior;
use app\modules\cnt\models\PlanoConta;
use app\modules\dsp\models\Item;

class RecebimentoItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_recebimento_item';
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
            'recebimentoitem' => [
                'class' => RecebimentoItemBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fin_recebimento_id','descricao_item','saldo','valor','valor_recebido'], 'required'],
            [['fin_recebimento_id','id','fin_receita_id','dsp_item_id','dsp_person_id'], 'integer'],
            [['descricao_item'], 'string', 'max' => 405],
            [
                'dsp_person_id', 'required',
                'when' => function ($model) {
                    if (!empty($model->dsp_item_id)) {
                        $item = Item::findOne($model->dsp_item_id);
                        if ($item->cnt_plano_conta_id > 0) {
                            $planoConta = PlanoConta::findOne($item->cnt_plano_conta_id);
                            if (!empty($planoConta->tem_plano_externo)) {
                                return true;
                            }
                        }
                    }
                    return false;
                },
            ],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fin_recebimento_id'=>'Recebimento',
            'fin_recebimento_recibo_id'=>'Recibo',
            'saldo'=>'Saldo',
            'valor'=>'Valor',
            'valor_recebido'=>'Valor Recebido',
        ];
    }

    
   
    

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecebimento()
    {
       return $this->hasOne(Recebimento::className(), ['id' => 'fin_recebimento_id']);
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
        return $this->hasOne(\app\modules\dsp\models\Item::className(), ['id' => 'dsp_item_id']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getPerson()
     {
        return $this->hasOne(\app\modules\dsp\models\Person::className(), ['id' => 'dsp_person_id']);
     }

   



}
