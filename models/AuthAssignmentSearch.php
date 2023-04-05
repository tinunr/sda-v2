<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AuthAssignment;

class AuthAssignmentSearch extends AuthAssignment
{
    

    public $globalSearch;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id','globalSearch'], 'safe'],
            [['created_at'], 'integer'],
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
        $query = AuthAssignment::find()
               ->joinWith('user')
               #->groupBy(['user.nome_completo'])
               ->orderBy('user_id');

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

        $query->andFilterWhere([
            'user_id' => $this->user_id,
        ]);

        $query->orFilterWhere(['like', 'item_name', $this->globalSearch])
              ->orFilterWhere(['like', 'user.nome_completo', $this->globalSearch]);

        return $dataProvider;
    }
}
