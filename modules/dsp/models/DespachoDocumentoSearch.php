<?php

namespace app\modules\dsp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\dsp\models\DespachoDocumento;

/**
 * DespachoDocumentoSearch represents the model behind the search form of `app\models\DespachoDocumento`.
 */
class DespachoDocumentoSearch extends DespachoDocumento
{
    public $globalSearch;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
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
        $query = DespachoDocumento::find();

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
        ]);


        $query->orFilterWhere(['like', 'descricao', $this->globalSearch]);
        

        return $dataProvider;
    }
}