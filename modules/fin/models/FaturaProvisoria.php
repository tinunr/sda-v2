<?php

namespace app\modules\fin\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\fin\behaviors\FaturaProvisoriaBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * This is the model class for table "candidatura_docente".
 *
 * @property integer $id
 * @property integer $perfil_id
 * @property string $desciplina
 */
class FaturaProvisoria extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fin_fatura_provisoria';
    }


    public $total;

    const STATUS_ANULADO = 0;
    const STATUS_ATIVO = 1;

    const POR_VALIDAR = 0;
    const VALIDADO = 1;
    const ENVIADO = 2;

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
            'faturaProvisoria' => [
                'class' => FaturaProvisoriaBehavior::className(),
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data', 'numero', 'dsp_person_id', 'mercadoria', 'dsp_processo_id'], 'required'],
            [['numero', 'dsp_person_id', 'impresso_principal', 'dsp_regime_id', 'dsp_regime_item_id', 'dsp_processo_id', 'dv', 'tce', 'pl', 'tn', 'gti', 'fotocopias', 'qt_estampilhas', 'form', 'regime_normal', 'regime_especial', 'exprevio_comercial', 'expedente_matricula', 'taxa_comunicaco', 'bas_ano_id', 'fin_fatura_proforma_id'], 'integer'],
            [['nord', 'dsp_regime_descricao', 'dsp_regime_item_tabela_anexa', 'dsp_regime_item_valor'], 'string', 'max' => 405],
            [['mercadoria'], 'string', 'max' => 9000],
            [['data', 'valor', 'dsp_regime_item_tabela_anexa_valor', 'total'], 'safe'],
            ['status', 'default', 'value' => self::STATUS_ATIVO],
            ['status', 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_ANULADO]],
            ['send', 'default', 'value' => self::POR_VALIDAR],
            ['send', 'in', 'range' => [self::POR_VALIDAR, self::VALIDADO, self::ENVIADO]],
            [['numero', 'bas_ano_id'], 'unique', 'targetAttribute' => ['numero', 'bas_ano_id']],
			[['taxa_comunicaco', 'deslocacao_transporte', 'impresso'], 'number'],





        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'numero' => 'Nº FP',
            'dsp_person_id' => 'Cliente',
            'impresso_principal' => 'impresso Principal',
            'dsp_regime_id' => 'CODE',
            'dsp_regime_descricao' => 'Regime',
            'dsp_regime_item_id' => 'Agenciamento',
            'dsp_regime_item_valor' => 'Valor Base',
            'dsp_regime_item_tabela_anexa' => 'Tabela Anexa',
            'dsp_regime_item_desconto' => 'Desconto',
            'mercadoria' => 'Mercadoria',
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


        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumber()
    {
        return Html::a($this->numero . '/' . $this->bas_ano_id, ['/fin/fatura-provisoria/view', 'id' => $this->id], ['class' => 'btn-link', 'data-pjax' => 0, 'target' => '_blank']); 
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
    public function getRegime()
    {
        return $this->hasOne(\app\modules\dsp\models\Regime::className(), ['id' => 'dsp_regime_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegimeItem()
    {
        return $this->hasOne(\app\modules\dsp\models\RegimeItem::className(), ['id' => 'dsp_regime_item_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegimeItemItem()
    {
        return $this->hasOne(\app\modules\dsp\models\RegimeItem::className(), ['id' => 'dsp_regime_item_tabela_anexa']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaProvisoriaItem()
    {
        return $this->hasMany(FaturaProvisoriaItem::className(), ['dsp_fatura_provisoria_id' => 'id'])->orderBy(['dsp_item_id' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaProvisoriaRegimeItem()
    {
        return $this->hasMany(FaturaProvisoriaRegimeItem::className(), ['dsp_fatura_provisoria_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespesa()
    {
        return $this->hasMany(Despesa::className(), ['dsp_fatura_provisoria_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaProforma()
    {
        return $this->hasOne(FaturaProforma::className(), ['id' => 'fin_fatura_proforma_id']);
    }
    
}
