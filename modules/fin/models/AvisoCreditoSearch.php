<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\AvisoCredito;
use yii\db\Query;

/**
 * AvisoCreditoSearch represents the model behind the search form of `app\models\AvisoCredito`.
 */
class AvisoCreditoSearch extends AvisoCredito
{
    public $globalSearch;
    public $comPl;
    public $porResponsavel;
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
            [['dsp_person_id','bas_ano_id'], 'integer'],
            [['globalSearch','status','comPl','dataInicio','dataFim','porResponsavel'], 'safe'],
            // [['globalSearch','descricao','dataFim','dataInicio'], 'safe'],

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
        $query = AvisoCredito::find();

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
    public function saldoAfavorNC($params)
    {

    $query = new Query;
    $query->select(["CONCAT(A.numero, '/', A.bas_ano_id) AS numero",'mercadoria'=>'A.descricao','valor_recebido'=>'A.valor','valor_despesa'=>'SUM(B.valor)','total_pago'=>'SUM(valor_pago)'])
        ->from('fin_aviso_credito A')
        ->leftJoin('fin_despesa B','A.id = B.fin_aviso_credito_id' )
        ->where('A.status=1')
        ->groupBy('A.id')
        ->orderBy('A.numero')
        ->having('valor_despesa > total_pago')
        ->andHaving('valor_recebido > 0');


    $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30000000,
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
    public function saldoAfavorNCPerson($params)
    {

    $query = new Query;
    $query->select(['dsp_person_id'=>'C.id','personName'=>'C.nome','valor_recebido'=>'A.valor','valor_despesa'=>'SUM(B.valor)','total_pago'=>'SUM(valor_pago)'])
        ->from('fin_aviso_credito A')
        ->leftJoin('fin_despesa B','A.id = B.fin_aviso_credito_id' )
        ->leftJoin('dsp_person C','C.id = A.dsp_person_id' )
        ->groupBy('C.id')
        ->orderBy('C.nome')
        ->having('valor_despesa > total_pago')
        ->andHaving('valor_recebido > 0');


    $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30000000,
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



}
