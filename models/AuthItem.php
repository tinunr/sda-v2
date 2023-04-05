<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "AuthItem".
 *
 * @property integer $id
 * @property string $nome
 * @property string $departamento_id
 */
class AuthItem extends \yii\db\ActiveRecord
{   
   
    public  $list;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
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
            [['name','type','description'], 'required'],
            [['type'], 'integer'],
            [['list'], 'safe'],
            [['name','description'], 'string', 'max' => 405],
            ['name', 'unique', 'targetClass' => '\app\models\AuthItem', 'message' => 'O Item já existe.'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Item',
            'type' => 'Tipo',
            'description' => 'Descrição',
            'data' => 'Data',
            'rule_name' => 'Rule Data',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtthItemParent()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtthItemChild()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }
}
