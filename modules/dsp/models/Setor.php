<?php

namespace app\modules\dsp\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Setor extends \yii\db\ActiveRecord
{   
    const ARQUIVO_ID = 10;
    const ARQUIVO_PROVISORIO_ID = 11;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_setor';
    }

    /**
    * @inheritdoc
    */
    public function behaviors()
    {
    return [
        'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            ],
            'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
                ],

        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sigla','descricao','caixa'], 'required'],
            [['sigla'], 'string', 'max' => 4],
            [['caixa','arquivo'], 'integer'],
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
           'sigla'=>'Sigla',
           'descricao'=>'Setor',
        ];
    }
 
    /**
    * @return \yii\db\ActiveQuery
     */
     public function getSetorUser()
     {
        return $this->hasMany(SetorUser::className(), ['dsp_setor_id' => 'id']);
     }


}
