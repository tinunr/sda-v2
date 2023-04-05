<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\DespesaItemBehavior;
use app\modules\dsp\models\Item;
use app\modules\cnt\models\PlanoConta;


class DespesaItem extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_despesa_item';
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
            'despesaitembehavior' => [
                'class' => DespesaItemBehavior::className(),
            ],

        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id','valor','fin_despesa_id'], 'required'],
            [['item_id','dsp_fatura_provisoria_id','cnt_plano_terceiro_id'], 'integer'],
            [['item_descricao'], 'string', 'max' => 405],
            [['valor','valor_iva'], 'double'],
            [
                'cnt_plano_terceiro_id', 'required',
                'when' => function ($model) {
                    if (!empty($model->despesa->fin_despesa_tipo_id)) {
                        if ($model->despesa->fin_despesa_tipo_id==2) {
                            $item = Item::findOne($model->item_id);
                            if ($item->cnt_plano_conta_id > 0) {
                                $planoConta = PlanoConta::findOne($item->cnt_plano_conta_id);
                                // print_r($model->despesa->fin_despesa_tipo_id);die();
                                if (!empty($planoConta->tem_plano_externo)) {
                                    return true;
                                }
                            }
                        }
                    }
                    return false;
                },
            ],
            [
                'valor_iva', 'required', 
                'when' => function ($model) {
                    if (!empty($model->despesa->fin_despesa_tipo_id)) {
                        if ($model->despesa->fin_despesa_tipo_id==2) {
                            $item = Item::findOne($model->item_id);
                            return (!empty($item->cnt_plano_iva_id))?true:false;
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
           'dsp_fatura_provisoria_id'=>'Fatura provisória',
           'valor'=>'Valor',
           'status'=>'Estado',
           'as_deleted'=>'Eliminado',
           'valor_iva'=>'IVA',
           'item_descricao'=>'Descrição',
           'cnt_plano_terceiro_id'=>'Terceiro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaProvisoria()
    {
       return $this->hasOne(FaturaProvisoria::className(), ['id' => 'dsp_fatura_provisoria_id']);
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
    public function getItem()
    {
       return $this->hasOne(\app\modules\dsp\models\Item::className(), ['id' => 'item_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
       return $this->hasOne(\app\modules\dsp\models\Person::className(), ['id' => 'cnt_plano_terceiro_id']);
    }


    

    



}
