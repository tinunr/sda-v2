<?php

namespace app\modules\cnt\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\cnt\behaviors\RazaoBehavior;
use app\modules\cnt\models\Documento;
use yii\helpers\Url;

/**
 * This is the model class for table "Diario".
 *
 * @property int $id
 * @property string $descricao
 */
class Razao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_razao';
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
            'razao' => [
                'class' => RazaoBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cnt_diario_id','data','valor_debito','valor_credito'], 'required'],
            [['descricao'], 'string', 'max' => 7000],
            [['documento_origem_numero'], 'string', 'max' => 405],
            [['cnt_documento_id','documento_origem_id','cnt_diario_id','bas_ano_id','numero','bas_mes_id','status','validacao_debito_credito','operacao_fecho'],'integer'],
            [['data','documento_origem_data','validacao_debito_credito'],'safe'],
            [['valor_debito','valor_debito'],'double'],
            [['valor_debito','valor_debito'], 'default', 'value' => 0],

            [['cnt_diario_id','numero','bas_mes_id','bas_ano_id'], 'unique', 'targetAttribute' => ['cnt_diario_id','numero','bas_mes_id', 'bas_ano_id']],
            ['valor_debito','compare','compareAttribute'=>'valor_credito'],
            ['valor_credito','compare','compareAttribute'=>'valor_debito'],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'descricao' => 'Descrição',
            'cnt_documento_id' => 'Documento',
            'cnt_diario_id' => 'Diário',
            'documento_origem_id'=>'Origem',
            'data'=>'Data Lançamento',
            'documento_origem_data'=>'Data do Documento',
            'valor_debito'=>'Valor Debito',
            'valor_credito'=>'Valor Credito',
            'numero'=>'Numero',
            'documento_origem_numero'=>'Num Doc',
            'validacao_debito_credito'=>'Validação',
            'bas_mes_id'=>'Mês',
            'bas_ano_id'=>'Ano',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumento()
    {
       return $this->hasOne(Documento::className(), ['id' => 'cnt_documento_id']);
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
    public function getRazaoItem()
    {
       return $this->hasMany(RazaoItem::className(), ['cnt_razao_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMes()
    {
       return $this->hasOne(\app\models\Mes::className(), ['id' => 'bas_mes_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAno()
    {
       return $this->hasOne(\app\models\Ano::className(), ['id' => 'bas_ano_id']);
    }

    public function origemUrl()
  {
    $url = ''; 
      if ($this->cnt_documento_id == Documento::FATURA_DEFINITIVA) {
        $url = Url::to(['/fin/fatura-definitiva/view', 'id' => $this->documento_origem_id]);
      }
      if (in_array($this->cnt_documento_id, [Documento::RECEBIMENTO_FATURA_PROVISORIA, Documento::RECEBIMENTO_ADIANTAMENTO, Documento::RECEBIMENTO_REEMBOLSO, Documento::RECEBIMENTO_TESOURARIO])) {
        $url = Url::to(['/fin/recebimento/view', 'id' => $this->documento_origem_id]);
      }
      if ($this->cnt_documento_id == Documento::PAGAMENTO) {
        $url = Url::to(['/fin/pagamento/view', 'id' => $this->documento_origem_id]);
      }
      if ($this->cnt_documento_id == Documento::MOVIMENTO_INTERNO) {
        $url = Url::to(['/fin/transferencia/view', 'id' => $this->documento_origem_id]);
      }
      if ($this->cnt_documento_id == Documento::MOVIMENTO_INTERNO) {
        $url = Url::to(['/fin/transferencia/view', 'id' => $this->documento_origem_id]);
      }
      if (in_array($this->cnt_documento_id, [Documento::DESPESA_FATURA_FORNECEDOR, Documento::FATURA_FORNECEDOR_INVESTIMENTO])) {
        $url = Url::to(['/fin/despesa/view', 'id' => $this->documento_origem_id]);
      }
      if (in_array($this->cnt_documento_id, [Documento::FACTURA])) {
        $url = Url::to(['/fin/fatura-eletronica/view', 'id' => $this->documento_origem_id]);
      }
      if (in_array($this->cnt_documento_id, [Documento::NOTA_DE_DEBITO_CLIENTE])) {
        $url = Url::to(['/fin/fatura-debito-cliente/view', 'id' => $this->documento_origem_id]);
      } 
    return $url;
  }
    
}
