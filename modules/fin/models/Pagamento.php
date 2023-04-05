<?php

namespace app\modules\fin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\PagamentoBehavior;
use yii\helpers\Html;

class pagamento extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fin_pagamento';
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
            'pagamentocaixa'=>[
                'class'=>PagamentoBehavior::className(),
            ]

        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_person_id','valor','numero','fin_documento_pagamento_id'], 'required'],
            [['dsp_person_id','status','as_deleted','id','fin_banco_id','fin_documento_pagamento_id','numero_documento','fin_banco_conta_id','fin_pagamento_ordem_id'], 'integer'],
            [['data_documento','data'],'safe'],
            ['fin_banco_id', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==2;}, 'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 2; }"],
            ['fin_banco_id', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==3;}, 'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 3; }"],
            ['fin_banco_conta_id', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==2;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 2; }"],
            ['fin_banco_conta_id', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==3;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 3; }"],
            ['data_documento','safe'],
            ['data_documento', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==2;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() == 2; }"],
            ['data_documento', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==3;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() ==3; }"],
            ['numero_documento', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==2;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() ==2; }"],
            ['numero_documento', 'required', 'when' => function ($model) {return $model->fin_documento_pagamento_id ==3;},'whenClient' => "function (attribute, value) { return $('#fin_documento_pagamento_id').val() ==3; }"],
            ['fin_banco_conta_id', 'validateDescoverta'],
            [['descricao'], 'string', 'max' => 9999],

            // ['numero', 'unique', 'message' => 'Este numero já existe.'],
            [['numero', 'bas_ano_id'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id']],



            
        ];
    }



    public function validateDescoverta($attribute, $params)
    {
        $model = BancoConta::findOne(['id'=>$this->fin_banco_conta_id]);
        $saldo = ($model->saldo - $this->valor)+$model->descoberta;
        if ($saldo < 0) {
            $this->addError('fin_documento_pagamento_id', 'Saldo insuficiente para realizar esta operação');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
           'id' => 'ID',
           'numero'=>'Número',
           'dsp_person_id'=>'Fornecedor',
           'fin_banco_id'=>'Banco',
           'valor'=>'Valor',
           'status'=>'Estado',
           'as_deleted'=>'Eliminado',
           'numero_documento'=>'Nº Documento',
           'fin_documento_pagamento_id'=>'Modalidade de Pagamento',
           'descricao'=>'Observação',
           'fin_banco_conta_id'=>'Conta Bancária',
        ];
    }


    
/**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero . '/' . $this->bas_ano_id, ['/fin/pagamento/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
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
    public function getPagamentoOrdem()
    {
       return $this->hasOne(PagamentoOrdem::className(), ['id' => 'fin_pagamento_ordem_id']);
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
    public function getItem()
    {
       return $this->hasMany(PagamentoItem::className(), ['fin_pagamento_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoContas()
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
    public function getUser()
    {
       return $this->hasOne(\app\models\user::className(), ['id' => 'created_by']);
    }



}
