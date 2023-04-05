<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AuthItem;

/**
 * AuthItemSearch represents the model behind the search form about `app\models\AuthItem`.
 */
class AuthItemSearch extends AuthItem
{
    public $globalSearch;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['name','globalSearch'], 'safe'],

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
        $query = AuthItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->orFilterWhere(['like', 'name', $this->globalSearch]);
          // grid filtering conditions
          $query->andFilterWhere([
            'type'=>$this->type ,
            
        ]);


        return $dataProvider;
    }


    
}
