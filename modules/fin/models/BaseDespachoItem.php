<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class BaseDespachoItem extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_base_despacho_item';
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
            [['fin_base_despacho_id','valor','item_descricao','dsp_item_id'], 'required'],
            [['fin_base_despacho_id','dsp_item_id','valor'], 'integer'],
            [['item_descricao'], 'string', 'max' => 405],
            [['valor'],'double', 'min'=>0],
            //[['data'],'date'],

            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
           'id' => 'ID',
           'fin_base_despacho_id'=>'Base Despacho',
           'dsp_item_id'=>'Fornecedor',
           'valor'=>'Valor',
           'status'=>'Estado',
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
