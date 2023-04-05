<?php

namespace app\modules\dsp\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\dsp\behaviors\PersonBehavior;
/**
 * This is the model class for table "Person".
 *
 * @property int $id
 * @property string $nome
 */
class Person extends \yii\db\ActiveRecord
{
    CONST ISENCAO_HONORARIO_NAO = 0;
    CONST ISENCAO_HONORARIO_SIM = 1;
    CONST ID_AGENCIA = 1;
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
                'custon'=>[
                    'class'=>PersonBehavior::className(),
                ] 
            ];

    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome','is_fornecedor','is_cliente','nif','isencao_honorario','bas_pais_id','endereco'], 'required'],
            [['nome','endereco'], 'string', 'max' => 405],            
            [['telefone','telemovel', 'nif','is_fornecedor','is_cliente','isencao_honorario','bas_pais_id','fax'], 'integer'],            
            [['email'], 'email'],  
            ['nif', 'unique', 'targetClass' => '\app\modules\dsp\models\Person', 'message' => 'NIF deve ser unico'],
            ['isencao_honorario', 'default', 'value' => self::ISENCAO_HONORARIO_NAO],
            ['isencao_honorario', 'in', 'range' => [self::ISENCAO_HONORARIO_NAO, self::ISENCAO_HONORARIO_SIM]],
            // ['nif','max'=>9,'min'=>]


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Cliente',
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

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getPais()
    {
       return $this->hasone(\app\models\Pais::className(), ['id' => 'bas_pais_id']);
    }
}
