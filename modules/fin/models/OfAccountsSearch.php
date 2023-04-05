<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\OfAccounts;

/**
 * OfAccountsSearch represents the model behind the search form of `app\models\OfAccounts`.
 */
class OfAccountsSearch extends OfAccounts
{
    public $globalSearch;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bas_ano_id','status','dsp_person_id'], 'integer'],
            [['globalSearch'], 'safe'],

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
        $query = OfAccounts::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'numero' => SORT_DESC,
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

        // grid filtering conditions
        $query->andFilterWhere([
            'bas_ano_id' => $this->bas_ano_id,
            'dsp_person_id' => $this->dsp_person_id,
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

        $query = OfAccounts::find()
               ->where(['status' => 1,'dsp_person_id' => $this->dsp_person_id]);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
