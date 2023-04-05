<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\PagamentoOrdem;

/**
 * PagamentoOrdemSearch represents the model behind the search form of `app\models\PagamentoOrdem`.
 */
class PagamentoOrdemSearch extends PagamentoOrdem
{
    public $globalSearch;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_person_id', 'bas_ano_id', 'status'], 'integer'],
            [['globalSearch', 'send'], 'safe'],

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
        $query = PagamentoOrdem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
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

        $query->orFilterWhere(['numero' => $this->globalSearch])
            ->orFilterWhere(['valor' => $this->globalSearch]);
        $query->andFilterWhere([
            'dsp_person_id' => $this->dsp_person_id,
            'bas_ano_id' => $this->bas_ano_id,
            'send' => $this->send,
        ]);




        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCreate($params)
    {
        $this->load($params);

        $query = PagamentoOrdem::find()
            ->where(['status' => 1, 'dsp_person_id' => $this->dsp_person_id]);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 500,
            ],
        ]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'dsp_person_id' => $this->dsp_person_id,
            'bas_ano_id' => $this->bas_ano_id,
        ]);




        return $dataProvider;
    }
}