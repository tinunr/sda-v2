<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\Despesa;

/**
 * DespesaSearch represents the model behind the search form of `app\models\Despesa`.
 */
class DespesaSearch extends Despesa
{
    public $globalSearch;
    public $beginDate;
    public $endDate;
    public $pagamento;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dsp_person_id','bas_ano_id','status','pagamento','is_lock','fin_despesa_tipo_id'], 'integer'],
            [['globalSearch','descricao','beginDate','endDate'], 'safe'],

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
         $query = Despesa::find()
               ->joinWith(['faturaProvisoria', 'processo']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'data' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->orFilterWhere(['like', 'fin_fatura_provisoria.numero', $this->globalSearch])
              ->orFilterWhere(['like','fin_despesa.numero', $this->globalSearch])
              ->orFilterWhere(['like','fin_despesa.valor', $this->globalSearch])
              ->orFilterWhere(['like','dsp_processo.numero', $this->globalSearch]);


        $query->andFilterWhere([
            'fin_despesa.dsp_person_id' => $this->dsp_person_id,
            'fin_despesa.bas_ano_id' => $this->bas_ano_id,
            'fin_despesa.fin_despesa_tipo_id' => $this->fin_despesa_tipo_id,
        ]);
        $query->andFilterWhere(['>=','fin_despesa.data' , $this->beginDate])
              ->andFilterWhere(['<=','fin_despesa.data' , $this->endDate]);


        

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchGrelha($params)
    {
         $query = Despesa::find()
               ->joinWith(['processo'])
               ->where(['not',['fin_despesa.dsp_processo_id'=>null]])
               ->andWhere(['not',['fin_despesa.dsp_processo_id'=>0]])
               ->andWhere(['fin_despesa.status'=>1])
               ->andWhere(['fin_despesa.dsp_fatura_provisoria_id'=>null])
               ->andWhere(['fin_despesa.fin_nota_credito_id'=>null])
               ->andWhere(['fin_despesa.fin_aviso_credito_id'=>null]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 250,
            ],
        ]);
         

