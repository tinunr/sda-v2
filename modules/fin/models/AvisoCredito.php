<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\AvisoCreditoBehavior;

class AvisoCredito extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_aviso_credito';
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
            'Avisocreditobehavior' => [
                'class' => AvisoCreditoBehavior::className(),
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
            [['dsp_person_id','fin_fatura_defenitiva_id','dsp_processo_id'], 'integer'],
            [['descricao'], 'string', 'max' => 405],
            ['bas_ano_id', 'default', 'value' => 18],
            // ['numero', 'unique', 'message' => 'Este numero já existe.'],

            
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
       return $this->hasOne(Despesa::className(), ['fin_aviso_credito_id' => 'id']);
    }



}
