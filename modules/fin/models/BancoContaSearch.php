<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\BancoConta;

/**
 * BancoContaSearch represents the model behind the search form of `app\models\BancoConta`.
 */
class BancoContaSearch extends BancoConta
{
    public $globalSearch;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','fin_banco_id'], 'integer'],
            [['globalSearch','descricao'], 'safe'],

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
        $query = BancoConta::find()->orderBy('cnt_diario_id');

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
            'id' => $this->id,
            'fin_banco_id' => $this->fin_banco_id,
        ]);


        return $dataProvider;
    }
}
