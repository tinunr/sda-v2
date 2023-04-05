<?php

namespace app\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "concelhos".
 *

 */
class DocumentoNumero extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bas_documento_numero';
    }

     /**
     * {@inheritdoc}
     */
    public static function primaryKey(){
        return ['bas_documento_id','ano'];
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
            [['bas_documento_id','numero','ano'], 'required'],
            [['bas_documento_id','numero','ano'],'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bas_documento_id' => 'Documento',
            'numero' => 'Ultimo Numero',
            'ano'=>'Ano'
        ];
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByDocumentId($bas_documento_id, $data = null)
    {
        $ano =!empty($data)?substr(date('Y', strtotime($data)),-2):substr(date('Y'),-2);
        if(($model = static::findOne(['bas_documento_id' => $bas_documento_id, 'ano'=>$ano])) !== null){
            return $model;
        }else{
            $model = new DocumentoNumero();
            $model->ano = substr(date('Y'),-2);
            $model->bas_documento_id = $bas_documento_id;
            $model->numero = 0;
            $model->save();
            return $model;
            
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getNexNumber()
    {
        return $this->numero+1;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function saveNexNumber()
    {
//$mmodel = DocumentoNumero::findOne(['bas_documento_id'=>$this->bas_documento_id, 'ano'=>substr(date('Y'),-2)]);   
            $this->numero = $this->numero +1;
            $this->save();

        return $this;
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumento()
    {
       return $this->hasOne(Documento::className(), ['id' => 'bas_documento_id']);
    }

}
