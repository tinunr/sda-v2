<?php

namespace app\modules\dsp\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;


class FaturaClassificada extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_fatura_classificada';
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
            [['descricao','nomeclatura','quantidade','peso_liquido','peso_bruto','valor','dsp_processo_id'], 'required'],
            [['quantidade'], 'integer'],
            [['peso_liquido','peso_bruto','valor'], 'double'],
            [['descricao','nomeclatura'], 'string', 'max' => 405],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' =>'Designação de Mercadoria',
            'nomeclatura'=>'Nomeclatura',
            'quantidade'=>'Qtd. Complemetar',
            'peso_liquido'=>'Peso Líquido',
            'peso_bruto'=>'Peso Bruto',
            'valor'=>'Valor',
        ];
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
        return $this->hasOne(Processo::className(), ['id' => 'dsp_processo_id']);
    }



}