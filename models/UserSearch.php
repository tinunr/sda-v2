<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    public $globalSearch;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'name', 'username', 'globalSearch',], 'safe'],
            [['name'], 'safe'],
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


           $query = User::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['name'=>SORT_ASC]],
            'pagination' => [
                'pageSize' => 150,
            ],
        ]);

        
         $this->load($params);
         #print_r($this->globalSearch);die();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        } 

        $query->orFilterWhere(['like', 'name', $this->globalSearch])
             ->orFilterWhere(['like', 'username', $this->globalSearch]);

        return $dataProvider;
    }

    
}
