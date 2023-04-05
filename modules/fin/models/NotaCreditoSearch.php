<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\NotaCredito;
use yii\db\Query;

/**
 * NotaCreditoSearch represents the model behind the search form of `app\models\NotaCredito`.
 */
class NotaCreditoSearch extends NotaCredito
{
    public $globalSearch;
    public $dataInicio;
    public $dataFim;
    public $porCliente;
    public $por_person;
    public $documento_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bas_ano_id','dsp_person_id'], 'integer'],
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
        $query = NotaCredito::find()->orderBy('numero DESC');

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
            'bas_ano_id' => $this->bas_ano_id,
            'dsp_person_id' => $this->dsp_person_id,
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
     public function saldoAfavorAc($params)
     {
         $query = new Query;
         $query->select([
             "CONCAT(A.numero, '/', A.bas_ano_id) AS numero",
             "CONCAT(B.numero, '/', B.bas_ano_id) AS numero_pr",
             'A.valor',
             'total_pago'=>'SUM(C.valor_pago)',
             'personName'=>'D.nome'
             ])
             ->from('fin_nota_credito A')
             ->leftJoin('dsp_processo B','B.id = A.dsp_processo_id')
             ->leftJoin('fin_despesa C','A.id = C.fin_nota_credito_id')
             ->leftJoin('dsp_person D','D.id = A.dsp_person_id')
             ->where(['A.status'=>1])
             ->andWhere(['C.status'=>1])
             ->andWhere(['>','C.saldo',0])
             ->orderBy(['A.bas_ano_id'=>SORT_DESC,'A.numero'=>SORT_DESC])
             ->groupBy(['A.bas_ano_id','A.numero']);

 
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' => [
                 'pageSize' => false,
             ],
              
         ]);
 
         $this->load($params);
 
         if (!$this->validate()) {
             // uncomment the following line if you do not want to return any records when validation fails
             return $dataProvider;
         }
         $query->andFilterWhere([
             'A.dsp_person_id'=>$this->dsp_person_id,
             'A.bas_ano_id'=>$this->bas_ano_id,
         ]);

        $query->andFilterWhere(['>=', 'A.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'A.data', $this->dataFim]);
            
         return $dataProvider;
     }



     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
     public function saldoAfavorAcPerson($params)
     {
         $query = new Query;
         $query->select([
             'dsp_person_id'=>'P.id',
             'personNome'=>'P.nome',
             "CONCAT(A.numero, '/', A.bas_ano_id) AS numero",
             "CONCAT(B.numero, '/', B.bas_ano_id) AS numero_pr",
             'A.valor',
             'total_pago'=>'SUM(C.valor_pago)'
             ])
             ->from('fin_nota_credito A')
             ->leftJoin('dsp_processo B','B.id = A.dsp_processo_id')
             ->leftJoin('fin_despesa C','A.id = C.fin_nota_credito_id')
             ->leftJoin('dsp_person P','A.id = A.dsp_person_id')
             ->orderBy('P.id')
             ->groupBy('P.nome')
            ->having('A.valor > total_pago');

 
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' => [
                 'pageSize' => 250,
             ],
         ]);
 
         $this->load($params);
 
         if (!$this->validate()) {
             // uncomment the following line if you do not want to return any records when validation fails
             return $dataProvider;
         }
         $query->andFilterWhere([
             'A.dsp_person_id'=>$this->dsp_person_id,
             'A.bas_ano_id'=>$this->bas_ano_id,
         ]);

        $query->andFilterWhere(['>=', 'A.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'A.data', $this->dataFim]);
            
         return $dataProvider;
     }
 
}
