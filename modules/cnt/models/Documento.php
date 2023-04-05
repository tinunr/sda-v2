<?php

namespace app\modules\cnt\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "concelhos".
 *
 */
class Documento extends \yii\db\ActiveRecord
{
    const FATURA_DEFINITIVA = 1;
    const DESPESA_FATURA_FORNECEDOR = 4;
    const FATURA_FORNECEDOR_INVESTIMENTO = 9;
    const FATURA_RECIBO = 10;
    const RECEBIMENTO_FATURA_PROVISORIA = 2;
    const RECEBIMENTO_ADIANTAMENTO = 6;
    const RECEBIMENTO_REEMBOLSO = 7;
    const RECEBIMENTO_TESOURARIO = 11;
    const PAGAMENTO = 3;
    const MOVIMENTO_INTERNO = 5;
    const REEMBOLSO = NULL;
    const FACTURA = 19;
    const NOTA_DE_DEBITO_CLIENTE = 20;

    const VALOR_ZERRO = 0;
    const VALOR_UM = 1;
    conSt DESCRICAO =[
        Documento::VALOR_ZERRO => 'Não',
        Documento::VALOR_UM => 'Sim',
    ];
    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_documento';
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
            [['descricao','cnt_documento_tipo_id','codigo'], 'required'],
            [['descricao','codigo'], 'string', 'max' => 405],
            [['cnt_natureza_id'], 'string', 'max' => 1],
            [['cnt_plano_conta_id','cnt_diario_id','tem_plano_externo','cnt_plano_iva_id','cnt_plano_fluxo_caixa_id','cnt_documento_tipo_id'], 'integer'],
            // ['codigo', 'unique', 'targetClass' => '\app\modules\cnt\models\Documento', 'message' => 'Código deve ser unico'],

           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Documento',
            'cnt_diario_id'=>'Diário',
            'cnt_natureza_id'=>'Natureza',
            'cnt_plano_conta_id'=>'Plano de Conta',
            'cnt_plano_iva_id'=>'Plano de IVA',
            'tem_plano_externo'=>'Tem Plano Externo',
            'cnt_plano_fluxo_caixa_id'=>'Fluxo de Caixa',
            'cnt_documento_tipo_id'=>'Tipo de Documento',
            'codigo'=>'Código',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoTipo()
    {
        return $this->hasOne(DocumentoTipo::className(), ['id' => 'cnt_documento_tipo_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiario()
    {
        return $this->hasOne(Diario::className(), ['id' => 'cnt_diario_id']);
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
    public function getPlanoIva()
    {
        return $this->hasOne(PlanoIva::className(), ['id' => 'cnt_plano_iva_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNatureza()
    {
        return $this->hasOne(Natureza::className(), ['id' => 'cnt_natureza_id']);
    }
}
