<?php

namespace app\modules\cnt\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cnt\models\PlanoConta;

/**
 * PlanoContaSearch represents the model behind the search form of `app\models\PlanoConta`.
 */
class PlanoContaSearch extends PlanoConta
{
    /**
     * {@inheritdoc}
     */

     public $globalSearch;

     
    public function rules()
    {
        return [
            [['globalSearch'], 'integer'],
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
        $query = PlanoConta::find();


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['path' => SORT_ASC]],
        ]);

        $this->load($params);
        $pai = PlanoConta::find()->where(['id'=>$this->globalSearch])->asArray()->one();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		if($this->globalSearch){
			$query->andFilterWhere(['like', 'path', $pai['path'].'%',false]);
		}
        
// print_r($query->createCommand()->getRawSql());die();

        return $dataProvider;
    }
}
