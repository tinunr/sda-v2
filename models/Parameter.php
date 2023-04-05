<?php

namespace app\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "Origem".
 *
 * @property int $id
 * @property string $descricao
 */
class Parameter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bas_parameter';
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
            [['descricao','parameter','isvaluechangeable','value'], 'required'],
            [['isvaluechangeable'], 'integer'],
            [['descricao'], 'string', 'max' => 405],
            [['bas_module_id','type'], 'string', 'max' => 64],
            ['value','safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descrição',
            'resource' => 'Recurso',
            'bas_module_id' => 'Modulo',
            'status' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getValue($bas_module_id,$parameter)
    {
        if (($model = Parameter::find()->where(['parameter'=>$parameter,'bas_module_id'=>$bas_module_id])->One()) !== null) {
            return $model->value;
        }
        throw new NotFoundHttpException('O parametro ['.$bas_module_id.' - '.$parameter.'] requisitado não existe.');
        
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
       return $this->hasOne(Module::className(), ['id' => 'bas_module_id']);
    }

    
}
