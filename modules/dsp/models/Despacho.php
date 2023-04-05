<?php

namespace app\modules\dsp\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\modules\dsp\behaviors\DespachoBehavior;


/**
 * This is the model class for table "Zona".
 *

 */
class Despacho extends \yii\db\ActiveRecord
{
   const STATUS_ATIVO = 0;
   const STATUS_ANULADO = 1;
   
   /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dsp_despacho';
    }

     /**
     * {@inheritdoc}
     */
     public static function primaryKey(){
        return ['id','dsp_desembaraco_id','bas_ano_id','nord'];
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
                'processstatus'=>[
                    'class'=>DespachoBehavior::className(),
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
            [['id','bas_ano_id','nord'], 'required'],
            [['rg','id','dsp_desembaraco_id', 'bas_ano_id','numero_liquidade','declarante','n_receita','destinatario_nif','artigo','anulado'], 'integer'],            
            [['modelo', 'verificador', 'reverificador', 'tramitacao','s_registo','s_liquidade','destinatario_nome','time_end_freeze','cor','s_reg'], 'string','max'=>'405'],
            [['nord'], 'safe'],
            [['data_prorogacao','data_registo' ,'data_liquidacao','data_receita'], 'safe'],
            [['valor'],'number','numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],

            ['anulado', 'default', 'value' => self::STATUS_ATIVO],
			['anulado', 'in', 'range' => [self::STATUS_ATIVO, self::STATUS_ANULADO]],
        ];
    }
    


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Nº Registo',
            'numero_registo' => 'Nº Despacho', 
            'data_registo' =>'Data Registo' ,
            'data_liquidacao' =>'Data Liquidação' ,
            'reverificador'=>'Reverificador'  ,
            'numero_liquidade'=>'Nº Liquidação',
            'bas_ano_id'=>'Ano', 
            'dsp_desembaraco_id'=>'Estancia',  
            's_liquidade'=>'S_Liquidade', 
            'verificador'=>'Verificador', 
            'reverificador'=>'Reverificador', 
            'tramitacao'=>'Tramitação', 
            'valor'=>'Total Imposições', 
            'modelo'=>'Modelo', 
            'rg'=>'RG', 
            'declarante'=>'Declarante', 
            'nord'=>'NORD', 
            'artigo'=>'Art.', 
            'destinatario_nif'=>'Destinatário',
             'destinatario_nome'=>'Destinatário Name', 
             'n_receita'=>'Nº Receita', 
             'data_receita'=>'Data Receita', 
             'time_end_freeze'=>'Time end Freeze', 
             'cor'=>'Cor',
             's_registo'=>'S-Reg',
             'anulado'=>'Anulado'

         ];
    }

   
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getNord()
    {
        return $this->hasOne(Nord::className(), ['numero' => 'nord','bas_ano_id'=>'bas_ano_id','dsp_desembaraco_id'=>'dsp_desembaraco_id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getDesembaraco()
    {
        return $this->hasOne(Desembaraco::className(), ['id' => 'dsp_desembaraco_id']);
    }

    
}
