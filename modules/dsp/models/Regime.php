<?php

namespace app\modules\dsp\models;

use Yii;


class Regime extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_regime';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','descricao'], 'required'],
            [['id'], 'integer'],
            [['descricao'], 'string', 'max' => 405],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Regime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegimeItem()
    {
       return $this->hasMany(RegimeItem::className(), ['dsp_regime_id' => 'id']);
    }



}
