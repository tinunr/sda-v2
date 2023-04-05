<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\NotaDebito;
use yii\db\Query;

/**
 * NotaDebitoSearch represents the model behind the search form of `app\models\NotaDebito`.
 */
class NotaDebitoSearch extends NotaDebito
{
    public $globalSearch;
    public $dataInicio;
    public $dataFim;
    public $porCliente;
    public $por_person;
    public $documento_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bas_ano_id','dsp_person_id'], 'integer'],
            [['globalSearch','descricao'], 'safe'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = NotaDebito::find()->orderBy('numero DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'bas_ano_id' => $this->bas_ano_id,
            'dsp_person_id' => $this->dsp_person_id,
        ]);


        $query->orFilterWhere(['like', 'descricao', $this->globalSearch]);
        

        return $dataProvider;
    }

     
 
}
