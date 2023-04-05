<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Denominacaos".
 *
 * @property int $id
 * @property string $descricao
 */
class Denominacao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'denominacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao'], 'required'],
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
            'descricao' => 'Denominacao',
        ];
    }
}
