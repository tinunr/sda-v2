<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\Receita;

/**
 * ReceitaSearch represents the model behind the search form of `app\models\Receita`.
 */
class ReceitaSearch extends Receita
{
    public $globalSearch;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_person_id','bas_ano_id','status'], 'integer'],
            [['globalSearch','descricao','faturaProvisoria'], 'safe'],

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
        $query = Receita::find();
        

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 500,
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
            'dsp_person_id' => $this->dsp_person_id,
            'bas_ano_id' => $this->bas_ano_id,
        ]);


        $query->orFilterWhere(['like', 'descricao', $this->globalSearch]);
        

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCreate($params)
    {
        $this->load($params);
        
        $query = Receita::find()
               ->joinWith(['faturaProvisoria','faturaDefinitiva'])      
               ->where(['!=','saldo' ,'valor_recebido']);
        
        // grid filtering conditions
        $query->andWhere(['or',
            ['fin_fatura_provisoria.status' => 1],
            ['fin_fatura_definitiva.status' => 1],
        ]); 
        // grid filtering conditions
        $query->andWhere(['or',
            ['!=','fin_fatura_provisoria.send' , 0],
            ['!=','fin_fatura_definitiva.send' , 0],
        ]);       
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => false,
        ]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere(['or',
            ['fin_fatura_provisoria.dsp_person_id' => $this->dsp_person_id],
            ['fin_fatura_definitiva.dsp_person_id' => $this->dsp_person_id],
        ]);


        

        return $dataProvider;
    }
}
