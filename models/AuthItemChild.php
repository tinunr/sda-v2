<?php

namespace app\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "AuthItemChild".
 *
 * @property integer $id
 * @property string $nome
 * @property string $departamento_id
 */
class AuthItemChild extends \yii\db\ActiveRecord
{   
   
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item_child';
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
            [['parent','child'], 'required'],
            [['parent','child'], 'string', 'max' => 124],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent' => 'Item Pai',
            'child' => 'Item Filho',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtthItemParent()
    {
        return $this->hasOne(AtthItem::className(), ['name' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtthItemChild()
    {
        return $this->hasOne(AtthItem::className(), ['name' => 'child']);
    }

}
