<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\NotaCreditoBehavior;

class NotaCredito extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_nota_credito';
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
            'notacreditobehavior' => [
                'class' => NotaCreditoBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','valor','dsp_person_id'], 'required'],
            [['dsp_person_id','fin_fatura_defenitiva_id','dsp_processo_id','numero'], 'integer'],
            [['descricao'], 'string', 'max' => 405],
            ['bas_ano_id', 'default', 'value' => 18],
            [['numero', 'bas_ano_id','status'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id','status']],
            ['data', 'safe'],

            
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
            'dsp_person_id' => 'Cliente',
            'dsp_processo_id' => 'Processo',
            'fin_fatura_defenitiva_id'=>'Fatura Definitiva',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDefinitiva()
    {
       return $this->hasOne(FaturaDefinitiva::className(), ['id' => 'fin_fatura_defenitiva_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
       return $this->hasOne(\app\modules\dsp\models\Person::className(), ['id' => 'dsp_person_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
       return $this->hasOne(\app\modules\dsp\models\Processo::className(), ['id' => 'dsp_processo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
     public function getDespesa()
     {
        return $this->hasOne(Despesa::className(), ['fin_nota_credito_id' => 'id']);
     }



}
