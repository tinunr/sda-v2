<?php

namespace app\modules\dsp\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "Zona".
 */
class ProcessoDespesaManual extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_processo_despesa_manual';
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_processo_id', 'dsp_item_id', 'valor'], 'required'],
            [['dsp_processo_id', 'dsp_item_id'], 'integer'],
            [['valor'], 'double'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dsp_processo_id' => 'Processo',
            'dsp_item_id' => 'Item',
            'valor' => 'Valor'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
        return $this->hasone(Processo::className(), ['id' => 'dsp_processo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasone(Item::className(), ['id' => 'dsp_item_id']);
    }
}