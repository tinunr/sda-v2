<?php

namespace app\modules\dsp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\dsp\models\Despacho;

/**
 * ZonaSearch represents the model behind the search form of `app\models\Zona`.
 */
class DespachoSearch extends Despacho
{
    
    public $globalSearch;
    public $beginDate;
    public $endDate;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           [['bas_ano_id'], 'integer'],
            [['globalSearch','beginDate','endDate'], 'safe'],
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
        $query = Despacho::find()->orderBy('id desc');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
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

      
        
        $query->orFilterWhere(['id'=>$this->globalSearch])
              ->orFilterWhere(['n_receita'=>$this->globalSearch])
              ->orFilterWhere(['nord'=>$this->globalSearch]);

        $query->andFilterWhere(['>=', 'data_registo', $this->beginDate])
              ->andFilterWhere(['<=', 'data_registo', $this->endDate]);
        $query->andFilterWhere([
                'bas_ano_id' => $this->bas_ano_id,
            ]);

        
        return $dataProvider;
    }
}
