<?php

namespace app\modules\cnt\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\cnt\behaviors\Modelo106Behavior;
/**
 * This is the model class for table "Modelo106".
 *
 * @property int $id
 * @property string $descricao
 */
class Modelo106 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnt_modelo_106';
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
            'modelo106' => [
                'class' =>Modelo106Behavior::className(),
            ],

        ];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ano','mes','nif','cd_af','designacao_social','reparticao_financa','nif_representante_legal','representante_legal','data','tipo_entrega','doc_cliente','doc_fornecedor','doc_reg_cliente','doc_reg_fornecedor'], 'required'],
            [['ano','mes','nif','cd_af','nif_representante_legal','tipo_entrega','doc_cliente','doc_fornecedor','doc_reg_cliente','doc_reg_fornecedor','bloquear_modelo','tecnico_conta_nif'], 'integer'],
            [['data'], 'safe'],
            [['designacao_social','reparticao_financa','representante_legal','tecnico_conta_nome'], 'string', 'max'=>405],
            [['ano','mes'], 'unique', 'targetAttribute' => ['ano','mes']],
            [['valor_1','valor_2','valor_3','valor_4','valor_5','valor_6','valor_7','valor_8','valor_9','valor_10'], 'double'],
            [['valor_11','valor_12','valor_13','valor_14','valor_15','valor_16','valor_17','valor_18','valor_19','valor_20'], 'double'],
            [['valor_21','valor_22','valor_23','valor_24','valor_25','valor_26','valor_27','valor_28','valor_29','valor_30'], 'double'],
            [['valor_31','valor_32','valor_33','valor_34','valor_35','valor_36','valor_37','valor_38','valor_39','valor_40'], 'double'],
            [['valor_41','valor_42','valor_3','valor_44','valor_45','valor_46','valor_47','valor_48','valor_49','valor_50'], 'double'],

            

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ano' => 'Ano',
            'mes' => 'Mes',
            'nif' => 'NIF',
            'designacao_social'=>'Designação Social',
            'reparticao_financa'=>'Repartição Finança',
            'nif_representante_legal'=>'NIF Representante Legal',
            'representante_legal'=>'Representante Legal',
            'tipo_entrega'=>'Tipo Entrega',
            'doc_cliente'=>'Cliente',
            'doc_fornecedor'=>'Fornecidores',
            'doc_reg_cliente'=>'Reg. Cliente',
            'doc_reg_fornecedor'=>'Reg. Fornecdor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelo106Cliente()
    {
        return $this->hasMany(Modelo106Cliente::className(), ['cnt_modelo_106_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelo106Fornecedor()
    {
        return $this->hasMany(Modelo106Fornecedor::className(), ['cnt_modelo_106_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnos()
    {
        return $this->hasOne(\app\models\Ano::className(), ['id' => 'ano']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMess()
    {
        return $this->hasOne(\app\models\Mes::className(), ['id' => 'mes']);
    }



}
