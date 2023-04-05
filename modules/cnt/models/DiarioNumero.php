<?php

namespace app\modules\cnt\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "concelhos".
 *

 */
class DiarioNumero extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_diario_numero';
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
            [['cnt_diario_id','numero','ano','mes'], 'required'],
            [['cnt_diario_id','numero','ano','mes'],'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cnt_diario_id' => 'Diário',
            'numero' => 'Numero',
            'ano'=>'Ano'
        ];
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByDiarioId($cnt_diario_id,$bas_ano_id, $bas_mes_id)
    {
        $diario = Diario::findOne(['id'=>$cnt_diario_id]);
        $bas_ano = \app\models\Ano::findOne($bas_ano_id)->ano;
        if(($model = static::findOne(['cnt_diario_id' => $cnt_diario_id, 'ano'=>$bas_ano,'mes'=>$bas_mes_id])) !== null){
            return $model;
        }else{
            $model = new DiarioNumero();
            $model->ano = $bas_ano;
            $model->mes = $bas_mes_id;
            $model->cnt_diario_id = $cnt_diario_id;
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
        return $this->numero + 1;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function saveNexNumber()
    {  
        $this->numero = $this->numero +1;
        $this->save();
        return $this;
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiario()
    {
       return $this->hasOne(Diario::className(), ['id' => 'cnt_diario_id']);
    }

}
