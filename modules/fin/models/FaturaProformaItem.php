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
class FaturaProformaItem extends \yii\db\ActiveRecord
{
    const ITEM_ORIGEM_M = 'M';
    const ITEM_ORIGEM_X = 'X';
    const ITEM_ORIGEM_D = 'D';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fin_fatura_proforma_item';
    }

    public $item_row;

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dsp_fatura_proforma_id', 'dsp_item_id', 'valor'], 'required'],
            [['dsp_fatura_proforma_id', 'dsp_item_id', 'item_row'], 'integer'],
            [['item_origem_id'], 'string', 'max' => 1],
            ['item_origem_id', 'default', 'value' => self::ITEM_ORIGEM_M],
            ['item_origem_id', 'in', 'range' => [self::ITEM_ORIGEM_M, self::ITEM_ORIGEM_X, self::ITEM_ORIGEM_D]],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dsp_fatura_proforma_id' => 'Fatura Provisória',
            'dsp_item_id' => 'Item',
            'valor' => 'Valor',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaProvisoria()
    {
        return $this->hasOne(FaturaProvisoria::className(), ['id' => 'dsp_fatura_proforma_id']);
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
    public static function totalItem($provider, $fieldName)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total = $total + $item[$fieldName];
        }
        return $total;
    }
}
