<?php

namespace app\modules\cnt\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\cnt\behaviors\PlanoFluxoCaixaBehavior;

/**
 * This is the model class for table "Zona".
 *

 */
class PlanoFluxoCaixa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_plano_fluxo_caixa';
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
            'planofluxocaixa' => [
                'class' => PlanoFluxoCaixaBehavior::className(),
            ],

        ];

    }

    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao','cnt_plano_fluxo_caixa_tipo_id','codigo','cnt_plano_fluxo_caixa_tipo_id'], 'required'],
            [['id','cnt_plano_fluxo_caixa_tipo_id'], 'integer'],
            [['descricao','codigo','path'], 'string', 'max' => 405],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Fluxo de Caixa',
         ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanoFluxoCaixaTipo()
    {
        return $this->hasOne(PlanoFluxoCaixaTipo::className(), ['id' => 'cnt_plano_fluxo_caixa_tipo_id']);
    }

    
}
