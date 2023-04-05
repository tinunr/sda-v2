<?php

namespace app\modules\dsp\models;

use Yii;

/**
 * This is the model class for table "tipologias".
 *
 * @property int $id
 * @property string $descricao
 */
class RegimeItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_regime_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_regime_id','descricao','forma','dsp_item_unidade'], 'required'],
            [['dsp_item_unidade','dsp_regime_parent_id'], 'integer'],
            [['descricao'], 'string', 'max' => 405],
            [['valor'], 'double'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Designação',
            'dsp_regime_id' => 'Regime',
            'forma' => 'Forma',
            'dsp_regime_parent_id'=>'Tabela Anexa',
            'dsp_item_unidade'=>'Unidade',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegimeItemItem()
    {
       return $this->hasMany(RegimeItemItem::className(), ['dsp_regime_item_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegimeItem()
    {
       return $this->hasOne(RegimeItem::className(), ['id' => 'dsp_regime_parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegimeUnidade()
    {
       return $this->hasOne(Unidade::className(), ['id' => 'dsp_item_unidade']);
    }
}
