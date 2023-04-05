<?php

namespace app\modules\dsp\models;

use Yii;

/**
 * This is the model class for table "tipologias".
 *
 * @property int $id
 * @property string $descricao
 */
class RegimeItemItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_regime_item_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_regime_item_id','descricao','forma','condicao','valor_produto','valor','dsp_item_unidade'], 'required'],
            [['descricao','forma','condicao'], 'string', 'max' => 405],
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
            'dsp_regime_item_id' => 'Regime',
            'forma' => 'Forma',
            'condicao' => 'Condição',
            'dsp_item_unidade'=>'Unidade',
            'valor'=>'Valor',
            'valor_produto'=>'Valor do produto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegimeUnidade()
    {
       return $this->hasOne(Unidade::className(), ['id' => 'dsp_item_unidade']);
    }
}