         $dataProvider->sort->attributes['processo'] = [
            'asc' => ['dsp_processo.numero' => SORT_ASC],
            'desc' => ['dsp_processo.numero' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        
        $query->andFilterWhere([
            'fin_despesa.dsp_person_id'=>$this->dsp_person_id,
            'fin_despesa.bas_ano_id'=>$this->bas_ano_id,
        ]);
        $query->andFilterWhere(['or',
            ['like','fin_despesa.numero', $this->globalSearch],
            ['like','dsp_processo.numero', $this->globalSearch]
        ]); 




        

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function relatorio($params)
    {
         $query = Despesa::find()
               ->joinWith(['faturaProvisoria', 'processo']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 500,
            ],
        ]);
         $dataProvider->sort->attributes['faturaProvisoria'] = [
            'asc' => ['fin_fatura_provisoria.numero' => SORT_ASC],
            'desc' => ['fin_fatura_provisoria.numero' => SORT_DESC],
        ];

         $dataProvider->sort->attributes['processo'] = [
            'asc' => ['dsp_processo.numero' => SORT_ASC],
            'desc' => ['dsp_processo.numero' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->orFilterWhere(['like', 'fin_fatura_provisoria.numero', $this->globalSearch])
              ->orFilterWhere(['like','fin_despesa.numero', $this->globalSearch])
              ->orFilterWhere(['like','fin_despesa.valor', $this->globalSearch])
              ->orFilterWhere(['like','dsp_processo.numero', $this->globalSearch]);


        $query->andFilterWhere(['fin_despesa.dsp_person_id' => $this->dsp_person_id])
              ->andFilterWhere(['>=','fin_despesa.data' ,$this->beginDate])
              ->andFilterWhere(['<=','fin_despesa.data' ,$this->endDate])
              ->andFilterWhere(['fin_despesa.is_lock' =>$this->is_lock]);
        if ($this->pagamento == 0) {
            $query->andFilterWhere(['>','fin_despesa.saldo' ,0]);
        }
        if ($this->pagamento == 1) {
            $query->andFilterWhere(['=','fin_despesa.saldo', 0]);
        }


        

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
 
         $query = Despesa::find()
                ->joinWith(['faturaProvisoria', 'processo'])
                ->leftJoin('dsp_nord', 'dsp_nord.dsp_processo_id= dsp_processo.id')
                ->where(['>','saldo' ,0])
                ->andWhere(['fin_despesa.status' => 1])
                ->orderBy('dsp_processo.numero');
 
 
         // add conditions that should always apply here
 
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'sort' => [
                 'defaultOrder' => [
                     'id' => SORT_DESC,
                 ]
             ],
             'pagination' => [
                 'pageSize' => 50000,
             ],
         ]);
          $dataProvider->sort->attributes['faturaProvisoria'] = [
             'asc' => ['fin_fatura_provisoria.numero' => SORT_ASC],
             'desc' => ['fin_fatura_provisoria.numero' => SORT_DESC],
         ];

         $dataProvider->sort->attributes['processo.nord.id'] = [
             'asc' => ['dsp_nord.id' => SORT_ASC],
             'desc' => ['dsp_nord.id' => SORT_DESC],
         ];
 
          $dataProvider->sort->attributes['processo'] = [
             'asc' => ['dsp_processo.numero' => SORT_ASC],
             'desc' => ['dsp_processo.numero' => SORT_DESC],
         ];
 
         $this->load($params);
 
         if (!$this->validate()) {
             return $dataProvider;
         }
 
         // grid filtering conditions
         $query->orFilterWhere(['like', 'fin_fatura_provisoria.numero', $this->globalSearch])
               ->orFilterWhere(['like','fin_despesa.numero', $this->globalSearch])
               ->orFilterWhere(['like','fin_despesa.valor', $this->globalSearch])
               ->orFilterWhere(['like','dsp_processo.numero', $this->globalSearch]);
 
 
         $query->andFilterWhere([
             'fin_despesa.dsp_person_id' => $this->dsp_person_id,
             'fin_despesa.dsp_person_id' => $this->dsp_person_id,
             'fin_despesa.bas_ano_id' => $this->bas_ano_id,
         ]);
 
         return $dataProvider;
     }


     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCreateOf($params)
    {
        $this->load($params);

        $query = Despesa::find()
               ->joinWith(['faturaProvisoria', 'processo'])
               ->where(['>','saldo' ,0])
               ->andWhere(['fin_despesa.status' => 1])
               ->andWhere(['fin_despesa.of_acount' => 1]);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 500,
            ],
        ]);
         $dataProvider->sort->attributes['faturaProvisoria'] = [
            'asc' => ['fin_fatura_provisoria.numero' => SORT_ASC],
            'desc' => ['fin_fatura_provisoria.numero' => SORT_DESC],
        ];

         $dataProvider->sort->attributes['processo'] = [
            'asc' => ['dsp_processo.numero' => SORT_ASC],
            'desc' => ['dsp_processo.numero' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->orFilterWhere(['like', 'fin_fatura_provisoria.numero', $this->globalSearch])
              ->orFilterWhere(['like','fin_despesa.numero', $this->globalSearch])
              ->orFilterWhere(['like','fin_despesa.valor', $this->globalSearch])
              ->orFilterWhere(['like','dsp_processo.numero', $this->globalSearch]);


        $query->andFilterWhere([
            'fin_despesa.dsp_person_id' => $this->dsp_person_id,
            'fin_despesa.dsp_person_id' => $this->dsp_person_id,
        ]);

        return $dataProvider;
    }

     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCreateC($params)
    {
        $this->load($params);

        $query = Despesa::find()
               ->where(['>','saldo' ,0])
               ->andWhere(['status' => 1])
               ->andWhere(['dsp_person_id' =>
                        (new yii\db\Query)
                       ->select(['A.dsp_person_id'])
                       ->from('fin_nota_debito A')
                       ->where(['>','A.saldo' ,0])
                       ->groupBy(['A.dsp_person_id'])
                       ]);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
         

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

      

        $query->andFilterWhere([
            'fin_despesa.dsp_person_id' => $this->dsp_person_id,
        ]);

        return $dataProvider;
    }
}
