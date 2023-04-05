<?php

namespace app\modules\fin\models;

use Yii; 
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\fin\behaviors\FaturaEletronicaBehavior;
use yii\helpers\Html;
use yii\db\Query;

/**
 * This is the model class for table "candidatura_docente".
 *
 * @property integer $id
 * @property integer $perfil_id
 * @property string $desciplina
 */
class FaturaEletronica extends \yii\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fin_fatura_eletronica';
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
                'class' => FaturaEletronicaBehavior::className(),
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
            [['data','iud'], 'safe'],
            [['regime', 'nord', 'formaula', 'posicao_tabela', 'dsp_regime_item_valor', 'dsp_regime_item_tabela_anexa_valor'], 'string', 'max' => '1000'],
            [['descricao', ], 'string', 'max' => '405'],
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
    public function getNumberSemLink()
    {
        return  $this->numero . '/' . $this->bas_ano_id; 
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero . '/' . $this->bas_ano_id, ['/fin/fatura-eletronica/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
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
    public function getFaturaEletronicaItem()
    {
        return $this->hasMany(FaturaEletronicaItem::className(), ['fin_fatura_eletronica_id' => 'id']);
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
    * Lists all User models.sss
    * @return mixed
    */
   public function baseTributavel()
   {
      $query = new Query;
      return $query->select(['A.valor'])
         ->from('fin_fatura_definitiva_item A')
         ->leftJoin('dsp_item B', 'B.id = A.dsp_item_id')
         ->where(['A.fin_fatura_definitiva_id' => $this->fin_fatura_definitiva_id])
         ->andWhere(['B.id' => 1002])
         ->scalar();
   }

    /**
   * Lists all User models.
   * @return mixed
   */
  public function inContabilidade()
  {
    $inContabilidade = \app\modules\cnt\models\Razao::find()
      ->where(['cnt_documento_id' => \app\modules\cnt\models\Documento::FACTURA])
      ->andWhere(['status' => 1])
      ->andWhere(['documento_origem_id' => $this->id])
      ->one(); 
    if (!empty($inContabilidade->id)) {
      return $inContabilidade->id;
    }
    return null;
  }


  public function peStatus(){
    if(!empty($this->iud)){
        return Html::img('@web/img/svg/file-upload-solid.svg',['width'=>'10px']);
    }
return null;
  }



  public function listItemns()
  {
    $model = $this;
   $posicao_tabela_desc= Yii::$app->params['posicao_tabela_desc'];
   $total_honorario = 0;
    $item= [];
         if (!$model->person->isencao_honorario | Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) > 0) :  
                 if (!empty($model->taxa_comunicaco)) :  
                    $total_honorario = $total_honorario + $model->taxa_comunicaco;
                   $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['taxa_comunicaco'],
                        'valor'=> $model->taxa_comunicaco, 
                   ]; 
                endif; 
                 if (!empty($model->tn)) :   
                    $total_honorario = $total_honorario + $model->tn;
                    $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['tn'],
                        'valor'=> $model->tn, 
                   ];  
                endif; 
                if (!empty($model->form)) : 
                    $total_honorario = $total_honorario + $model->form;
                    $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['form'],
                        'valor'=> $model->form, 
                   ]; 
                endif; 
                if (!empty($model->regime_normal)) : 
                    $total_honorario = $total_honorario + $model->regime_normal;
                     $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['regime_normal'],
                        'valor'=> $model->regime_normal, 
                   ]; 
                endif; 
                if (!empty($model->regime_especial)) : 
                    $total_honorario = $total_honorario + $model->regime_especial;
                    $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['regime_especial'],
                        'valor'=> $model->regime_especial, 
                   ];  
                endif; 
                if (!empty($model->exprevio_comercial)) : 
                    $total_honorario = $total_honorario + $model->exprevio_comercial;
                     $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['exprevio_comercial'],
                        'valor'=> $model->exprevio_comercial, 
                   ];   
                endif; 
                if (!empty($model->expedente_matricula)) : 
                    $total_honorario = $total_honorario + $model->expedente_matricula;
                    $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['expedente_matricula'],
                        'valor'=> $model->expedente_matricula, 
                   ];  
               
                endif; 
                if (!empty($model->dv)) : 
                    $total_honorario = $total_honorario + $model->dv;
                     $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['dv'],
                        'valor'=> $model->dv, 
                   ];  
                endif;  
                if (!empty($model->gti)) : 
                    $total_honorario = $total_honorario + $model->gti;
                 $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['gti'],
                        'valor'=> $model->gti, 
                   ];  
                endif; 
                if (!empty($model->pl)) : 
                    $total_honorario = $total_honorario + $model->pl;
                $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['pl'],
                        'valor'=> $model->pl, 
                   ]; 
               
                endif; 
                if (!empty($model->tce)) : 
                    $total_honorario = $total_honorario + $model->tce;
                     $item[] = [
                        'id'=>1076,
                        'descrcao'=>$posicao_tabela_desc['tce'],
                        'valor'=> $model->tce, 
                   ];  
                endif; 
                if ($model->acrescimo > 0) : 
                    $total_honorario = $total_honorario + ($model->acrescimo * $regimeConfig['valorPorItem']);
                    $item[]= [
                        'id'=>1076,
                        'descrcao'=>'Acréscimo',
                        'valor'=> ($model->acrescimo * $regimeConfig['valorPorItem']), 
                   ];  
                endif;  
                $valor_tabel_honorario = Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) - $total_honorario; 
                if (empty($model->tn) && !$model->person->isencao_honorario && ((Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) - $total_honorario) > 0)) : 
                $item[]= [
                        'id'=>1076,
                        'descrcao'=>'Tabela Honorário',
                        'valor'=> (Yii::$app->FinQuery->valorHonorarioFaturaDefinitiva($model->faturaDefinitiva->id) - $total_honorario), 
                   ];  
                endif;   
                endif;  
                foreach (Yii::$app->FinQuery->outrasDespesaFaturaDefinitiva($model->faturaDefinitiva->id) as $key => $modelItem) :  
                     $item[] = [
                        'id'=>str_pad($modelItem['id'], 2, '0', STR_PAD_LEFT),
                        'descrcao'=>$modelItem['descricao'],
                        'valor'=> $modelItem['valor'], 
                   ];  
                
                endforeach;  
                 return $item;
  }
}
