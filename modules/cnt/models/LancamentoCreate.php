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
class LancamentoCreate extends Model
{

    public $cnt_lancamento_id;
    public $cnt_diario_id;
    public $bas_ano_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['cnt_diario_id','bas_ano_id','cnt_lancamento_id'], 'required'],
            [['cnt_diario_id','bas_ano_id','cnt_lancamento_id'], 'each', 'rule' => ['integer']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cnt_diario_id' => 'Diario',
            'cnt_lancamento_id'=>'Lançamento',
            'bas_ano_id' => 'Ano',
        ];
    }

}
