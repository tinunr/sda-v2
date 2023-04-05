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
class ProcessoStatusFinanceiro extends \yii\db\ActiveRecord
{
        
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_processo_status_financeiro';
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
            [['dsp_processo_id','dsp_processo_status_id'], 'required'],
            [['dsp_processo_id','dsp_processo_status_id','user_id','dsp_setor_id'], 'integer'],
            [['descricao'], 'string', 'max' => 405],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dsp_processo_id' => 'Processo',
            'dsp_processo_status_id' =>'Estado'  ,
         ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessoStatus()
    {
       return $this->hasone(ProcessoStatus::className(), ['id' => 'dsp_processo_status_id']);
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
       return $this->hasone(\app\models\User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetor()
    {
       return $this->hasone(Setor::className(), ['id' => 'dsp_setor_id']);
    }

   


    
}
