<?php

namespace app\modules\dsp\models;

use Yii;

/**
 * This is the model class for table "Origem".
 *
 * @property int $id
 * @property string $descricao
 */
class Origem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_origem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','id'], 'required'],
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
            'descricao' => 'Origem',
        ];
    }

    
}
