<?php

namespace app\modules\fin\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "candidatura_docente".
 *
 * @property integer $id
 * @property integer $perfil_id
 * @property string $desciplina
 */
class FaturaDefinitivaItem extends \yii\db\ActiveRecord
{
   const ITEM_ORIGEM_M = 'M';
   const ITEM_ORIGEM_X = 'X';
   const ITEM_ORIGEM_D = 'D';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fin_fatura_definitiva_item';
    }

    public $item_row;

     /**
    * @inheritdoc
    */
    public function behaviors()
    {
    return [
        'timestamp' => [
                'class' => TimestampBehavior::className(),
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fin_fatura_definitiva_id', 'dsp_item_id'], 'required'],
            [['fin_fatura_definitiva_id', 'dsp_item_id'], 'integer'],
            [['valor'], 'double'],
            ['item_origem_id', 'default', 'value' => self::ITEM_ORIGEM_M],
            ['item_origem_id', 'in', 'range' => [self::ITEM_ORIGEM_M, self::ITEM_ORIGEM_X, self::ITEM_ORIGEM_D]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fin_fatura_definitiva_id' => 'Fatura Definitiva',
            'dsp_item_id'=>'Item', 
            'valor'=>'Valor',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDefinitiva()
    {
       return $this->hasOne(FaturaDefinitiva::className(), ['id' => 'fin_fatura_definitiva_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
       return $this->hasOne(\app\modules\dsp\models\Item::className(), ['id' => 'dsp_item_id']);
    }

   
}
