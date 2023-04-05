<?php

namespace app\modules\dsp\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\dsp\behaviors\PedidoLevantamentoBehavior;

/**
 * This is the model class for table "Zona".
 *

 */
class PedidoLevantamento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_pedido_levantamento';
    }

    /**
     * {@inheritdoc}
     */
     public static function primaryKey(){
        return ['id','bas_ano_id','dsp_desembaraco_id'];
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
            'plstatus'=>[
                    'class'=>PedidoLevantamentoBehavior::className(),
            ],

        ];

    }

    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id','status','importador_nif','dsp_desembaraco_id','peso_bruto','n_volumes','r_volumes','importador_nif','bas_ano_id'], 'integer'],            
            [['reverificador','ser','declarante','manifesto','importador_nome','vereficador','reverificador'], 'string','max'=>'405'],
            [['titulo_propriedade'], 'safe'],
            [['data_proragacao','data_registo' ,'data_regularizacao','data_autorizacao'], 'safe'],
            ['data_regularizacao', 'required', 'when' => function ($model) {
                return $model->data_autorizacao != null;
            }, 'enableClientValidation' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bas_ano_id'=>'Ano',
            'dsp_desembaraco_id' => 'Estância',
            'declarante' => 'Declarante',
            'data_registo' =>'Data Elaboração' ,
            'data_autorizacao' =>'Data Autorização' ,
            'data_regularizacao' =>'Data Regularização' ,
            'data_proragacao' =>'Data Prorrogação' ,
            'reverificador'=>'Reverificador',   
            'status'=>'Estado',  
            'n_volumes'=>'Nº Volumes', 
            'r_volumes'=>'Rº Volumes', 
            'peso_bruto'=>'Peso Bruto',
            'ser'=>'Ser',
            'manifesto'=>'Manifesto',
            'importador_nif'=>'NIF Importador',
            'importador_nome'=>'Nome Importador',
            'titulo_propriedade'=>'Título propriedade',
            'vereficador'=>'Vereficador',
         ];
    }

   
    /**
     * @return \yii\db\ActiveQuery
     */
     public function getPedidoLevantamentoStatus()
     {
         return $this->hasOne(PedidoLevantamentoStatus::className(), ['id' => 'status']);
     }

     /**
     * @return \yii\db\ActiveQuery
     */
     public function getDesembaraco()
     {
         return $this->hasOne(Desembaraco::className(), ['id' => 'dsp_desembaraco_id']);
     }

    
}
