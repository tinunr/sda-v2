<?php

namespace app\modules\dsp\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use backend\models\User;
use app\modules\dsp\behaviors\ProcessoWorkflowBehavior;

/**
 * This is the model class for table "candidatura_docente".
 *
 * @property integer $id
 * @property integer $perfil_id
 * @property string $desciplina
 */
class ProcessoWorkflow extends \yii\db\ActiveRecord
{
    const SCENARIO_INIT = 'init';
    const SCENARIO_RECEBER_BOX = 'receber-box';
    const SCENARIO_ENVIAR_BOX = 'enviar-box';
    const SCENARIO_CREATE = 'create';
    // const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dsp_processo_workflow';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at', 'data_inicio'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'workflow' => [
                'class' => ProcessoWorkflowBehavior::className(),
            ],


        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dsp_processo_id', 'data_inicio', 'status', 'dsp_setor_id'], 'required'],
            [['dsp_processo_id', 'user_id', 'dsp_setor_id', 'classificacao', 'in_workflow_id', 'out_workflow_id', 'prioridade'], 'integer'],
            ['descricao', 'string', 'max' => 405],

            [['in_data_hora', 'out_data_hora', 'recebeu_data_hora', 'data_inicio', 'data_fim'], 'safe'],
            ['user_id', 'required', 'when' => function ($model) {
                return empty($model->dsp_setor_id);
            }],
            ['dsp_setor_id', 'required', 'when' => function ($model) {
                return empty($model->user_id);
            }],
            ['in_workflow_id', 'unique', 'targetAttribute' => 'in_workflow_id', 'on' => self::SCENARIO_CREATE],
            ['out_workflow_id', 'unique', 'targetAttribute' => 'out_workflow_id', 'on' => self::SCENARIO_CREATE],
            // ['user_id', 'required', 'on' => self::SCENARIO_INIT],
            // ['dsp_setor_id', 'required', 'on' => self::SCENARIO_RECEBER_BOX ],
            // [['user_id','dsp_setor_id'], 'required', 'on' => self::SCENARIO_ENVIAR_BOX ], 
			['data_inicio', 'default', 'value' => new Expression('NOW()')],

        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dsp_processo_id' => 'Processo',
            'classificacao' => 'Classificação',
            'de_user_id' => 'Origem',
            'de_dsp_setor_id' => 'Setor Origem',
            'para_user_id' => 'Destino',
            'para_dsp_setor_id' => 'Setor Destino',
            'in_data_hora' => 'Data Envio',
            'recebeu_data_hora' => 'Data Receção',
            'recebeu_data_hora' => 'Data Inicio',
            'recebeu_data_hora' => 'Data Fim',
            'descricao' => 'Mensagem',
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
    public function getUser()
    {
        return $this->hasone(\app\models\User::className(), ['id' => 'user_id']);
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetor()
    {
        return $this->hasOne(Setor::className(), ['id' => 'dsp_setor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkflowStatus()
    {
        return $this->hasOne(ProcessoWorkflowStatus::className(), ['id' => 'status']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInWorkflow()
    {
        return $this->hasOne(ProcessoWorkflow::className(), ['id' => 'in_workflow_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutWorkflow()
    {
        return $this->hasOne(ProcessoWorkflow::className(), ['id' => 'out_workflow_id']);
    }
}
