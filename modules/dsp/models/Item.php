<?php

namespace app\modules\dsp\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "Item".
 *
 * @property int $id
 * @property string $descricao
 */
class Item extends \yii\db\ActiveRecord
{
    const AVISO_CREDITO = 1067;
    const NOTA_CREDITO = 1068;
    const ADIANTAMENTO = 1066;
    const HONORARIO = 1002;
    const IVA_HONORARIO = 1003;

    const VALOR_ZERRO = 0;
    const VALOR_UM = 1;
    conSt DESCRICAO =[
        Item::VALOR_ZERRO => 'Não',
        Item::VALOR_UM => 'Sim',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_item';
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
            [['descricao','dsp_item_type_id','cnt_plano_conta_id','protocolo_processo'], 'required'],
            [['descricao'], 'string', 'max' => 405],
            [['dsp_person_id','id','dsp_item_type_id','cnt_plano_conta_id','cnt_plano_iva_id','cnt_plano_fluxo_caixa_id','protocolo_processo'], 'integer'],
            ['id', 'unique', 'targetClass' => '\app\modules\dsp\models\Item', 'message' => 'ID deve ser unico'],
            ['dsp_person_id', 'required', 'when' => function ($model) {return $model->dsp_item_type_id ==1;},'whenClient' => "function (attribute, value) { return $('#dsp_item_type_id').val() == 1; }"],
            ['id', 'required', 'when' => function ($model) {return $model->dsp_item_type_id ==1;},'whenClient' => "function (attribute, value) { return $('#dsp_item_type_id').val() == 1; }"],

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
            'dsp_person_id' => 'Beneficiário',
            'dsp_item_type_id' => 'Tipo de Item',
            'cnt_plano_iva_id'=>'Plano de IVA',
            'cnt_plano_conta_id'=>'Plano de Conta',
            'cnt_plano_fluxo_caixa_id'=>'Plano Fluxo de Caixa',
            'protocolo_processo'=>'Ativo Protocolo Processo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemTipo()
    {
        return $this->hasOne(ItemTipo::className(), ['id' => 'dsp_item_type_id']);
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
     public function getPlanoConta()
     {
         return $this->hasOne(\app\modules\cnt\models\PlanoConta::className(), ['id' => 'cnt_plano_conta_id']);
     }
 
     /**
      * @return \yii\db\ActiveQuery
      */
     public function getPlanoIva()
     {
         return $this->hasOne(\app\modules\cnt\models\PlanoIva::className(), ['id' => 'cnt_plano_iva_id']);
     }

     /**
      * @return \yii\db\ActiveQuery
      */
     public function getPlanoFluxoCaixa()
     {
         return $this->hasOne(\app\modules\cnt\models\PlanoFluxoCaixa::className(), ['id' => 'cnt_plano_fluxo_caixa_id']);
     }
}
