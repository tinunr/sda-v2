<?php

namespace app\modules\fin\models;

use Yii; 
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\FaturaDebitoClienteBehavior;
use yii\helpers\Html;

/**
 * This is the model class for table "candidatura_docente".
 *
 * @property integer $id
 * @property integer $perfil_id
 * @property string $desciplina
 */
class FaturaDebitoCliente extends \yii\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fin_fatura_debito_cliente';
    }

    public $fin_fatura_provisoria_id;
    public $fin_totoal_fp;

    const STATUS_ANULADO = 0;
    const STATUS_ATIVO = 1;

    const POR_VALIDAR = 0;
    const VALIDADO = 1;
    const ENVIADO = 2;
    const FATURA_DEFINITIVA_SERIE_A = 1;
    const FATURA_DEFINITIVA_SERIE_B = 2;

    const STATUS_TEXTO = [
        self::STATUS_ANULADO => 'Anulado',
        self::STATUS_ATIVO => 'Ativo',
    ];

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
            'faturadefinitiva' => [
                'class' => FaturaDebitoClienteBehavior::className(),
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data', 'dsp_processo_id', 'valor', 'bas_ano_id', 'dsp_person_id'], 'required'],
            [['numero', 'dsp_processo_id', 'bas_ano_id', 'dsp_person_id', 'n_registo', 'n_receita', 'acrescimo'], 'integer'],
            [['numero', 'bas_ano_id'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id']],
            // ['numero', 'unique', 'message' => 'Este numero já existe.'],
            [['data'], 'safe'],
            [['descricao', 'regime', 'nord', 'formaula', 'posicao_tabela', 'dsp_regime_item_valor', 'dsp_regime_item_tabela_anexa_valor'], 'string', 'max' => '405'],
            [['valor', 'fin_totoal_fp'], 'double'],
            // ['valor', 'compare', 'compareAttribute' => 'fin_totoal_fp', 'operator' => '<='],
            [['data_registo', 'data_receita'], 'safe'],
            [['impresso_principal',  'impresso_intercalar',  'pl',  'gti',  'tce',  'tn',  'form',  'regime_normal',  'regime_especial',  'exprevio_comercial',  'expedente_matricula',  'taxa_comunicaco',  'dv',  'fotocopias',  'qt_estampilhas'], 'integer'],
            ['fin_fatura_provisoria_id', 'each', 'rule' => ['integer']],
            ['status', 'default', 'value' => self::STATUS_ATIVO],
            ['status', 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_ANULADO]],
            ['send', 'default', 'value' => self::POR_VALIDAR],
            ['send', 'in', 'range' => [self::POR_VALIDAR, self::VALIDADO, self::ENVIADO]],
            ['fin_fatura_definitiva_serie', 'default', 'value' => self::FATURA_DEFINITIVA_SERIE_A],
            ['fin_fatura_definitiva_serie', 'in', 'range' => [self::FATURA_DEFINITIVA_SERIE_A, self::FATURA_DEFINITIVA_SERIE_B]],
			[['taxa_comunicaco', 'deslocacao_transporte', 'impresso'], 'number'],

        ];
    }

    public function validateProcesso($attribute, $params)
    {
        $model = FaturaDefinitiva::findOne(['dsp_processo_id' => $this->dsp_processo_id, 'status' => 0]);
        if ($model != null) {
            $this->addError('dsp_processo_id', 'Já existe uma FD para este processo');
        }
        $saldoReceita = (new \yii\db\Query())
            ->from('fin_receita A')
            ->leftJoin('fin_fatura_provisoria B', 'B.id = A.dsp_fataura_provisoria_id')
            ->where(['B.dsp_processo_id' => $this->dsp_processo_id])
            ->andWhere(['A.status' => 1])
            ->sum('A.saldo');
        if ($saldoReceita > 0) {
            $this->addError('dsp_processo_id', 'Este processo não foi recebido na totalidade.');
        }

        $saldoDespesa = (new \yii\db\Query())
            ->from('fin_despesa A')
            ->where(['A.dsp_processo_id' => $this->dsp_processo_id])
            ->andWhere(['A.status' => 1])
            ->andWhere(['A.fin_recebimento_id' => null])
            ->sum('A.saldo');
        if ($saldoDespesa > 0) {
            $this->addError('dsp_processo_id', 'Este processo não foi pago as despesas na sua totalidade.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'numero' => 'Número',
            'dsp_processo_id' => 'Processo',
            'decricao' => 'Descrição',
            'valor' => 'Valor',
            'n_registo' => 'Nº Registo',
            'data_registo' => 'Data Registo',
            'n_receita' => 'Nº Receita',
            'data_receita' => 'Data Receita',
            'dsp_person_id' => 'Cliente',
            'nord' => 'NORD',
            'formaula' => 'Posissão Tabela',
            'regime' => 'Regime',
            'impresso_intercalar' => 'Impresso Intercalar',
            'dsp_processo_id' => 'Processo',
            'dv' => 'D.V',
            'tce' => 'T.C.E',
            'pl' => 'P.L',
            'tn' => 'T.N',
            'gti' => 'G.T.I',
            'fotocopias' => 'Fotocopias',
            'qt_estampilhas' => 'Qt.Estampilhas',
            'form' => 'Form',
            'regime_normal' => 'Regime normal',
            'regime_especial' => 'Regime especial',
            'exprevio_comercial' => 'Exame previo/comercial(hs)',
            'expedente_matricula' => 'Expidente para Matrícula',
            'valor' => 'Valor Fatura',
            'honorario' => 'Honorários',
            'iva_honorario' => 'IVA sobre Honorários',
            'dsp_regime_item_tabela_anexa_valor' => 'Valor Base',
            'taxa_comunicaco' => 'Taxa de Comunicação',
            'fin_fatura_provisoria_id' => 'Fatura Provisória',
            'posicao_tabela' => 'Psição da Tabela',
            'acrescimo' => 'Acréscimo',
            'dsp_regime_item_valor' => 'Valor Regime',


        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero . '/' . $this->bas_ano_id, ['/fin/fatura-debito-cliente/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDefinitiva()
    {
        return $this->hasOne(FaturaDefinitiva::className(), ['id' => 'fin_fatura_definitiva_id']);
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
    public function getReceita()
    {
        return $this->hasOne(Receita::className(), ['dsp_fataura_provisoria_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDebitoClienteItem()
    {
        return $this->hasMany(FaturaDebitoClienteItem::className(), ['fin_fatura_debito_cliente_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDefinitivaProvisoria()
    {
        return $this->hasMany(FaturaDefinitivaProvisoria::className(), ['fin_fatura_definitiva_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(\app\modules\dsp\models\Person::className(), ['id' => 'dsp_person_id']);
    }
     /**
   * Lists all User models.
   * @return mixed
   */
  public function inContabilidade()
  {
    $inContabilidade = \app\modules\cnt\models\Razao::find()
      ->where(['cnt_documento_id' => \app\modules\cnt\models\Documento::NOTA_DE_DEBITO_CLIENTE])
      ->andWhere(['status' => 1])
      ->andWhere(['documento_origem_id' => $this->id])
      ->one(); 
    if (!empty($inContabilidade->id)) {
      return $inContabilidade->id;
    }
    return null;
  }
}
