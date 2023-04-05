<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\FaturaProformsa;
use yii\db\Query;

/**
 * FaturaProformaSearch represents the model behind the search form about `backend\models\FaturaProforma`.
 */
class FaturaProformaSearch extends FaturaProforma
{
    public $globalSearch;
    public $beginDate;
    public $endDate;
    public $recebido;
    public $dataInicio;
    public $dataFim;
    public $porCliente;
    public $por_person;
    public $documento_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'bas_ano_id', 'dsp_person_id', 'send'], 'integer'],
            [['dataInicio', 'dataFim', 'numero', 'globalSearch', 'processo', 'beginDate', 'endDate', 'recebido'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
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
        $query = FaturaProforma::find()
            //    ->joinWith(['processo','receita'])
            ->leftJoin('dsp_processo', 'dsp_processo.id=fin_fatura_proforma.dsp_processo_id')
            //    ->leftJoin('dsp_nord','dsp_processo.id=dsp_nord.dsp_processo_id')
            ->orderBy('numero DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => ['defaultOrder' => ['data' => SORT_DESC]]
        ]);

        // $dataProvider->sort->attributes['processo'] = [
        //     'asc' => ['dsp_processo.numero' => SORT_ASC],
        //     'desc' => ['dsp_processo.numero' => SORT_DESC],
        // ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->orFilterWhere(['like', 'fin_fatura_proforma.numero', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_proforma.nord', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_proforma.valor', $this->globalSearch])
            ->orFilterWhere(['like', 'dsp_processo.numero', $this->globalSearch]);

        $query->andFilterWhere([
            'fin_fatura_proforma.dsp_person_id' => $this->dsp_person_id,
            'fin_fatura_proforma.bas_ano_id' => $this->bas_ano_id,
            'fin_fatura_proforma.send' => $this->send,
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
    public function searchReport($params)
    {
        $query = FaturaProforma::find()
            ->joinWith(['processo', 'receita'])
            ->leftJoin('dsp_nord', 'dsp_processo.id=dsp_nord.dsp_processo_id')
            ->where(['fin_fatura_proforma.status' => 1])
            ->orderBy('numero DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['data' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['processo'] = [
            'asc' => ['dsp_processo.numero' => SORT_ASC],
            'desc' => ['dsp_processo.numero' => SORT_DESC],
        ];

        $this->load($params);
        // print_r($this);die();
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->orFilterWhere(['like', 'fin_fatura_proforma.numero', $this->globalSearch])
            ->orFilterWhere(['like', 'dsp_nord.id', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_proforma.valor', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_proforma.send', $this->send])
            ->orFilterWhere(['like', 'dsp_processo.numero', $this->globalSearch]);

        $query->andFilterWhere([
            'fin_fatura_proforma.dsp_person_id' => $this->dsp_person_id,
            'fin_fatura_proforma.bas_ano_id' => $this->bas_ano_id,
        ]);
        $query->andFilterWhere(['>=', 'fin_fatura_proforma.data', $this->beginDate]);
        $query->andFilterWhere(['<=', 'fin_fatura_proforma.data', $this->endDate]);
        if ($this->recebido == 'naorecebido') {
            $query->andFilterWhere(['>', 'fin_receita.saldo', 0]);
        }
        if ($this->recebido == 'recebido') {
            $query->andFilterWhere(['<=', 'fin_receita.saldo', 0]);
        }

        // print_r($query->createCommand()->getRawSql());die();
        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function report($params)
    {
        $query = FaturaProforma::find()
            ->joinWith(['processo'])
            ->joinWith(['receita']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['data' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['processo'] = [
            'asc' => ['dsp_processo.numero' => SORT_ASC],
            'desc' => ['dsp_processo.numero' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->orFilterWhere(['like', 'fin_fatura_proforma.numero', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_proforma.nord', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_proforma.valor', $this->globalSearch])
            ->orFilterWhere(['like', 'dsp_processo.numero', $this->globalSearch]);

        $query->andFilterWhere([
            'fin_fatura_proforma.dsp_person_id' => $this->dsp_person_id,
            'fin_fatura_proforma.bas_ano_id' => $this->bas_ano_id,
            'fin_fatura_proforma.send' => $this->send,
        ]);
        $query->andFilterWhere(['>=', 'fin_fatura_proforma.data', $this->beginDate]);
        $query->andFilterWhere(['<=', 'fin_fatura_proforma.data', $this->endDate]);


        if ($this->recebido == 'naorecebido') {
            $query->andFilterWhere(['>', 'fin_receita.saldo', 0]);
        }
        if ($this->recebido == 'recebido') {
            $query->andFilterWhere(['<=', 'fin_receita.saldo', 0]);
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
    public function saldoAfavorFp($params)
    {
        $subQuery = new Query;
        $subQuery->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_proforma_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('A.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id = 1');

        $subQuery2 = new Query;
        $subQuery2->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_proforma_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('A.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id != 1');

        $query = new Query;
        $query->select(["CONCAT(A.numero, '/', A.bas_ano_id) AS numero", 'nord' => 'A.nord', 'mercadoria' => 'A.mercadoria', 'valor_recebido' => 'SUM(C.valor_recebido)', 'honorario' => $subQuery, 'a_cliente' => $subQuery2, 'total_pago' => 'SUM(D.valor_pago)', 'valor_despesa' => 'SUM(D.valor)', 'nord' => 'A.nord'])
            ->from('fin_fatura_proforma A')
            ->leftJoin('fin_receita C', 'A.id = C.dsp_fataura_provisoria_id')
            ->leftJoin('fin_despesa D', 'A.id = D.dsp_fatura_provisoria_id')
            ->orderBy('A.id')
            ->groupBy('A.numero')
            ->having('valor_despesa > total_pago ')
            ->andHaving('valor_recebido > 0 ');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        }
        $query->andFilterWhere([
            'A.dsp_person_id' => $this->dsp_person_id,
            'A.bas_ano_id' => $this->bas_ano_id,
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
    public function saldoAfavorFpPerson($params)
    {
        $subQuery = new Query;
        $subQuery->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_proforma_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('A.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id = 1');

        $subQuery2 = new Query;
        $subQuery2->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_proforma_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('A.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id != 1');

        $query = new Query;
        $query->select(['dsp_person_id' => 'P.id', 'personName' => 'P.nome', "CONCAT(A.numero, '/', A.bas_ano_id) AS numero", 'nord' => 'A.nord', 'mercadoria' => 'A.mercadoria', 'valor_recebido' => 'SUM(C.valor_recebido)', 'honorario' => $subQuery, 'a_cliente' => $subQuery2, 'total_pago' => 'SUM(D.valor_pago)', 'valor_despesa' => 'SUM(D.valor)'])
            ->from('fin_fatura_proforma A')
            ->leftJoin('fin_receita C', 'A.id = C.dsp_fataura_provisoria_id')
            ->leftJoin('fin_despesa D', 'A.id = D.dsp_fatura_provisoria_id')
            ->leftJoin('dsp_person P', 'A.dsp_person_id = P.id')
            ->orderBy('P.nome')
            ->groupBy('P.id')
            ->having('valor_despesa > total_pago ')
            ->andHaving('valor_recebido > 0 ');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        }
        $query->andFilterWhere([
            'A.dsp_person_id' => $this->dsp_person_id,
            'A.bas_ano_id' => $this->bas_ano_id,
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
    public function saldoAdeverFp($params)
    {

        $subQuery = new Query;
        $subQuery->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_proforma_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('A.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id = 1');

        $subQuery2 = new Query;
        $subQuery2->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_proforma_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('A.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id != 1');

        $query = new Query;
        $query->select(["CONCAT(A.numero, '/', A.bas_ano_id) AS numero", 'nord' => 'A.nord', 'mercadoria' => 'A.mercadoria', 'valor_areceber' => 'SUM(C.valor)', 'valor_recebido' => 'SUM(C.valor_recebido)', 'honorario' => $subQuery, 'a_cliente' => $subQuery2, 'total_pago' => 'SUM(D.valor_pago)', 'valor_despesa' => 'SUM(D.valor)', 'processo' => "CONCAT(E.numero, '/', E.bas_ano_id)"])
            ->from('fin_fatura_proforma A')
            ->leftJoin('fin_receita C', 'A.id = C.dsp_fataura_provisoria_id')
            ->leftJoin('fin_despesa D', 'A.id = D.dsp_fatura_provisoria_id')
            ->leftJoin('dsp_processo E', 'E.id = A.dsp_processo_id')
            ->orderBy('A.id')
            ->groupBy('A.numero')
            ->having('valor_recebido < total_pago ');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        }
        $query->andFilterWhere([
            'A.dsp_person_id' => $this->dsp_person_id,
            'A.bas_ano_id' => $this->bas_ano_id,
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
    public function saldoAdeverFpPerson($params)
    {
        $subQuery = new Query;
        $subQuery->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_proforma_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('A.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id = 1');

        $subQuery2 = new Query;
        $subQuery2->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_proforma_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('A.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id != 1');

        $query = new Query;
        $query->select(['dsp_person_id' => 'P.id', 'personName' => 'P.nome', "CONCAT(A.numero, '/', A.bas_ano_id) AS numero", 'nord' => 'A.nord', 'mercadoria' => 'A.mercadoria', 'valor_recebido' => 'SUM(C.valor_recebido)', 'honorario' => $subQuery, 'a_cliente' => $subQuery2, 'total_pago' => 'SUM(D.valor_pago)', 'valor_despesa' => 'SUM(D.valor)'])
            ->from('fin_fatura_proforma A')
            ->leftJoin('fin_receita C', 'A.id = C.dsp_fataura_provisoria_id')
            ->leftJoin('fin_despesa D', 'A.id = D.dsp_fatura_provisoria_id')
            ->leftJoin('dsp_person P', 'A.dsp_person_id = P.id')
            ->orderBy('P.id')
            ->groupBy('P.nome')
            ->having('valor_recebido < total_pago ');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        }
        $query->andFilterWhere([
            'A.dsp_person_id' => $this->dsp_person_id,
            'A.bas_ano_id' => $this->bas_ano_id,
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
    public function honorario($params)
    {
        $query = new Query;
        $query->select([
            'numero' => "CONCAT(B.numero, '/', B.bas_ano_id)", 'nord' => 'B.nord', 'A.valor', 'B.data', 'D.nome', 'recebido' => "(CASE WHEN C.valor_recebido > 0 THEN  'Recebido' ELSE 'Não Recebido'  END)", 'numero_recebimento' => "GROUP_CONCAT(CONCAT(F.numero, '/', F.bas_ano_id))", 'data_recebimento' => 'F.data', 'B.mercadoria'
        ])->distinct()
            ->from('fin_fatura_proforma_item A')
            ->leftJoin('fin_fatura_proforma B', 'B.id = A.dsp_fatura_provisoria_id')
            ->leftJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id')
            ->leftJoin('dsp_person D', 'D.id = B.dsp_person_id')
            ->leftJoin('fin_recebimento_item E', 'E.fin_receita_id = C.id')
            ->leftJoin('fin_recebimento F', 'F.id = E.fin_recebimento_id')
            ->where(['A.dsp_item_id' => 1002])
            ->andWhere(['B.status' => 1])
            ->groupBy('A.dsp_fatura_provisoria_id')
            ->orderBy('B.data DESC');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['B.dsp_person_id' => $this->dsp_person_id]);
        $query->andFilterWhere(['>=', 'B.data', $this->beginDate]);
        $query->andFilterWhere(['<=', 'B.data', $this->endDate]);

        $query->andFilterWhere(['>=', 'F.data', $this->dataInicio]);
        $query->andFilterWhere(['<=', 'F.data', $this->dataFim]);

        if ($this->recebido == 1) {
            $query->andFilterWhere(['=', 'C.valor_recebido', 0]);
        }
        if ($this->recebido == 2) {
            $query->andFilterWhere(['>', 'C.valor_recebido', 0]);
        }

        return $dataProvider;
    }
}
