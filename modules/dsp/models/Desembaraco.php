<?php

namespace app\modules\dsp\models;

use Yii;

/**
 * This is the model class for table "tipologias".
 *
 * @property int $id
 * @property string $descricao
 */
class Desembaraco extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_desembaraco';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code','descricao'], 'required'],
            [['code','descricao'], 'string', 'max' => 405],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Codigo',
            'descricao' => 'Estancia',
        ];
    }
}
