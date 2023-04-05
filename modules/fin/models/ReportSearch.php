<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class ReportSearch extends Model
{
    public $status;
    public $dsp_person_id;
    public $por_person;
    public $documento_id;
    public $dataInicio;
    public $dataFim;
    public $bas_ano_id;
    public $globalSearch;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['dsp_person_id', 'documento_id','bas_ano_id'], 'integer'],
            [['status','dataInicio','dataFim','globalSearch'], 'safe'],
            ['por_person', 'boolean'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dsp_person_id' => 'Clente / Fornecidor',
            'documento_id' => 'Documento',
            'bas_ano_id' => 'Ano',
            'status'=>'Estado',
            'dataInicio'=>'Data Inicio',
            'dataFim'=>'Data Fim',
            'por_person'=>'Agrupar por Cliente / Fornecidor',
        ];
    }

}
