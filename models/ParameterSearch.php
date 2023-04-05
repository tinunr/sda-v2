<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Parameter;

/**
 * ParameterSearch represents the model behind the search form of `app\models\Parameter`.
 */
class ParameterSearch extends Parameter
{
    /**
     * {@inheritdoc}
     */

     public $globalSearch;

     
    public function rules()
    {
        return [
            [['bas_module_id'], 'integer'],
            [['descricao'], 'safe'],
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
        $query = Parameter::find();


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 250,
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
            'bas_module_id' => $this->bas_module_id,
        ]);

        $query->andFilterWhere(['like', 'descricao', $this->globalSearch]);

        return $dataProvider;
    }
}
