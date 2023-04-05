<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\Recebimento;
use yii\db\Query;

/**
 * RecebimentoSearch represents the model behind the search form of `app\models\Recebimento`.
 */
class RecebimentoSearch extends Recebimento
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
            [['id','dsp_person_id','bas_ano_id','fin_recebimento_tipo_id'], 'integer'],
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
        $query = Recebimento::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
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

        $query->orFilterWhere(['numero'=>$this->globalSearch])
              ->orFilterWhere(['valor'=>$this->globalSearch]);
        
        // grid filtering conditions
        $query->andFilterWhere([
            'dsp_person_id' => $this->dsp_person_id,
            'bas_ano_id' => $this->bas_ano_id,
            'fin_recebimento_tipo_id'=>$this->fin_recebimento_tipo_id,
        ]);

        $query->andFilterWhere(['>=', 'data', $this->dataInicio])
        ->andFilterWhere(['<=', 'data', $this->dataFim]);


        

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

        $query = Recebimento::find()
               ->where(['dsp_person_id' => $this->dsp_person_id]);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


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


        

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
     public function saldoAfavorAd($params)
     { 
         $query = new Query;
         $query->select(["CONCAT(A.numero, '/', A.bas_ano_id) AS numero",'A.data','valor_recebido'=>'SUM(A.valor)','valor_despesa'=>'SUM(D.valor)','total_pago'=>'SUM(D.valor_pago)'])
             ->from('fin_recebimento A')
             ->leftJoin('fin_despesa D','A.id = D.fin_recebimento_id')
             ->where('A.fin_recebimento_tipo_id =2')
             ->groupBy('A.id')
             ->orderBy('A.numero')
            ->having('valor_recebido > total_pago');

 
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
     public function saldoAfavorAdPerson($params)
     {
         $subQuery = new Query;
         $subQuery->select(['sum(BB.valor) as honorario'])
             ->from('fin_fatura_provisoria_item BB')
             ->leftJoin('dsp_item CC','BB.dsp_item_id = CC.id')
             ->where('B.id = BB.dsp_fatura_provisoria_id')
             ->andWhere('CC.dsp_person_id = 1');
 
         $query = new Query;
         $query->select(['dsp_person_id'=>'P.id','personName'=>'P.nome',"CONCAT(A.numero, '/', A.bas_ano_id) AS numero",'A.data','valor_recebido'=>'SUM(A.valor)','honorario'=>$subQuery,'valor_despesa'=>'SUM(D.valor)','total_pago'=>'SUM(D.valor_pago)'])
             ->from('fin_recebimento A')
             ->leftJoin('fin_fatura_provisoria B','A.dsp_fatura_provisoria_id = B.id')
             ->leftJoin('fin_receita C','A.id = C.dsp_fataura_provisoria_id')
             ->leftJoin('fin_despesa D','B.id = D.dsp_fatura_provisoria_id')
             ->leftJoin('dsp_person P','P.id = A.dsp_person_id')
             ->where('A.fin_recebimento_tipo_id =2')
             ->groupBy('P.id')
             ->orderBy('P.nome')
            ->having('valor_despesa > total_pago');

 
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
     public function searchFp($params)
     {
         $query = new Query;
        $query = new Query;
         $query->select(["CONCAT(D.numero, '/', D.bas_ano_id) AS numero_fp","CONCAT(B.numero, '/', B.bas_ano_id) AS numero",'D.mercadoria','D.data','valor_recebido'=>'A.valor_recebido','cliente'=>'E.nome'])
             ->from('fin_recebimento_item A')
             ->leftJoin('fin_recebimento B','A.fin_recebimento_id = B.id')
             ->leftJoin('fin_receita C','A.fin_receita_id = C.id')
             ->leftJoin('fin_fatura_provisoria D','C.dsp_fataura_provisoria_id = D.id')
             ->leftJoin('dsp_person E','D.dsp_person_id = E.id')
             ->groupBy('D.numero');
 
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' => [
                 'pageSize' => 50,
             ],
         ]);
 
         $this->load($params);
 
         if (!$this->validate()) {
             // uncomment the following line if you do not want to return any records when validation fails
             return $dataProvider;
         }
         $query->andFilterWhere([
             'D.dsp_person_id'=>$this->dsp_person_id,
             'B.bas_ano_id'=>$this->bas_ano_id,
         ]);
         $query->andFilterWhere(['>=', 'B.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'B.data', $this->dataFim]);
         return $dataProvider;
     }



      /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
     public function adiantamento($params)
     { 
         $query = new Query;
         $query->select([
             'A.id',
             "CONCAT(A.numero, '/', A.bas_ano_id) AS numero",
             'A.descricao',
             'A.data',
             'valor_recebido'=>'SUM(A.valor)',
             'valor_despesa'=>'SUM(D.valor)',
             'valor_pago'=>'SUM(D.valor_pago)'
             ])
             ->from('fin_recebimento A')
             ->leftJoin('fin_despesa D','A.id = D.fin_recebimento_id')
             ->where('A.fin_recebimento_tipo_id=2')
             ->groupBy('A.id')
             ->orderBy('A.id DESC, A.bas_ano_id DESC');



        /**
         * Calcular o valor da despesa
         */
        $query2 = (new \yii\db\Query())
            ->select([
                'Z.id', 
                'Z.numero', 
                'Z.nord', 
                'Z.mercadoria',
                'Z.person', 
                'Z.data', 
                'valor_despesa' => 'SUM(A.valor)',
                'Z.total_pago'
            ])
            ->from(['Z' => $query])
            ->leftJoin('fin_despesa A', 'A.status = 1 AND A.of_acount=0 AND A.dsp_processo_id = Z.id')
            ->where(['>','total_pago',0])
            ->groupBy('Z.id');

 
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' =>   false, 
         ]);
 
         $this->load($params);
 
         if (!$this->validate()) {
             // uncomment the following line if you do not want to return any records when validation fails
             return $dataProvider;
         }
         $query->andFilterWhere([
             'A.dsp_person_id'=>$this->dsp_person_id,
         ]);
        $query->andFilterWhere(['>=', 'A.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'A.data', $this->dataFim]);

         return $dataProvider;
     }



}
