<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipologias".
 *
 * @property int $id
 * @property string $descricao
 */
class Island extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bas_island';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ano','id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ano' => 'Ano',
        ];
    }
}
