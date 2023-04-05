<?php

namespace app\modules\cnt\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cnt\models\Modelo106;

/**
 * Modelo106Search represents the model behind the search form of `app\models\Modelo106`.
 */
class Modelo106Search extends Modelo106
{
    /**
     * {@inheritdoc}
     */

     public $globalSearch;

     
    public function rules()
    {
        return [
            [['ano','mes'], 'integer'],
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
        $query = Modelo106::find()->orderBy('mes');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => ['defaultOrder' => ['ano' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ano' => $this->ano,
            'mes' => $this->mes,
        ]);

        

        return $dataProvider;
    }
}
