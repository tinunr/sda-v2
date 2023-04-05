<?php

namespace app\modules\dsp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\dsp\models\Nord;

/**
 * ZonaSearch represents the model behind the search form of `app\models\Zona`.
 */
class NordSearch extends Nord
{
    
    public $globalSearch;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numero','bas_ano_id','dsp_desembaraco_id'], 'integer'],
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
        $query = Nord::find()
               ->joinWith('processo')
               ->orderBy('dsp_nord.numero DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id' => [
                        'asc' => ['id' => SORT_ASC],
                        'desc' => ['id' => SORT_DESC],
                        // 'default' => SORT_DESC,   
                    ]
                ],
            ],
            'pagination' => [
                'pageSize' =>50,
            ],
        ]);

        $this->load($params);
        // print_r($this);die();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
                
         
       $query->orFilterWhere(['like','dsp_nord.numero',$this->globalSearch])
              ->orFilterWhere(['like','dsp_processo.numero',$this->globalSearch]);
       
 
       // grid filtering conditions
       $query->andFilterWhere([
        'dsp_nord.bas_ano_id' => $this->bas_ano_id,
        'dsp_nord.dsp_desembaraco_id' => $this->dsp_desembaraco_id,
        // 'dsp_nord.numero' => $this->globalSearch,
    ]); 
     
        
        return $dataProvider;
    }
}
