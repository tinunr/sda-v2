<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\CaixaTransacao;

/**
 * CaixaTransacaoSearch represents the model behind the search form of `app\models\CaixaTransacao`.
 */
class CaixaTransacaoSearch extends CaixaTransacao
{
    public $globalSearch;
    public $dataInicio;
    public $dataFim;
    public $fin_banco_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','fin_banco_id'], 'integer'],
            [['globalSearch','descricao','dataInicio','dataFim'], 'safe'],

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
        $query = CaixaTransacao::find()
                ->leftJoin('fin_caixa','fin_caixa.id=fin_caixa_transacao.fin_caixa_id')
                ->orderBy('id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 500000,
            ],
        ]);

        $this->load($params);
// print_r($this);die();
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'fin_caixa.fin_banco_conta_id' => $this->fin_banco_id,
            'fin_caixa_transacao.fin_documento_pagamento_id' => $this->fin_documento_pagamento_id,
            'fin_caixa_transacao.fin_caixa_operacao_id' => $this->fin_caixa_operacao_id,
        ]);

        $query->andFilterWhere(['>=', 'fin_caixa_transacao.data', $this->dataInicio])
        ->andFilterWhere(['<=', 'fin_caixa_transacao.data', $this->dataFim]);


        

        return $dataProvider;
    }



     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
     public function searchCaixaView($params)
     {
         $query = CaixaTransacao::find();
 
         // add conditions that should always apply here
 
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' => [
                 'pageSize' => 5000,
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
             'fin_caixa_id' => $this->fin_caixa_id,
         ]);
 
         
 
         
 
         return $dataProvider;
     }
}
