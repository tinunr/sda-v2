<?php

namespace app\modules\cnt\models;

use Yii;

/**
 * This is the model class for table "Diario".
 *
 * @property int $id
 * @property string $descricao
 */
class Diario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_diario';
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
            'id' => 'Código',
            'descricao' => 'Diário',
        ];
    }

    
}
