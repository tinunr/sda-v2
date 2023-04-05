<?php

namespace app\modules\dsp\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "Zona".
 *

 */
class ProcessoDespachoDocumento extends \yii\db\ActiveRecord
{
    public $file;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_processo_despacho_documento';
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
            [['dsp_processo_id','dsp_despacho_documento_id','descricao'], 'required'],
            [['dsp_processo_id','bas_ficheiro_id','dsp_despacho_documento_id'], 'integer'],
            [['file'], 'file', 'extensions' => 'png,jpg,pdf,doc,docx,xsl,xlsx,zip,rar'],
            [['descricao',], 'string', 'max' => 405],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dsp_processo_id' => 'Processo',
            'bas_ficheiro_id' => 'Ficheiros',
            'dsp_despacho_documento_id'=>'Documento despecho'
         ];
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcesso()
    {
       return $this->hasone(Processo::className(), ['id' => 'dsp_processo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespachoDocumento()
    {
       return $this->hasone(DespachoDocumento::className(), ['id' => 'dsp_despacho_documento_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFicheiro()
    {
       return $this->hasone(\app\models\Ficheiro::className(), ['id' => 'bas_ficheiro_id']);
    }


    
}