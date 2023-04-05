<?php

namespace app\modules\cnt\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RazaoAutoCreate extends Model
{

    public $dataInicio;
    public $dataFim;
    public $cnt_diario_id;
     public $cnt_documento_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['cnt_diario_id'], 'required'],
            [['dataInicio','dataFim'], 'safe'],
            [['cnt_diario_id','cnt_documento_id'], 'each', 'rule' => ['integer']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cnt_diario_id' => 'Diario',
            'cnt_documento_id'=>'Documentos',
            'bas_ano_id' => 'Ano',
            'status'=>'Estado',
            'dataInicio'=>'Data Inicio',
            'dataFim'=>'Data Fim',
        ];
    }

}
