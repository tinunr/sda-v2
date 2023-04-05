<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\BancoTransacao;

/**
 * BancoTransacaoSearch represents the model behind the search form of `app\models\BancoTransacao`.
 */
class BancoTransacaoSearch extends BancoTransacao
{
    public $globalSearch;
    public $dataInicio;
    public $dataFim;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','fin_banco_id','fin_documento_pagamento_id','fin_banco_transacao_tipo_id'], 'integer'],
            [['globalSearch','descricao'], 'safe'],
            [['dataInicio','dataFim'], 'date'],

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
        $query = BancoTransacao::find()->orderby('id DESC');

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
            'fin_banco_id' => $this->fin_banco_id,
            'fin_documento_pagamento_id' => $this->fin_documento_pagamento_id,
            'fin_banco_transacao_tipo_id' => $this->fin_banco_transacao_tipo_id,
        ]);


        $query->andFilterWhere(['>=', 'data', $this->dataInicio])
              ->andFilterWhere(['<=', 'data', $this->dataFim]);
        

        return $dataProvider;
    }
}
