<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\Caixa;

/**
 * CaixaSearch represents the model behind the search form of `app\models\Caixa`.
 */
class CaixaSearch extends Caixa
{
    public $fin_banco_id;
    public $globalSearch;
    public $fin_documento_pagamento_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','fin_banco_id','fin_banco_conta_id','status'], 'integer'],
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
        $query = Caixa::find();
            //    ->orderBy('data_abertura DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'status' => SORT_ASC,
                    'created_at' => SORT_DESC,
                ]
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
            'fin_banco_conta_id' => $this->fin_banco_conta_id,
            'status' => $this->status,
        ]);       

        return $dataProvider;
    }
}
