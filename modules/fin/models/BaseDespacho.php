<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class BaseDespacho extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_base_despacho';
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
            [['dsp_processo_id','dsp_fatura_provisoria_id','dsp_person_id','valor'], 'required'],
            [['dsp_fatura_provisoria_id','dsp_processo_id','dsp_person_id','status','as_deleted','recebido'], 'integer'],
            [['valor'],'double', 'min'=>0],
            [['data'],'safe'],

            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
           'id' => 'ID',
           'dsp_processo_id'=>'Processo',
           'dsp_fatura_provisoria_id'=>'Fatura Provisória',
           'dsp_person_id'=>'Fornecedor',
           'valor'=>'Valor',
           'status'=>'Estado',
           'as_deleted'=>'Eliminado',
           'data'=>'Data',
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
    public function getDespesaItem()
    {
       return $this->hasMany(DespesaItem::className(), ['fin_despesa_id' => 'id']);
    }

    



}
