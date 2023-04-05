<?php

namespace app\modules\cnt\models;

use Yii;

/**
 * This is the model class for table "Tipologia".
 *
 * @property int $id
 * @property string $descricao
 */
class Tipologia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_tipologia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','numero_destino','abreviatura'], 'required'],
            [['descricao','numero_destino','numero_destino_b','abreviatura'], 'string', 'max' => 405],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Tipologia',
            'numero_destino'=>'Nº de destino',
            'numero_destino_b'=>'Nº de destino B',
        ];
    }

    
}
