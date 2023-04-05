<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\Transferencia;

/**
 * TransferenciaSearch represents the model behind the search form of `app\models\Transferencia`.
 */
class TransferenciaSearch extends Transferencia
{
    public $globalSearch;
    public $bas_ano_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bas_ano_id'], 'integer'],
            [['globalSearch', 'descricao'], 'safe'],

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
        $query = Transferencia::find()
            ->orderby('numero DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);
        // print_r($this);die();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        


        $query->orFilterWhere(['referencia' => $this->globalSearch])
            ->orFilterWhere(['numero' => $this->globalSearch]);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'bas_ano_id' => $this->bas_ano_id,
        ]);

        return $dataProvider;
    }
}
