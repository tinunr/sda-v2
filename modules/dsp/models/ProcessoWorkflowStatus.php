<?php

namespace app\modules\dsp\models;

use Yii;


class ProcessoWorkflowStatus extends \yii\db\ActiveRecord
{
    const PEND_RECEBIMENTO = 1;
    const RECEBIDO = 2;
    const ENVIADO = 3;
    const AGUR_RECEBIMENTO = 4;
    const ARQUIVADO = 5;
    const DESARQUIVADO = 6;
    const DISPONIVEL_SETOR = 7;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_processo_workflow_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'descricao'], 'required'],
            [['id'], 'integer'],
            [['descricao'], 'string', 'max' => 405],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Estado',
        ];
    }
}
