<?php

namespace app\modules\cnt\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "PlanoExterno".
 *
 * @property int $id
 * @property string $nome
 */
class PlanoTerceiro extends \yii\db\ActiveRecord
{
    CONST ISENCAO_HONORARIO_NAO = 0;
    CONST ISENCAO_HONORARIO_SIM = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_person';
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
            [['nome','is_fornecedor','is_cliente','nif','isencao_honorario'], 'required'],
            [['nome','endereco'], 'string', 'max' => 405],            
            [['telefone','telemovel', 'nif','is_fornecedor','is_cliente','isencao_honorario'], 'integer'],            
            [['email'], 'email'],  
            ['nif', 'unique', 'targetClass' => '\app\modules\dsp\models\Person', 'message' => 'NIF deve ser unico'],
            ['isencao_honorario', 'default', 'value' => self::ISENCAO_HONORARIO_NAO],
            ['isencao_honorario', 'in', 'range' => [self::ISENCAO_HONORARIO_NAO, self::ISENCAO_HONORARIO_SIM]],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Terceiro',
            'telefone' => 'Telefone',
            'telemovel' => 'Telemovel',
            'endereco' => 'Endereço',
            'nif' => 'NIF',
            'email'=>'E-Mail',
            'is_cliente'=>'Cleinte',
            'is_fornecedor'=>'Fornecedor',
            'isencao_honorario'=>'Isenção de Honorário',
        ];
    }
}
