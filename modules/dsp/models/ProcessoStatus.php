<?php

namespace app\modules\dsp\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;
/**
 * This is the model class for table "Zona".
 *

 */
class ProcessoStatus extends \yii\db\ActiveRecord
{
    const NAO_ATRIBUIDO = 1;
    const EM_EXECUCAO = 2;
    const PENDENTE = 3;
    const REGISTRADO = 4;
    const LIQUIDADO = 5;
    const PAGO = 6;
    const RECEBIDO = 7;
    const RECEITADO = 8;
    const CONCLUIDO = 9;
    const PARCEALMENTE_CONCLUIDO = 10;
    const STATUS_ANULADO = 11;
    // Estados Financeiro
    const FIN_SEM_FATURA_PROVISORIA = 12;
    const FIN_PAGO_SEM_FATURA_PROVISORIA = 13;
    const FIN_FATURA_PROVISORIA_EMETIDO = 14;
    const FIN_RECEBIDO = 15;
    const FIN_PAGO_COM_FATURA_PROVISORIA = 16;
    const FIN_FATURA_DEFINITIVA = 17;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_processo_status';
    }

    /**
     * {@inheritdoc}
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
            [['descricao'], 'required'],
            [['dsp_processo_status_tipo_id'], 'integer'],
            [['descricao'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' =>'Estado'  ,
         ];
    }

   


    
}
