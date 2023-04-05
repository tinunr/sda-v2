<?php

namespace app\modules\cnt\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\cnt\behaviors\RazaoItemBehavior;
use app\modules\cnt\models\Natureza;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\cnt\models\PlanoIva;
use app\modules\dsp\models\Person;

/**
 * This is the model class for table "Diario".
 *
 * @property int $id
 * @property string $descricao
 */
class RazaoItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_razao_item';
    }

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
            'razaoitem' => [
                'class' => RazaoItemBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cnt_razao_id','valor','cnt_natureza_id','cnt_plano_conta_id','descricao'], 'required'],
            [['cnt_razao_id','cnt_plano_conta_id','cnt_plano_terceiro_id','cnt_plano_fluxo_caixa_id','cnt_plano_iva_id','dsp_item_id'], 'integer'],
            [['valor'],'double'],
            [['cnt_natureza_id'], 'string', 'max' => 1],
            [['descricao','documento_origem_numero','documento_origem_tipo'], 'string', 'max' => 405],
            // type_id needs to exist in the column "id" in the table defined in ProductType class 
            // ['cnt_plano_conta_id', 'exist', 'targetClass' => PlanoConta::class, 'targetAttribute' => ['cnt_plano_conta_id' => 'id']], 
            ['cnt_plano_conta_id', 'exist', 'targetClass' => PlanoConta::class, 'targetAttribute' => ['cnt_plano_conta_id' => 'id'],'filter' => ['!=','cnt_plano_conta_tipo_id',1],'message'=>'A conta selecionada não é uma conta operacional.'], 

            ['cnt_plano_terceiro_id', 'exist', 'targetClass' => Person::class, 'targetAttribute' => ['cnt_plano_terceiro_id' => 'id']], 
            ['cnt_plano_fluxo_caixa_id', 'exist', 'targetClass' => PlanoFluxoCaixa::class, 'targetAttribute' => ['cnt_plano_fluxo_caixa_id' => 'id']], 
            ['cnt_plano_iva_id', 'exist', 'targetClass' => PlanoIva::class, 'targetAttribute' => ['cnt_plano_iva_id' => 'id']], 
            ['cnt_natureza_id', 'exist', 'targetClass' => Natureza::class, 'targetAttribute' => ['cnt_natureza_id' => 'id']], 
            [
                'cnt_plano_terceiro_id', 'required', 
                'when' => function ($model) {
                    return !empty($model->cnt_plano_conta_id)?PlanoConta::findOne($model->cnt_plano_conta_id)->tem_plano_externo:false;
                },
            ],
            [
                'cnt_plano_fluxo_caixa_id', 'required', 
                'when' => function ($model) {
                    return !empty($model->cnt_plano_conta_id)?PlanoConta::findOne($model->cnt_plano_conta_id)->tem_plano_fluxo_caixa:false;
                },
            ],
            [
                'cnt_plano_iva_id', 'required', 
                'when' => function ($model) {
                    return !empty($model->cnt_plano_conta_id)?PlanoConta::findOne($model->cnt_plano_conta_id)->is_plano_conta_iva:false;
                },
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'cnt_razao_id'=>'Razão',
            'cnt_natureza_id'=>'C/D',
            'cnt_plano_conta_id'=>'Conta',
            'cnt_plano_terceiro_id'=>'Terceiro',
            'cnt_plano_fluxo_caixa_id'=>'Caixa',
            'cnt_plano_iva_id'=>'IVA',
            'descricao'=>'Descrição',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRazao()
    {
       return $this->hasOne(Razao::className(), ['id' => 'cnt_razao_id']);
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
    public function getPlanoConta()
    {
       return $this->hasOne(PlanoConta::className(), ['id' => 'cnt_plano_conta_id']);
    }

    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanoTerceiro()
    {
       return $this->hasOne(PlanoTerceiro::className(), ['id' => 'cnt_plano_terceiro_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanoFluxoCaixa()
    {
       return $this->hasOne(PlanoFluxoCaixa::className(), ['id' => 'cnt_plano_fluxo_caixa_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanoIva()
    {
       return $this->hasOne(PlanoIva::className(), ['id' => 'cnt_plano_iva_id']);
    }

    
}
