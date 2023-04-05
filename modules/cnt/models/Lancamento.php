<?php

namespace app\modules\cnt\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
// use app\modules\cnt\behaviors\PlanoContaBehavior;

/**
 * This is the model class for table "PlanoConta".
 *
 * @property int $id
 * @property string $descricao
 */
class Lancamento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_lancamento';
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

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','cnt_lancamento_tipo_id','bas_mes_id','cnt_diario_id'], 'required'],
            [['descricao'], 'string', 'max' => 405],
            [['cnt_lancamento_tipo_id','bas_mes_id','cnt_diario_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descrição',
            'cnt_lancamento_tipo_id' => 'Tipo Lancamento',
            'bas_mes_id' => 'Mês',
            'cnt_diario_id'=>'Diário',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLancamentoTipo()
    {
        return $this->hasOne(LancamentoTipo::className(), ['id' => 'cnt_lancamento_tipo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
     public function getLancamentoItem()
     {
         return $this->hasMany(LancamentoItem::className(), ['cnt_lancamento_id' => 'id']);
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
    public function getDiario()
    {
        return $this->hasOne(Diario::className(), ['id' => 'cnt_diario_id']);
    }

}
