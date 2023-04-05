<?php

namespace app\modules\dsp\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;
use app\modules\dsp\behaviors\ProcessoBehavior;
use yii\helpers\Html;


/**
 * This is the model class for table "Zona".
 *

 */
class Processo extends \yii\db\ActiveRecord
{

   /**
    * {@inheritdoc}
    */
   public static function tableName()
   {
      return 'dsp_processo';
   }
   public $obsStatus = 1;
   public $despacho_documento;
   public $processo_tarefa;
   public $processo_tce;
   public $pl_data_prorogacao;
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
         'processStatus' => [
            'class' => ProcessoBehavior::className(),
         ],

      ];
   }

   /**
    * @var UploadedFile
    */
   public $xmlFile;

   /**
    * {@inheritdoc}
    */
   public function rules()
   {
      return [
         [['numero', 'dsp_person_id', 'data', 'nome_fatura', 'status', 'bas_ano_id', 'descricao', 'dsp_setor_id'], 'required'],
         [['id', 'numero', 'dsp_person_id', 'status', 'user_id', 'nome_fatura', 'bas_ano_id', 'n_levantamento', 'n_levantamento_ano_id', 'n_levantamento_desembarco_id', 'n_registro_tn', 'fin_currency_id', 'dsp_setor_id', 'status_financeiro_id', 'dv', 'exame_previo_comercial', 'requerimento_espeical', 'requerimento_normal', 'policia_desova', 'policia_selagem', 'dsp_setor_id'], 'integer'],
         [['descricao',], 'string', 'max' => 9000000],
         [['data', 'data_execucao', 'data_conclucao'], 'safe'],
         [['valor'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
         // [['numero'], 'unique', 'targetClass' => '\app\modules\dsp\models\Processo', 'message' => 'Já existe um processo com este numero.'],
         [['numero', 'bas_ano_id'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id']],
         ['n_levantamento_ano_id', 'required', 'when' => function ($model) {
            return $model->n_levantamento > 0;
         }, 'whenClient' => "function (attribute, value) { return $('#n_levantamento').val() >0; }"],
         ['n_levantamento_desembarco_id', 'required', 'when' => function ($model) {
            return $model->n_levantamento != null;
         }, 'whenClient' => "function (attribute, value) { return $('#n_levantamento').val() >0; }"],
         ['dsp_setor_id', 'required', 'when' => function ($model) {
            return $model->user_id != null;
         }, 'whenClient' => "function (attribute, value) { return $('#user_id').val() >0; }"],
         [['despacho_documento', 'processo_tarefa', 'processo_tce', 'pl_data_prorogacao'], 'safe'],



      ];
   }

   /**
    * {@inheritdoc}
    */
   public function attributeLabels()
   {
      return [
         'id' => 'ID',
         'numero' => 'Nº Processo',
         'descricao' => 'Mercadoria',
         'n_registro_tn' => 'Nº de Registro TN',
         'n_levantamento' => 'Nº PL',
         'dsp_person_id' => 'Cliente',
         'nome_fatura' => 'Nome a apresentar na fatura',
         'data' => 'Data',
         'status' => 'Estado',
         'user_id' => 'Responsável',
         'dsp_setor_id' => 'Setor',
         'bas_ano_id' => 'Ano',
         'data_execucao' => 'Data Execução',
         'data_conclucao' => 'Data Conclusão',
         'fin_currency_id' => 'Moeda',
         'n_levantamento_ano_id' => 'Ano',
         'n_levantamento_desembarco_id' => 'Estância',
         'dv' => 'DV (Declaração de Valor)',
         'exame_previo_comercial' => 'Exame prévio/Comercial (hrs)',
         'requerimento_espeical' => 'Requerimento Espeical',
         'requerimento_normal' => 'Requerimento Normal',
         'policia_desova' => 'Quantidade de contentor Policia-acompanhamento p/desova',
         'policia_selagem' => 'Polícia-acompanhamento   p/selagem bebidas',
      ];
   }

   /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero . '/' . $this->bas_ano_id, ['/dsp/processo/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
    }


   /**
    * @return \yii\db\ActiveQuery
    */
   public function getPerson()
   {
      return $this->hasone(Person::className(), ['id' => 'dsp_person_id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getNomeFatura()
   {
      return $this->hasone(Person::className(), ['id' => 'nome_fatura']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getNord()
   {
      return $this->hasOne(Nord::className(), ['dsp_processo_id' => 'id']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getPedidoLevantamento()
   {
      return $this->hasOne(PedidoLevantamento::className(), ['id' => 'n_levantamento', 'bas_ano_id' => 'n_levantamento_ano_id', 'dsp_desembaraco_id' => 'n_levantamento_desembarco_id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getDespacho()
   {
      return $this->hasOne(Despacho::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getUser()
   {
      return $this->hasone(\app\models\User::className(), ['id' => 'user_id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getSetor()
   {
      return $this->hasone(Setor::className(), ['id' => 'dsp_setor_id']);
   }


   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoStatus()
   {
      return $this->hasone(ProcessoStatus::className(), ['id' => 'status']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoStatusFinanceiro()
   {
      return $this->hasone(ProcessoStatus::className(), ['id' => 'status_financeiro_id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoDespachoDocumento()
   {
      return $this->hasMany(ProcessoDespachoDocumento::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoTce()
   {
      return $this->hasMany(ProcessoTce::className(), ['dsp_processo_id' => 'id']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoTarefa()
   {
      return $this->hasMany(ProcessoTarefa::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoHistorico()
   {
      return $this->hasMany(ProcessoHistorico::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoObs()
   {
      return $this->hasMany(ProcessoObs::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getFaturaProvisorio()
   {
      return $this->hasMany(\app\modules\fin\models\FaturaProvisoria::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getDespesa()
   {
      return $this->hasMany(\app\modules\fin\models\Despesa::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getFaturaDefinitiva()
   {
      return $this->hasMany(\app\modules\fin\models\FaturaDefinitiva::className(), ['dsp_processo_id' => 'id']);
   }

   
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getFaturaEletronica()
   {
      return $this->hasMany(\app\modules\fin\models\FaturaEletronica::className(), ['dsp_processo_id' => 'id']);
   }

    /**
    * @return \yii\db\ActiveQuery
    */
   public function getFaturaDebitoCliente()
   {
      return $this->hasMany(\app\modules\fin\models\FaturaDebitoCliente::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoWorkflow()
   {
      return $this->hasMany(ProcessoWorkflow::className(), ['dsp_processo_id' => 'id']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoStatusOperacional()
   {
      return $this->hasMany(ProcessoStatusOperacional::className(), ['dsp_processo_id' => 'id']);
   }
   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoStatusFinanceiroHistorico()
   {
      return $this->hasMany(ProcessoStatusFinanceiro::className(), ['dsp_processo_id' => 'id']);
   }

   /**
    * @return \yii\db\ActiveQuery
    */
   public function getFaturaClassificada()
   {
      return $this->hasMany(FaturaClassificada::className(), ['dsp_processo_id' => 'id']);
   }


   /**
    * @return \yii\db\ActiveQuery
    */
   public function getProcessoDespesaManual()
   {
      return $this->hasMany(ProcessoDespesaManual::className(), ['dsp_processo_id' => 'id']);
   }

   public function getNumeroPedidoLevantamento()
   {
      return empty($this->pedidoLevantamento->id) ? '' : $this->pedidoLevantamento->id;
   }
   /**
    * Undocumented function
    *
    * @param Processo $model
    * @return void
    */
   public  function diasPedidoLevantamento()
   {
      // existe data prorogração em pl
      $pl = null;
      if (!empty($this->pedidoLevantamento->data_proragacao)) {
         $pl = Yii::$app->formatter->asRelativeTime($this->pedidoLevantamento->data_proragacao);
         $origin = new \DateTime(date('Y-m-d'));
         $target = new \DateTime($this->pedidoLevantamento->data_proragacao);
         $interval = $origin->diff($target);
         // return $interval->format('%R%a days');
      } else {
         if (!empty($this->pedidoLevantamento->data_regularizacao)) {
            $pl = Yii::$app->formatter->asRelativeTime($this->pedidoLevantamento->data_regularizacao);
            $origin = new \DateTime(date('Y-m-d'));
            $target = new \DateTime($this->pedidoLevantamento->data_regularizacao);
            $interval = $origin->diff($target);
            // return $interval;
            // return $interval->format('%R%a dias');
         }
         return $pl;
      }
      return null;
   }

   /**
    * Undocumented function
    *
    * @param Processo $model
    * @return void
    */
   public  function getNumeroRegisto()
   {
      return !empty($this->nord->despacho->id) ? $this->nord->despacho->id : null;
   }

   /**
    * Undocumented function
    *
    * @param Processo $model
    * @return void
    */
   public  function getDataRegisto()
   {
      return !empty($this->nord->despacho->data_registo) ? Yii::$app->formatter->asDate($this->nord->despacho->data_registo) . ' / ' . Yii::$app->formatter->asRelativeTime($this->nord->despacho->data_registo) : null;
   }

   /**
    * Undocumented function
    *
    * @param Processo $model
    * @return void
    */
   public  function getNumeroLiquidacao()
   {
      return !empty($this->nord->despacho->numero_liquidade) ? $this->nord->despacho->numero_liquidade : null;
   }

   /**
    * Undocumented function
    *
    * @param Processo $model
    * @return void
    */
   public  function getDataLiquidacao()
   {
      $liq = null; //data liquidação
      if (!empty($this->nord->despacho->data_liquidacao)) {
         $liq = Yii::$app->formatter->asDate($this->nord->despacho->data_liquidacao) . ' / ' . Yii::$app->formatter->asRelativeTime($this->nord->despacho->data_liquidacao);
      }
      return $liq;
   }


   /**
    * Undocumented function
    *
    * @param Processo $model
    * @return void
    */
   public  function diasLiquidacao()
   {
      $liq = 0; //data liquidação
      if (!empty($this->nord->despacho->data_liquidacao)) {
         $liq = Yii::$app->formatter->asRelativeTime($this->nord->despacho->data_liquidacao);
      }
      return $liq;
   }

   /**
    * Undocumented function
    *
    * @param Processo $model
    * @return void
    */
   public  function diasEscritorio()
   {
      // dias de documento no escritorio
      return  Yii::$app->formatter->asRelativeTime($this->created_at);
   }

   public function plDataRegularizacao()
   {
      $data = null;
      if (!empty($this->pedidoLevantamento->data_proragacao)) {
         $data = $this->pedidoLevantamento->data_proragacao;
      } elseif (!empty($this->pedidoLevantamento->data_regularizacao)) {
         $data = $this->pedidoLevantamento->data_regularizacao;
      }
      return $data;
   }

   public function dataUltimoWorkflow()
   {
      $workflow = ProcessoWorkflow::find()->where(['dsp_processo_id' => $this->id])->orderBy('id desc')->one();
      if (!empty($workflow)) {
         return Yii::$app->formatter->asDateTime($workflow->data_inicio) . ' - ' . Yii::$app->formatter->asRelativeTime($workflow->data_inicio);
      }
      return 'workflow nao definido';
   }
}
