<?php

namespace app\modules\cnt\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cnt\models\DiarioNumero;

class DiarioNumeroSearch extends DiarioNumero
{
    

    public $globalSearch;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['globalSearch','ano','mes'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = DiarioNumero::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        

        $query->andFilterWhere(['like', 'descricao', $this->globalSearch]);
        $query->andFilterWhere([
            'ano'=> $this->ano,
            'mes'=>$this->mes,
            ]);

        return $dataProvider;
    }
}
