<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\RecebimentoBehavior;
use yii\helpers\Html;

class Recebimento extends \yii\db\ActiveRecord
{
   const REEMBOLCO_AFAVOR_CLIENTE = "AFAVOR_CLIENTE"; 
   const REEMBOLCO_AFAVOR_AGENCIA = "AFAVOR_AGENCIA"; 
   
   public $banco;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_recebimento';
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
            'caixatransacao' => [
                'class' => RecebimentoBehavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_person_id','data','fin_documento_pagamento_id','numero','valor'], 'required'],
            [['numero','fin_banco_id','fin_documento_pagamento_id','dsp_person_id','fin_receita_id','dsp_fatura_provisoria_id','fin_banco_conta_id','fin_recebimento_tipo_id','dsp_processo_id'], 'integer'],
            [['descricao','numero_documento','tipo_reembolco'], 'string', 'max' => 405],
            ['fin_banco_id', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==2;}, 'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 2; }"],
            ['fin_banco_id', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==3;}, 'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 3; }"],
            ['fin_banco_conta_id', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==3;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 3; }"],
            ['data_documento','safe'],
            ['data_documento', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==2;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 2; }"],
            ['data_documento', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==3;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() ==3; }"],
            ['numero_documento', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==2;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() ==2; }"],
            ['numero_documento', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==3;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() ==3; }"],
            [['fin_banco_id','fin_banco_conta_id'], 'default', 'value' => 1],
            // ['numero', 'unique', 'message' => 'Este numero já existe.'],
            [['numero', 'bas_ano_id'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id']],

            ['dsp_processo_id', 'required', 'when' => function ($model) {return $model->fin_recebimento_tipo_id ==4;},'whenClient' => "function (attribute, value) { return $('#fin_recebimento_tipo_id').val() ==4; }"],


            //  [['fin_banco_id','fin_banco_conta_id','data_documento'], 'validateFormaPagamento'],
             ['tipo_reembolco', 'default','value'=>self::REEMBOLCO_AFAVOR_CLIENTE],

            
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'numero' => 'Nnúmero',
            'fin_banco_id'=>'Caixa / Banco',
            'fin_banco_conta_id'=>'Nº de Conta',
            'fin_documento_pagamento_id'=>'Documento',
            'dsp_person_id'=>'Cliente',
            'numero_documento'=>'Nº de documento',
            'fin_receita_id'=>'Receita',
            'dsp_fatura_provisoria_id'=>'FP',
            'descricao'=>'Observação',
            'data_documento'=>'Data Documento',
        ];
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero . '/' . $this->bas_ano_id, ['/fin/recebimento/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecebimentoItem()
    {
       return $this->hasMany(RecebimentoItem::className(), ['fin_recebimento_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceita()
    {
       return $this->hasMany(Receita::className(), ['fin_receita_id' => 'id']);
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
    public function getBancoConta()
    {
       return $this->hasOne(BancoConta::className(), ['id' => 'fin_banco_conta_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancos()
    {
       return $this->hasOne(Banco::className(), ['id' => 'fin_banco_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoPagamento()
    {
       return $this->hasOne(DocumentoPagamento::className(), ['id' => 'fin_documento_pagamento_id']);
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
    public function getDespesa()
    {
       return $this->hasMany(Despesa::className(), ['fin_recebimento_id' => 'id']);
    }
     /**
     * @return \yii\db\ActiveQuery
     */
     public function getRecebimentoTipo()
     {
        return $this->hasOne(RecebimentoTipo::className(), ['id' => 'fin_recebimento_tipo_id']);
     }


     /**
     * @return \yii\db\ActiveQuery
     */
     public function getProcesso()
     {
        return $this->hasOne(\app\modules\dsp\models\Processo::className(), ['id' => 'dsp_processo_id']);
     }


    




}
