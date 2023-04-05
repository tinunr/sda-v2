<?php

namespace app\modules\dsp\models;

use Yii;


class Unidade extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_regime_unidade';
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
            'descricao' => 'Unidade',
        ];
    }



}
