<?php

namespace app\modules\dsp\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "candidatura_docente".
 *
 * @property integer $id
 * @property integer $perfil_id
 * @property string $desciplina
 */
class SetorUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dsp_setor_user';
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dsp_setor_id','user_id'], 'required'],
            [['dsp_setor_id','user_id'], 'integer'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Utilizador',
            'dsp_setor_id'=>'Setor',
        ];
    }


    /**
    * @return \yii\db\ActiveQuery
     */
     public function getUser()
     {
        return $this->hasOne(\app\models\User::className(), ['id' => 'user_id']);
     }

     /**
    * @return \yii\db\ActiveQuery
     */
    public function getSetor()
    {
       return $this->hasOne(Setor::className(), ['id' => 'dsp_setor_id']);
    }

    
   
}
