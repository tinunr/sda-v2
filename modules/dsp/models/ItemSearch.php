<?php

namespace app\modules\dsp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\dsp\models\Item;

/**
 * ItemSearch represents the model behind the search form of `app\models\Item`.
 */
class ItemSearch extends Item
{
    /**
     * {@inheritdoc}
     */

     public $globalSearch;

     
    public function rules()
    {
        return [
            [['id','dsp_item_type_id'], 'integer'],
            [['descricao'], 'safe'],
            [['dsp_person_id'], 'safe'],
            [['valor'], 'safe'],
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
        $query = Item::find();


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
            'dsp_item_type_id'=>$this->dsp_item_type_id,
        ]);

        $query->orFilterWhere(['like', 'descricao', $this->globalSearch])
             ->orFilterWhere(['like', 'id', $this->globalSearch])
             ->orFilterWhere(['like', 'cnt_plano_conta_id', $this->globalSearch]);

        return $dataProvider;
    }
}
