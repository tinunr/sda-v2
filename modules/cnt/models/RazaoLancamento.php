<?php

namespace app\modules\cnt\models;

use Yii;

/**
 * This is the model class for table "RazaoLancamento".
 *
 * @property int $id
 * @property string $descricao
 */
class RazaoLancamento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_razao_lancamento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','ano'], 'required'],
            [['descricao'], 'string','max'=>405],
            [['ano'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'descricao' => 'Ano',
        ];
    }

    
}