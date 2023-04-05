<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\DespesaBehavior;
use app\modules\fin\models\DespesaTipo;
use yii\helpers\Html;

class Despesa extends \yii\db\ActiveRecord
{

   public $selecionado;
   const STATUS_ATIVO = 1;
   const STATUS_ANULADO = 0;
   const IS_LOCK = 1;
   const IS_UNLOCK = 0;

   const STATUS_TEXTO = [
      self::STATUS_ANULADO => 'Anulado',
      self::STATUS_ATIVO => 'Ativado',
      self::IS_LOCK => 'Desbloqueado',
      self::IS_UNLOCK => 'Bloqueado',
   ];


   /**
    * {@inheritdoc}
    */
   public static function tableName()
   {
      return 'fin_despesa';
   }

   /**
    * @inheritdoc
    */
   public function behaviors()
   {
      return [
         'timestamp' => [
            'class' => TimestampBehavior::className(),
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
         'despesa' => [
            'class' => DespesaBehavior::className(),
         ],

      ];
   }
   /**
    * {@inheritdoc}
    */
   public function rules()
   {
      return [
         [['dsp_person_id', 'valor', 'valor_pago', 'saldo', 'recebido'], 'required'],
         [['dsp_processo_id', 'dsp_fatura_provisoria_id', 'dsp_person_id', 'status', 'as_deleted', 'id', 'id', 'bas_ano_id', 'recebido', 'fin_recebimento_id', 'fin_nota_credito_id', 'fin_aviso_credito_id', 'of_acount', 'cnt_documento_id', 'fin_despesa_tipo_id', 'fin_of_account_id'], 'integer'],
         // ['numero', 'unique', 'message' => 'Este numero já existe.'],
         [['descricao'], 'string', 'max' => 405],
         [['numero'], 'safe'],
         // [['valor'],'double', 'min'=>0.1],
         [['valor'],'double', 'min'=>0],
         [['valor', 'valor_pago', 'saldo'], 'double', 'min' => 0],
         ['saldo', 'compare', 'compareValue' => 0, 'operator' => '>=', 'enableClientValidation' => false],
         ['valor_pago', 'compare', 'compareAttribute' => 'valor', 'operator' => '<=', 'enableClientValidation' => false],
         [['data_vencimento', 'data'], 'safe'],
         ['dsp_processo_id', 'required', 'when' => function ($model) {
            return $model->numero == null && $model->fin_nota_credito_id == null;
         }],
         // ['fin_nota_credito_id', 'required', 'when' => function ($model) {return $model->fin_despesa_tipo_id ==DespesaTipo::AGENCIA;}],
         ['cnt_documento_id', 'required', 'when' => function ($model) {
            return $model->fin_despesa_tipo_id == DespesaTipo::AGENCIA;
         }, 'whenClient' => "function (attribute, value) { return $('#fin_despesa_tipo_id').val() == 2; }"],

         // ['numero', 'required', 'when' => function ($model) {return $model->dsp_processo_id ==null;}],


         // adiciona 'dsp_person_id' como unico solicitação JMC
         [['numero', 'bas_ano_id', 'status', 'dsp_person_id'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id', 'status', 'dsp_person_id']],

         // ['numero', 'unique', 'on'=>'create','targetClass' => '\app\modules\fin\models\Despesa','message' => 'Já existe uma despesa com este Numero'], 
         ['fin_despesa_tipo_id', 'default', 'value' => DespesaTipo::CLIENTE],
         ['saldo', 'default', 'value' => 0],
         ['is_lock', 'default', 'value' => self::IS_UNLOCK],
         ['is_lock', 'in', 'range' => [self::IS_UNLOCK, self::IS_LOCK]],



      ];
   }

   /**
    * {@inheritdoc}
    */
   public function attributeLabels()
   {
      return [
         'id' => 'ID',
         'dsp_processo_id' => 'Processo',
         'dsp_fatura_provisoria_id' => 'Fatura Provisória',
         'dsp_person_id' => 'Fornecedor',
         'valor' => 'Valor',
         'valor_pago' => 'Valor Pago',
         'saldo' => 'Saldo',
         'status' => 'Estado',
         'numero' => 'Número',
         'descricao' => 'Descrição',
         'as_deleted' => 'Eliminado',
         'recebido' => 'Recebido',
         'data' => 'Data',
         'data_vencimento' => 'Data de Vencimento',
         'fin_aviso_credito_id' => 'Nota de Crediro',
         'cnt_documento_id' => 'Documento',
      ];
   }



/**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero, ['/fin/despesa/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
    }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getPerson()
   {
      return $this->hasOne(\app\modules\dsp\models\Person::className(), ['id' => 'dsp_person_id']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getDespesaItem()
   {
      return $this->hasMany(DespesaItem::className(), ['fin_despesa_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getFaturaProvisoria()
   {
      return $this->hasOne(FaturaProvisoria::className(), ['id' => 'dsp_fatura_provisoria_id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getRecebimento()
   {
      return $this->hasOne(Recebimento::className(), ['id' => 'fin_recebimento_id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcesso()
   {
      return $this->hasOne(\app\modules\dsp\models\Processo::className(), ['id' => 'dsp_processo_id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getNotaCredito()
   {
      return $this->hasOne(NotaCredito::className(), ['id' => 'fin_nota_credito_id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getOfAccounts()
   {
      return $this->hasMany(OfAccounts::className(), ['fin_despesa_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getOfAccountsItem()
   {
      return $this->hasMany(OfAccountsItem::className(), ['fin_despesa_id' => 'id']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getPagamentoItem()
   {
      return $this->hasMany(PagamentoItem::className(), ['fin_despesa_id' => 'id']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getPagamentoOrdemItem()
   {
      return $this->hasMany(PagamentoOrdemItem::className(), ['fin_despesa_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getDocumento()
   {
      return $this->hasOne(\app\modules\cnt\models\Documento::className(), ['id' => 'cnt_documento_id']);
   }
}
