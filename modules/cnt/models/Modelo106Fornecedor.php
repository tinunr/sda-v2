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
class Modelo106Fornecedor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_modelo_106_fornecedor';
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
            [['cnt_modelo_106_id','origem','designacao','tp_doc','num_doc','data','vl_fatura','vl_base_incid','tx_iva','iva_sup','direito_ded','tipologia','linha_dest_mod','iva_ded'], 'required'],
            [['cnt_modelo_106_id','nif','linha_dest_mod'], 'integer'],
            [['vl_fatura','vl_base_incid','tx_iva','iva_sup','direito_ded','iva_ded'], 'double'],
            [['origem','designacao','tp_doc','num_doc'], 'string'],
            [['date'], 'safe'],
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
            $total+=$item[$fieldName];
        }
        return $total;
    }




}
