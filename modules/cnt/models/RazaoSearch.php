<?php

namespace app\modules\cnt\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cnt\models\Razao;

/**
 * RazaoSearch represents the model behind the search form of `app\models\Razao`.
 */
class RazaoSearch extends Razao
{
    /**
     * {@inheritdoc}
     */

     public $globalSearch;

     
    public function rules()
    {
        return [
            [['id','cnt_diario_id','cnt_documento_id','bas_ano_id','bas_mes_id'], 'integer'],
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
        $query = Razao::find()->orderBy('cnt_diario_id, numero DESC');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
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
            'cnt_diario_id' => $this->cnt_diario_id,
            'cnt_documento_id' => $this->cnt_documento_id,
            'bas_ano_id' => $this->bas_ano_id,
            'bas_mes_id' => $this->bas_mes_id,
        ]);

        $query->andFilterWhere(['like', 'descricao', $this->globalSearch]);

        return $dataProvider;
    }
}
