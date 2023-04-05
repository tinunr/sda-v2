<?php

namespace app\modules\cnt\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "Modelo106".
 *
 * @property int $id
 * @property string $descricao
 */
class Modelo106Cliente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_modelo_106_cliente';
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
            [['cnt_modelo_106_id','data','vl_fatura','vl_base_incid','tx_iva','iva_liq','linha_dest_mod'], 'required'],
            [['cnt_modelo_106_id','nif'], 'integer'],
            [['vl_fatura','vl_base_incid','tx_iva','iva_liq','nao_liq_imp','linha_dest_mod'], 'double'],
            [['designacao','origem','num_doc'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cnt_modelo_106_id' => 'ID',
            'origem' => 'Origem',
            'tp_doc' => 'Tip. Doc',
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelo106()
    {
        return $this->hasOne(Modelo106::className(), ['id' => 'cnt_modelo_106_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function totalItem($provider, $fieldName)
    {
        $total=0;
        foreach($provider as $item){
            $total= $total +$item[$fieldName];
        }
        return $total;
    }



}
