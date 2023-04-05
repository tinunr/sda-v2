<?php

namespace app\modules\fin\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "candidatura_docente".
 *
 * @property integer $id
 * @property integer $perfil_id
 * @property string $desciplina
 */
class FaturaProvisoriaRegimeItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fin_fatura_provisoria_regime_item';
    }

    public $item_row;

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

        ];

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dsp_fatura_provisoria_id', 'dsp_regime_item_id','valor_base','tabela_anexa'], 'required'],
            [['dsp_fatura_provisoria_id', 'dsp_regime_item_id','valor_base','desconto','tabela_anexa'], 'integer'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dsp_fatura_provisoria_id' => 'Fatura Provisória',
            'dsp_regime_item_id'=>'Item', 
            'valor_base'=>'Valor Base',
            'tabela_anexa'=>'Tabela Anexa',
            'desconto'=>'Desconto',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFaturaProvisoria()
    {
       return $this->hasone(FaturaProvisoria::className(), ['id' => 'dsp_fatura_provisoria_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegimeItem()
    {
       return $this->hasone(\app\modules\dsp\models\RegimeItem::className(), ['id' => 'dsp_regime_item_id']);
    }

   
}
