<?php

namespace app\modules\cnt\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cnt\models\PlanoIva;

/**
 * ZonaSearch represents the model behind the search form of `app\models\Zona`.
 */
class PlanoIvaSearch extends PlanoIva
{
    
    public $globalSearch;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['globalSearch','id'], 'safe'],
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
        $query = PlanoIva::find();

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
        $query->orFilterWhere(['like','descricao',$this->globalSearch]);
        return $dataProvider;
    }
}
