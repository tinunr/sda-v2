<?php

namespace app\modules\dsp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\dsp\models\PedidoLevantamento;

/**
 * ZonaSearch represents the model behind the search form of `app\models\Zona`.
 */
class PedidoLevantamentoSearch extends PedidoLevantamento
{
    
    public $globalSearch;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status','bas_ano_id'], 'integer'],
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
        $query = PedidoLevantamento::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->orFilterWhere(['like', 'id', $this->globalSearch]);
        $query->andFilterWhere([
            'status' => $this->status,
            'bas_ano_id'=>$this->bas_ano_id,
        ]);        
        return $dataProvider;
    }
}
