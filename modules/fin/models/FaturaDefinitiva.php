<?php

namespace app\modules\fin\models;

use Yii;
use yii\db\Query;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\FaturaDefinitivaBehavior;
use app\modules\dsp\models\Person;
use yii\helpers\Html;
/**
 * This is the model class for table "candidatura_docente".
 *
 * @property integer $id
 * @property integer $perfil_id
 * @property string $desciplina
 */
class FaturaDefinitiva extends \yii\db\ActiveRecord
{



    public $fin_fatura_provisoria_id;
    public $fin_totoal_fp;
    public $undo_fatura;
    public $undo_nota_debito;

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
    public static function tableName()
    {
        return 'fin_fatura_definitiva';
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
            'faturadefinitiva' => [
                'class' => FaturaDefinitivaBehavior::className(),
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
            ['valor', 'compare', 'compareAttribute' => 'fin_totoal_fp', 'operator' => '<='],
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
            [['undo_fatura','undo_nota_debito'], 'integer'],

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
            'numero' => 'Nº FD',
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
        return Html::a($this->numero . '/' . $this->bas_ano_id, ['/fin/fatura-definitiva/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaEletronicaLink()
    {
        if(($fatura = $this->faturaEletronica ) !==null){
            return Html::a($fatura->numero . '/' . $fatura->bas_ano_id, ['/fin/fatura-eletronica/view', 'id' => $fatura->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
        }
        return false;
       
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDebitoClienteLink()
    {
        if(($faturaDebitoCliente = $this->faturaDebitoCliente ) !==null){
            return Html::a($faturaDebitoCliente->numero . '/' . $faturaDebitoCliente->bas_ano_id, ['/fin/fatura-debito-cliente/view', 'id' => $faturaDebitoCliente->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
        }
        return false;
       
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function numero()
    {
        return  $this->numero . '/' . $this->bas_ano_id; 
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
    public function getFaturaDefinitivaItem()
    {
        return $this->hasMany(FaturaDefinitivaItem::className(), ['fin_fatura_definitiva_id' => 'id']);
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
    public function getFaturaEletronica()
    {
        return $this->hasOne(FaturaEletronica::className(), ['fin_fatura_definitiva_id' => 'id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaDebitoCliente()
    {
        return $this->hasOne(FaturaDebitoCliente::className(), ['fin_fatura_definitiva_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(\app\modules\dsp\models\Person::className(), ['id' => 'dsp_person_id']);
    }

    /**
     * Lists all User models.sss
     * @return mixed
     */
    public function valorFaturaEletonica()
    {
        return $this->valor - $this->valorDebitoCliente();
    }

    /**
     * Lists all User models.sss
     * @return mixed
     */
    public function itemsFaturaEletronica()
    {
        $query = new Query;
        return $query->select(['B.id', 'B.descricao', 'A.valor'])
            ->from('fin_fatura_definitiva_item A')
            ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
            ->where(['A.fin_fatura_definitiva_id' => $this->id])
            // ->andWhere(['not', ['B.id' => Yii::$app->params['honorario_and_iva']]])
            ->andWhere(['B.dsp_person_id' => Person::ID_AGENCIA])
            ->all();
    }

    /**
     * Lists all User models.sss
     * @return mixed
     */
    public function itemsDebitoCliente()
    {
        $query = new Query;
        return $query->select(['B.id', 'B.descricao', 'A.valor'])
            ->from('fin_fatura_definitiva_item A')
            ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
            ->where(['A.fin_fatura_definitiva_id' => $this->id])
            ->andWhere(['not', ['B.dsp_person_id' => Person::ID_AGENCIA]])
            ->all();
    }

    /**
     * Lists all User models.sss
     * @return mixed
     */
    public function valorDebitoCliente()
    {
        $query = new Query;
        return $query->select(['SUM(A.valor)'])
            ->from('fin_fatura_definitiva_item A')
            ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
            ->where(['A.fin_fatura_definitiva_id' => $this->id])
            ->andWhere(['not', ['B.dsp_person_id' => Person::ID_AGENCIA]])
            ->scalar();
    }
	
	
	/**
     * calcular Total valor Outros Honorários
     * @return scalar
     */
    public function valorOutrosHonorarios()
    {
        $total = 0;
		if($this->taxa_comunicaco > 0){
			$total = $total + $this->taxa_comunicaco;
		}
		if($this->deslocacao_transporte > 0){
			$total = $total + $this->deslocacao_transporte;
		}
		if($this->impresso > 0){
			$total = $total + $this->impresso;
		}
		return $total;
    }
	
	/**
    * calcular Valor Honorários
    * @return scalar
    */
   public function valorHonorarioFaturaDefinitiva()
   {
      $query = new Query;
      return $query->select(['A.valor'])
         ->from('fin_fatura_definitiva_item A')
         ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
         ->where(['A.fin_fatura_definitiva_id' => $this->id])
         ->andWhere(['B.id' => 1002])
         ->scalar();
   }

   /**
    * get Valor IVA de Honorários
    * @return scalar
    */
   public function valorIvaHonorarioFaturaDefinitiva()
   {
      $query = new Query;
      return $query->select(['A.valor'])
         ->from('fin_fatura_definitiva_item A')
         ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
         ->where(['A.fin_fatura_definitiva_id' => $this->id])
         ->andWhere(['B.id' => 1003])
         ->scalar();
   }


   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function faturaProvisoriasLink()
   {
    $html = '';
    if(!empty($this->faturaDefinitivaProvisoria)){
     foreach($this->faturaDefinitivaProvisoria as $faturaDefinitivaProvisoria){
         $html .= $faturaDefinitivaProvisoria->faturaProvisoria->getNumber().', '  ; 
     }}
        return $html;
    }

    /**
    * Lists all User models.sss
    * @return mixed
    */
   public function recebimentosLink()
   { 
        $html = '';
        if(!empty($this->faturaDefinitivaProvisoria)){
            foreach($this->faturaDefinitivaProvisoria as $faturaDefinitivaProvisoria){ 
                if(!empty($faturaDefinitivaProvisoria->faturaProvisoria->receita->recebimentoItem)){
                    foreach($faturaDefinitivaProvisoria->faturaProvisoria->receita->recebimentoItem as $recebimentoItem){
                        $html .= $recebimentoItem->recebimento->getNumber().', '  ;  
                        }
                }
            }
        }
        return $html;
    }

    /**
    * Lists all User models.sss
    * @return mixed
    */
   public function encontroDeContasLink()
   { 
        $html = '';
        if(!empty($this->faturaDefinitivaProvisoria)){
            foreach($this->faturaDefinitivaProvisoria as $faturaDefinitivaProvisoria){ 
                if(!empty($faturaDefinitivaProvisoria->faturaProvisoria->receita->ofAccounts)){
                    foreach($faturaDefinitivaProvisoria->faturaProvisoria->receita->ofAccounts as $ofAccounts){
                        $html .= $ofAccounts->getNumber().', '  ;  
                        }
                }
                if(!empty($faturaDefinitivaProvisoria->faturaProvisoria->receita->ofAccountsItem)){
                    foreach($faturaDefinitivaProvisoria->faturaProvisoria->receita->ofAccountsItem as $ofAccountsItem){
                        $html .= $ofAccountsItem->ofAccounts->getNumber().', '  ;  
                        }
                }
            }
        }
        return $html;
    }

   
   
   /**
    * Lists all User models.sss
    * @return mixed
    */
   public function FaturasE()
   {
      $recebimentos =  (new \yii\db\Query())->select([
         'fatura_provisorias' => "GROUP_CONCAT(CONCAT(B.numero, '/', B.bas_ano_id))", 
         'recibos' => "GROUP_CONCAT(CONCAT(E.numero, '/', E.bas_ano_id))"
      ])
         ->from('fin_fatura_definitiva_provisoria A')
         ->leftJoin('fin_fatura_provisoria B', 'A.fin_fatura_provisoria_id = B.id')
         ->innerJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id')
         ->leftJoin('fin_recebimento_item D', 'C.id = D.fin_receita_id')
         ->leftJoin('fin_recebimento E', 'E.id = D.fin_recebimento_id AND E.status=1')
         ->where('B.status=1') 
         ->andWhere(['A.fin_fatura_definitiva_id' => $this->id])
         ->groupBy('A.fin_fatura_definitiva_id')
         ->one();

      $encontro_de_contas_a =  (new \yii\db\Query())->select([
         'fatura_provisorias' => "GROUP_CONCAT(CONCAT(B.numero, '/', B.bas_ano_id))", 'recibos' => "GROUP_CONCAT(CONCAT(E.numero, '/', E.bas_ano_id))"
      ])
         ->from('fin_fatura_definitiva_provisoria A')
         ->leftJoin('fin_fatura_provisoria B', 'A.fin_fatura_provisoria_id = B.id')
         ->innerJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id')
         ->leftJoin('fin_of_accounts_item D', 'C.id = D.fin_receita_id')
         ->leftJoin('fin_of_accounts E', 'E.id = D.fin_of_account_id AND E.status=1')
         ->where('B.status=1') 
         ->andWhere(['A.fin_fatura_definitiva_id' => $this->id])
         ->groupBy('A.fin_fatura_definitiva_id')
         ->one();

      $encontro_de_contas_b =  (new \yii\db\Query())->select([
         'fatura_provisorias' => "GROUP_CONCAT(CONCAT(B.numero, '/', B.bas_ano_id))", 'recibos' => "GROUP_CONCAT(CONCAT(D.numero, '/', D.bas_ano_id))"
      ])
         ->from('fin_fatura_definitiva_provisoria A')
         ->leftJoin('fin_fatura_provisoria B', 'A.fin_fatura_provisoria_id = B.id')
         ->innerJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id')
         ->leftJoin('fin_of_accounts D', 'C.id = D.fin_receita_id AND D.status=1')
         ->leftJoin('fin_of_accounts_item E', 'E.fin_of_account_id = D.id ')
         ->where('B.status=1')
         ->andWhere(['A.fin_fatura_definitiva_id' => $this->id])
         ->groupBy('A.fin_fatura_definitiva_id')
         ->one();
      return [
         'fatura_provisorias' => empty($recebimentos['fatura_provisorias']) ? '' : 'FP: ' . $recebimentos['fatura_provisorias'],
         'recibos' => (empty($recebimentos['recibos']) ? '' : 'REC: ' . $recebimentos['recibos']) . (empty($encontro_de_contas_a['recibos']) ? '' : ',EC: ' . $encontro_de_contas_a['recibos']) . (empty($encontro_de_contas_b['recibos']) ? '' : ',EC: ' . $encontro_de_contas_b['recibos']),
      ];
   }
}
