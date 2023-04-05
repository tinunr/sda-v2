<?php

namespace app\modules\cnt\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\cnt\behaviors\PlanoContaBehavior;

/**
 * This is the model class for table "PlanoConta".
 *
 * @property int $id
 * @property string $descricao
 */
class PlanoConta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_plano_conta';
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
            'planoconta' => [
                'class' => PlanoContaBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','id','tem_plano_externo','cnt_plano_conta_tipo_id','codigo','tem_plano_fluxo_caixa','is_plano_conta_iva','cnt_natureza_id'], 'required'],
            [['descricao','codigo','path'], 'string', 'max' => 405],
            [['codigo','tem_plano_externo','tem_plano_fluxo_caixa','cnt_plano_conta_tipo_id','cnt_plano_conta_id','is_plano_conta_iva','cnt_plano_conta_abertura','cnt_plano_conta_fecho'], 'integer'],
            ['id', 'unique', 'targetClass' => '\app\modules\cnt\models\PlanoConta', 'message' => 'Código deve ser unico'],
            [['cnt_natureza_id'], 'string','max'=>'1'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Conta',
            'tem_plano_externo' => 'Tem P.Ext.',
            'cnt_natureza_id' => 'Natureza',
            'tem_plano_fluxo_caixa' => 'Leva F. Caixa',
            'cnt_plano_conta_tipo_id'=>'Tipo',
            'cnt_plano_conta_id'=>'Pai',
            'is_plano_conta_iva'=>'Conta de IVA',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::className(), ['id' => 'dsp_person_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanoContaPai()
    {
        return $this->hasOne(PlanConta::className(), ['id' => 'cnt_plano_conta_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNatureza()
    {
        return $this->hasOne(Natureza::className(), ['id' => 'cnt_natureza_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanoContaTipo()
    {
        return $this->hasOne(PlanoContaTipo::className(), ['id' => 'cnt_plano_conta_tipo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAbertura()
    {
        return $this->hasOne(PlanoConta::className(), ['id' => 'cnt_plano_conta_abertura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFecho()
    {
        return $this->hasOne(PlanoConta::className(), ['id' => 'cnt_plano_conta_fecho']);
    }



}
