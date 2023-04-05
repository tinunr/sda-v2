<?php

namespace app\modules\dsp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\dsp\models\Processo;
use yii\db\Query;

/**
 * ZonaSearch represents the model behind the search form of `app\models\Zona`.
 */
class ProcessoSearch extends Processo
{
    const  COM_PL = 1;
    const  COM_PL_POR_RESOLVER = 2;
    const  COM_PL_RESOLVIDO = 3;
    const  REGISTADO = 4;
    const  LIQUIDADO = 5;
    const  RECEITADO = 6;

    public $globalSearch;
    public $comPl;
    public $porResponsavel;
    public $dataInicio;
    public $dataFim;
    public $porCliente;
    public $por_person;
    public $documento_id;
    public $bas_formato_id;
    public $agrupar_por;
    public $dsp_desembaraco_id;
    public $filter_opctions;


    /**
     * @inheritdoc
     */
    public function filtersOpctions()
    {
        return [
            self::COM_PL => 'Com PL',
            self::COM_PL_POR_RESOLVER => 'Com PL por resolver',
            self::COM_PL_RESOLVIDO => 'Com PL Resolvido',
            self::REGISTADO => 'Registado',
            self::LIQUIDADO => 'Liquidado',
            self::RECEITADO => 'Receitado',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numero', 'user_id', 'bas_ano_id', 'dsp_person_id', 'nord'], 'integer'],
            [['globalSearch', 'status', 'comPl', 'dataInicio', 'dataFim', 'porResponsavel', 'porCliente', 'descricao', 'bas_formato_id', 'agrupar_por', 'dsp_desembaraco_id', 'status_financeiro_id'], 'safe'],
            ['filter_opctions', 'each', 'rule' => ['integer']],
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
        $query = Processo::find()
            ->joinWith('nord')
            ->joinWith('pedidoLevantamento')
            ->leftJoin('dsp_despacho', 'dsp_despacho.bas_ano_id= dsp_nord.bas_ano_id  AND dsp_despacho.nord= dsp_nord.numero AND dsp_despacho.dsp_desembaraco_id=dsp_nord.dsp_desembaraco_id')
            ->orderBy('numero DESC');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 80,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->orFilterWhere(['dsp_processo.numero' => $this->globalSearch])
            ->orFilterWhere(['like', 'descricao', $this->descricao])
            ->orFilterWhere(['dsp_nord.id' => $this->globalSearch]);
        if (!empty($this->filter_opctions)) {
            if (in_array(self::COM_PL, $this->filter_opctions)) {
                $query->orFilterWhere(['>', 'dsp_pedido_levantamento.id', 0]);
            }
            if (in_array(self::COM_PL_POR_RESOLVER, $this->filter_opctions)) {
                $query->andFilterWhere(['>', 'n_levantamento', 0])
                    ->andFilterWhere(['dsp_processo.status' => [1, 2, 3, 4, 5, 7]]);
            }
            if (in_array(self::COM_PL_RESOLVIDO, $this->filter_opctions)) {
                $query->andFilterWhere(['>', 'n_levantamento', 0])
                    ->andFilterWhere(['dsp_processo.status' => [6, 8, 9]]);
            }
            if (in_array(self::REGISTADO, $this->filter_opctions)) {
                $query->andFilterWhere(['>', 'dsp_despacho.id', 0]);
            }
            if (in_array(self::LIQUIDADO, $this->filter_opctions)) {
                $query->andFilterWhere(['>', 'dsp_despacho.numero_liquidade', 0]);
            }
            if (in_array(self::RECEITADO, $this->filter_opctions)) {
                $query->andFilterWhere(['>', 'dsp_despacho.n_receita', 0]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'dsp_processo.status' => $this->status,
            'dsp_processo.dsp_person_id' => $this->dsp_person_id,
            'dsp_processo.bas_ano_id' => $this->bas_ano_id,
            'dsp_processo.status_financeiro_id' => $this->status_financeiro_id,
        ]);

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     * processo com pedido de levantamento por resolver
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchProcessoPlPorResover($params)
    {
        $query = Processo::find()
            ->joinWith('nord')
            ->joinWith('pedidoLevantamento')
            ->leftJoin('dsp_despacho', 'dsp_despacho.bas_ano_id= dsp_nord.bas_ano_id  AND dsp_despacho.nord= dsp_nord.numero AND dsp_despacho.dsp_desembaraco_id=dsp_nord.dsp_desembaraco_id')
            ->where(['>', 'dsp_pedido_levantamento.id', 0])
            ->andWhere(['dsp_despacho.n_receita' => null])
            ->orderBy('numero DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->orFilterWhere(['dsp_processo.numero' => $this->globalSearch])
            ->orFilterWhere(['like', 'descricao', $this->globalSearch])
            ->orFilterWhere(['dsp_nord.id' => $this->globalSearch]);

        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'dsp_despacho.dsp_desembaraco_id' => $this->dsp_desembaraco_id,
            'dsp_processo.dsp_person_id' => $this->dsp_person_id,
            'dsp_processo.bas_ano_id' => $this->bas_ano_id,
            'dsp_processo.status_financeiro_id' => $this->status_financeiro_id,
        ]);

        $query->andFilterWhere(['>=', 'dsp_processo.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'dsp_processo.data', $this->dataFim]);

        return $dataProvider;
    }


    /**
     * Relatório saldo a favor do cilente por processo
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function relatorio($params)
    {

        $query1 = (new \yii\db\Query())
            ->select([
                'A.id',
                'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)",
                'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`D`.`numero`, '/', `D`.`bas_ano_id`) SEPARATOR ',')",
                'nord' => 'C.id',
                'mercadoria' => 'A.descricao',
                'dsp_person_id' => 'A.nome_fatura',
                'person' => 'B.nome',
                'valor_recebido' => 'SUM(E.valor_recebido)',
                'A.data'
            ])
            ->from('dsp_processo A')
            ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
            ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
            ->leftJoin('fin_fatura_provisoria D', 'A.id=D.dsp_processo_id')
            ->leftJoin('fin_fatura_definitiva F', 'A.id=F.dsp_processo_id AND F.fin_fatura_definitiva_serie =2')
            ->innerJoin('fin_receita E', 'D.id = E.dsp_fataura_provisoria_id OR F.id = E.dsp_fataura_provisoria_id')
            ->where('A.status!=11')
        ->andFilterWhere(['>=', 'A.data', $this->dataInicio])
        ->andFilterWhere(['<=', 'A.data', $this->dataFim])
        ->andFilterWhere(['<=', 'A.dsp_person_id', $this->dsp_person_id])
            ->groupBy('A.id')
            ->orderBy('A.data');

        $query2 = (new \yii\db\Query())
            ->select([
                'Z.id',
                'Z.numero',
                'Z.fatura_provisorias',
                'Z.nord',
                'Z.mercadoria',
                'Z.dsp_person_id',
                'Z.person',
                'Z.data',
                'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido)+Z.valor_recebido ELSE Z.valor_recebido END'
            ])
            ->from(['Z' => $query1])
            ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = Z.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
            ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
            ->groupBy('Z.id')
            ->orderBy('Z.data');


        $query3 = (new \yii\db\Query())
            ->select([
                'X.id',
                'X.numero',
                'X.fatura_provisorias',
                'X.nord',
                'X.mercadoria',
                'X.dsp_person_id',
                'X.person',
                'X.data',
                'X.valor_recebido',
                'total_pago' => 'CASE WHEN SUM(A.valor_pago) > 0 THEN SUM(A.valor_pago) ELSE 0 END'
            ])
            ->from(['X' => $query2])
            ->leftJoin('fin_despesa A', 'A.status = 1 AND A.of_acount=0 AND A.dsp_processo_id = X.id')
            ->where('X.valor_recebido > 0')
            ->groupBy('X.id')
            ->orderBy('X.data');


        $query4 = (new \yii\db\Query())
            ->select([
                'W.id',
                'W.numero',
                'W.fatura_provisorias',
                'W.nord',
                'W.mercadoria',
                'W.dsp_person_id',
                'W.person',
                'W.data',
                'W.valor_recebido',
                'valor_despesa' => 'SUM(B.valor)', 
                'W.total_pago'
            ])
            ->from(['W' => $query3])
            ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
            ->innerJoin('fin_receita C', 'A.id = C.dsp_fataura_provisoria_id AND C.valor_recebido > 0')
            ->leftJoin('fin_fatura_provisoria_item B', 'A.id = B.dsp_fatura_provisoria_id ')
            ->innerJoin('dsp_item D', 'D.id = B.dsp_item_id AND D.dsp_person_id != 1')
            ->groupBy('W.id')
            ->orderBy('W.data');

        $query = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero',
                'P.fatura_provisorias',
                'P.nord',
                'P.mercadoria',
                'P.dsp_person_id',
                'P.person',
                'P.data',
                'P.valor_recebido',
                'P.valor_despesa',
                'total_pago' => 'CASE WHEN SUM(A.valor) > 0 THEN SUM(A.valor)+P.total_pago ELSE P.total_pago END'
            ])
            ->from(['P' => $query4])
            ->leftJoin('fin_nota_credito A', 'A.status = 1 AND A.dsp_processo_id = P.id')
            ->orderBy(['P.id'=> SORT_DESC])
            ->groupBy('P.id')
            ->having('valor_despesa > total_pago');



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
            'P.dsp_person_id' => $this->dsp_person_id,
        ]);
        $query->andFilterWhere(['>=', 'P.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'P.data', $this->dataFim]);



        return $dataProvider;
    }



    
    /**
     * Relatório saldo a favor do cilente por processo 2
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function relatorioAfavorAdever($params)
    {

        /**
         * Valor recebido das fatuas provisoria / Fatura definitiva especial
         */
        $query1 = (new \yii\db\Query())
            ->select([
                'A.id',
                'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)",
                'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`D`.`numero`, '/', `D`.`bas_ano_id`) SEPARATOR ',')",
                'nord' => 'C.id',
                'mercadoria' => 'A.descricao',
                'dsp_person_id' => 'A.nome_fatura',
                'person' => 'B.nome',
                'valor_recebido' => 'SUM(E.valor_recebido)',
                'A.data'
            ])
            ->from('dsp_processo A')
            ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
            ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
            ->leftJoin('fin_fatura_provisoria D', 'A.id=D.dsp_processo_id AND D.status =1')
            ->leftJoin('fin_fatura_definitiva F', 'A.id=F.dsp_processo_id AND F.fin_fatura_definitiva_serie =2 AND F.status =1')
            ->innerJoin('fin_receita E', 'D.id = E.dsp_fataura_provisoria_id OR F.id = E.dsp_fataura_provisoria_id')
            ->where('A.status!=11')
            ->groupBy('A.id')
            ->orderBy('A.data');
        /**
         * Valor recebido da adiantamento onde fin_recebimento_tipo_id = 4 de reenbolço a favor do cliente
         */
        $query2 = (new \yii\db\Query())
            ->select([
                'Z.id',
                'Z.numero',
                'Z.fatura_provisorias',
                'Z.nord',
                'Z.mercadoria',
                'Z.dsp_person_id',
                'Z.person',
                'Z.data',
                'Z.valor_recebido',
                'reembolco_afavor_cliente' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido) ELSE 0 END'
            ])
            ->from(['Z' => $query1])
            ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = Z.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
            ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
            ->groupBy('Z.id')
            ->orderBy('Z.data');

        /**
         * pagamento das despesas
         * conceito pagamento das despesas
         * 
         **/        
        $query3 = (new \yii\db\Query())
            ->select([
                'X.id',
                'X.numero',
                'X.fatura_provisorias',
                'X.nord',
                'X.mercadoria',
                'X.dsp_person_id',
                'X.person',
                'X.data',
                'X.valor_recebido',
                'X.reembolco_afavor_cliente',
                'valor_pago' => 'CASE WHEN SUM(A.valor_pago) > 0 THEN SUM(A.valor_pago) ELSE 0 END'
            ])
            ->from(['X' => $query2])
            ->leftJoin('fin_despesa A', 'A.status = 1 AND A.of_acount=0 AND A.dsp_processo_id = X.id')
            // ->where('X.valor_recebido > 0')
            ->groupBy('X.id')
            ->orderBy('X.data');

        /***
         * Valor das despesas
         **/        
        $query4 = (new \yii\db\Query())
            ->select([
                'W.id',
                'W.numero',
                'W.fatura_provisorias',
                'W.nord',
                'W.mercadoria',
                'W.dsp_person_id',
                'W.person',
                'W.data',
                'W.valor_recebido',
                'W.reembolco_afavor_cliente',
                'valor_despesa' => 'SUM(B.valor)', 
                'W.valor_pago'
            ])
            ->from(['W' => $query3])
            ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
            ->innerJoin('fin_receita C', 'A.id = C.dsp_fataura_provisoria_id AND C.valor_recebido > 0')
            ->leftJoin('fin_fatura_provisoria_item B', 'A.id = B.dsp_fatura_provisoria_id ')
            ->innerJoin('dsp_item D', 'D.id = B.dsp_item_id AND D.dsp_person_id != 1')
            ->groupBy('W.id')
            ->orderBy('W.data');
        /**
         * Valor pagao
         * adiconar valor de aviso de credito
         */
        $query5 = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero',
                'P.fatura_provisorias',
                'P.nord',
                'P.mercadoria',
                'P.dsp_person_id',
                'P.person',
                'P.data',
                'valor_recebido'=>'(CASE WHEN P.valor_despesa <= P.valor_recebido THEN P.valor_despesa ELSE P.valor_recebido END)',
                'P.valor_despesa',
                'P.reembolco_afavor_cliente',
                'P.valor_pago',
                'aviso_credito' => 'CASE WHEN SUM(A.valor) > 0 THEN SUM(A.valor) ELSE 0 END'
            ])
            ->from(['P' => $query4])
            ->leftJoin('fin_nota_credito A', 'A.status = 1 AND A.dsp_processo_id = P.id')
            ->orderBy(['P.id'=> SORT_DESC])
            ->groupBy('P.id');



            $query = (new \yii\db\Query())
            ->select([
                'PP.id',
                'PP.numero',
                'PP.fatura_provisorias',
                'PP.nord',
                'PP.mercadoria',
                'PP.dsp_person_id',
                'PP.person',
                'PP.data',
                'PP.valor_recebido',
                'PP.valor_despesa',
                'PP.reembolco_afavor_cliente',
                'PP.valor_pago',
                'PP.aviso_credito',
                'nota_credito' => 'CASE WHEN SUM(A.valor) > 0 THEN SUM(A.valor) ELSE 0 END',
                'saldo'=>'PP.valor_recebido + PP.reembolco_afavor_cliente - (CASE WHEN SUM(A.valor) > 0 THEN SUM(A.valor) ELSE 0 END) - PP.valor_pago - PP.aviso_credito'
            ])
            ->from(['PP' => $query5])
            ->leftJoin('fin_aviso_credito A', 'A.status = 1 AND A.dsp_processo_id = PP.id')
            ->orderBy(['PP.id'=> SORT_DESC])
            ->groupBy('PP.id')
            ->having('saldo != 0');

// print_r($query->createCommand()->rawSql);die();

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
            'PP.dsp_person_id' => $this->dsp_person_id,
        ]);
        $query->andFilterWhere(['>=', 'PP.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'PP.data', $this->dataFim]);



        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function relatorioPerson($params)
    {



        $subQuery = new Query;
        $subQuery->select(['sum(BB.valor) as honorario'])
            ->from('fin_fatura_provisoria_item BB')
            ->leftJoin('dsp_item CC', 'BB.dsp_item_id = CC.id')
            ->where('B.id = BB.dsp_fatura_provisoria_id')
            ->andWhere('CC.dsp_person_id = 1');

        $query = new Query;
        $query->select(['dsp_person_id' => 'P.id', 'personName' => 'P.nome', "CONCAT(A.numero, '/', A.bas_ano_id) AS numero", 'nord' => 'E.id', 'mercadoria' => 'A.descricao', 'valor_recebido' => 'SUM(C.valor)', 'honorario' => $subQuery, 'valor_despesa' => 'SUM(D.valor)', 'total_pago' => 'SUM(D.valor_pago)'])
            ->from('dsp_processo A')
            ->leftJoin('fin_fatura_provisoria B', 'A.id = B.dsp_processo_id')
            ->leftJoin('fin_fatura_definitiva F', 'A.id=F.dsp_processo_id AND F.fin_fatura_definitiva_serie = 2')
            ->leftJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id OR F.id = E.dsp_fataura_provisoria_id')
            ->leftJoin('fin_despesa D', 'A.id = D.dsp_processo_id')
            ->leftJoin('dsp_nord E', 'A.id = E.dsp_processo_id')
            ->leftJoin('dsp_person P', 'P.id = A.dsp_person_id')
            ->orderBy('P.id')
            ->groupBy('P.nome')
            ->having('valor_despesa > total_pago');


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
     *  Relatório de listagem de valores a pagarou a dever do cliente por processo
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function saldoAdeverPr($params)
    {

        /**
         * Listar todos processos com valor de despesa pago maior deo que zerro 
         * [valor_pago > 0] 
         * of_acount=0 porque existe outro
         */
        $query1 = (new \yii\db\Query())
            ->select([
                'A.id', 
                'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)", 
                'nord' => 'C.id', 
                'mercadoria' => 'A.descricao', 
                'dsp_person_id' => 'A.nome_fatura', 
                'person' => 'B.nome', 
                'A.data', 
                'total_pago' => 'CASE WHEN SUM(D.valor_pago) > 0 THEN SUM(D.valor_pago) ELSE 0 END'
            ])
            ->from('dsp_processo A')
            ->innerJoin('fin_despesa D', 'D.status = 1 AND D.of_acount=0 AND D.dsp_processo_id = A.id')
            ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
            ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
            ->where('A.status!=11')
            ->groupBy('A.id')
            ->having('total_pago > 0');

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
            ->from(['Z' => $query1])
            ->leftJoin('fin_despesa A', 'A.status = 1 AND A.of_acount=0 AND A.dsp_processo_id = Z.id')
            ->groupBy('Z.id');

 
        /**
         * Calcular valor recebido atravez da fatura provisória
         */
        $query3 = (new \yii\db\Query())
            ->select([
                'W.id', 
                'W.numero', 
                'W.nord',
                'W.mercadoria',
                'W.person', 
                'W.data', 
                'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido) ELSE 0 END', 
                'W.valor_despesa', 
                'W.total_pago', 
                'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
            ])
            ->from(['W' => $query2])
            ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
            ->leftJoin('fin_receita B', 'B.dsp_fataura_provisoria_id = A.id')
            ->groupBy('W.id');

         /**
         * Calcular valor recebido atravez da fatura definitiva especial
         */
        $query4 = (new \yii\db\Query())
            ->select([
                'WW.id', 
                'WW.numero', 
                'WW.nord', 
                'WW.mercadoria', 
                'WW.person', 
                'WW.data', 
                'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido) + WW.valor_recebido ELSE WW.valor_recebido END', 
                'WW.valor_despesa',
                'WW.total_pago', 
                'WW.fatura_provisorias', 
                'fatura_definitivas' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
            ])
            ->from(['WW' => $query3])
            ->leftJoin('fin_fatura_definitiva A', 'A.dsp_processo_id = WW.id AND A.status = 1 AND A.fin_fatura_definitiva_serie =2')
            ->leftJoin('fin_receita B', 'B.fin_fataura_definitiva_id = A.id')
            ->groupBy('WW.id');
        

        /**
         * Valor recebido via reembolço
         **/    
        $query = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido)+P.valor_recebido ELSE P.valor_recebido END', 
                'P.valor_despesa', 
                'P.total_pago', 
                'P.fatura_provisorias',
                'P.fatura_definitivas'
            ])
            ->from(['P' => $query4])
            ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = P.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
            ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC])
            ->having('valor_recebido < total_pago');

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
            'P.dsp_person_id' => $this->dsp_person_id,
        ]);
        $query->andFilterWhere(['>=', 'P.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'P.data', $this->dataFim]);



        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function saldoAdeverNew($params)
    {

        /**
         * Listar todos processos com valor de despesa pago maior deo que zerro 
         * [valor_pago > 0] 
         * of_acount=0 porque existe outro
         */
        $query1 = (new \yii\db\Query())
            ->select([
                'A.id', 
                'numero' => "CONCAT(A.numero, '/', A.bas_ano_id)", 
                'nord' => 'C.id', 
                'mercadoria' => 'A.descricao', 
                'dsp_person_id' => 'A.nome_fatura', 
                'person' => 'B.nome', 
                'A.data', 
                'total_pago' => 'CASE WHEN SUM(D.valor_pago) > 0 THEN SUM(D.valor_pago) ELSE 0 END'
            ])
            ->from('dsp_processo A')
            ->innerJoin('fin_despesa D', 'D.status = 1 AND D.of_acount=0 AND D.dsp_processo_id = A.id')
            ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
            ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
            ->where('A.status!=11')
            ->groupBy('A.id') ;

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
            ->from(['Z' => $query1])
            ->leftJoin('fin_despesa A', 'A.status = 1 AND A.of_acount=0 AND A.dsp_processo_id = Z.id')
            ->where(['>','total_pago',0])
            ->groupBy('Z.id');

 
        /**
         * Calcular valor recebido atravez da fatura provisória
         */
        $query3 = (new \yii\db\Query())
            ->select([
                'W.id', 
                'W.numero', 
                'W.nord',
                'W.mercadoria',
                'W.person', 
                'W.data', 
                'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido) ELSE 0 END', 
                'W.valor_despesa', 
                'W.total_pago', 
                'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
            ])
            ->from(['W' => $query2])
            ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
            ->leftJoin('fin_receita B', 'B.dsp_fataura_provisoria_id = A.id')
            ->groupBy('W.id');

         /**
         * Calcular valor recebido atravez da fatura definitiva especial
         */
        $query4 = (new \yii\db\Query())
            ->select([
                'WW.id', 
                'WW.numero', 
                'WW.nord', 
                'WW.mercadoria', 
                'WW.person', 
                'WW.data', 
                'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido) + WW.valor_recebido ELSE WW.valor_recebido END', 
                'WW.valor_despesa',
                'WW.total_pago', 
                'fatura_provisorias' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
            ])
            ->from(['WW' => $query3])
            ->leftJoin('fin_fatura_definitiva A', 'A.dsp_processo_id = WW.id AND A.status = 1 AND A.fin_fatura_definitiva_serie =2')
            ->leftJoin('fin_receita B', 'B.fin_fataura_definitiva_id = A.id')
            ->groupBy('WW.id');
                
            
            
        

        /**
         * Valor recebido via reembolço
         **/    
        $query = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'valor_recebido' => 'CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido)+P.valor_recebido ELSE P.valor_recebido END', 
                'P.valor_despesa', 
                'P.total_pago', 
                'P.fatura_provisorias',
                'saldo'=>'CASE WHEN (CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido)+P.valor_recebido ELSE P.valor_recebido END) < P.valor_despesa THEN P.total_pago - (CASE WHEN SUM(B.valor_recebido) > 0 THEN SUM(B.valor_recebido)+P.valor_recebido ELSE P.valor_recebido END)   ELSE  P.total_pago - P.valor_despesa END'
            ])
            ->from(['P' => $query4])
            ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = P.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
            ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC])
            ->having('saldo > 0');

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
            'P.dsp_person_id' => $this->dsp_person_id,
        ]);
        $query->andFilterWhere(['>=', 'P.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'P.data', $this->dataFim]);



        return $dataProvider;
    }



    /**
     * Relatório de listagem de valores a pagarou a dever do cliente por processo
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function saldoAdeverPrPerson($params)
    {

        $subQuery = new Query;
        $subQuery->select([
            'valor_recebido' => new \yii\db\Expression("CASE WHEN  sum(C.valor_recebido)>0 THEN sum(C.valor_recebido) ELSE 0 END")
        ])
            ->from('fin_fatura_provisoria B')
            ->leftJoin('fin_receita C', 'B.id = C.dsp_fataura_provisoria_id')
            ->where('A.id = B.dsp_processo_id')
            ->andWhere('B.status = 1');


        $query = new Query;
        $query->select([
            'dsp_person_id' => 'P.id',
            'personName' => 'P.nome',
            'nord' => 'E.id',
            'mercadoria' => 'A.descricao',
            'valor_recebido' => $subQuery,
            'valor_despesa' => 'SUM(D.valor)',
            'total_pago' => 'SUM(D.valor_pago)',
            'nord' => 'E.id'
        ])
            ->from('dsp_processo A')
            ->leftJoin('fin_despesa D', 'A.id = D.dsp_processo_id AND D.of_acount=0')
            ->leftJoin('dsp_nord E', 'A.id = E.dsp_processo_id ')
            ->leftJoin('dsp_person P', 'P.id = A.dsp_person_id')
            ->where('D.status=1')
            ->orderBy('P.id')
            ->groupBy('P.nome')
            ->having('valor_recebido < total_pago')
            ->andHaving('total_pago > 0');

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
    public function processo($params)
    {

        $query = Processo::find()->orderBy('status');

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
            'user_id' => $this->user_id,
            'status' => $this->status,
            'dsp_person_id' => $this->dsp_person_id,
        ]);
        $query->andFilterWhere(['>=', 'data', $this->dataInicio])
            ->andFilterWhere(['<=', 'data', $this->dataFim]);
        if ($this->comPl == 0) {
            $query->andFilterWhere(['>', 'n_levantamento', $this->comPl]);
        }
        if ($this->comPl == 1) {
            $query->andFilterWhere(['>', 'n_levantamento', $this->comPl])
                ->andFilterWhere(['status' => [1, 2, 3, 4, 5, 7]]);
        }
        if ($this->comPl == 2) {
            $query->andFilterWhere(['>', 'n_levantamento', $this->comPl])
                ->andFilterWhere(['status' => [6, 8, 9]]);
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
    public function responsavel($params)
    {

        $query = new Query;
        $query->select(['user.id', 'user.name'])
            ->from('dsp_processo')
            ->join('LEFT JOIN', 'user', 'user.id = dsp_processo.user_id')
            ->orderBy('user.name')
            ->groupBy(['user.id']);


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
            'dsp_processo.user_id' => $this->user_id,
            'dsp_processo.status' => $this->status,
            'dsp_processo.dsp_person_id' => $this->dsp_person_id,
        ]);
        $query->andFilterWhere(['>=', 'dsp_processo.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'dsp_processo.data', $this->dataFim]);
        if ($this->comPl == 0) {
            $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl]);
        }
        if ($this->comPl == 1) {
            $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl])
                ->andFilterWhere(['dsp_processo.status' => [1, 2, 3, 4, 5, 7]]);
        }
        if ($this->comPl == 2) {
            $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl])
                ->andFilterWhere(['dsp_processo.status' => [6, 8, 9]]);
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
    public function person($params)
    {

        $query = new Query;
        $query->select(['dsp_person.id', 'dsp_person.nome'])
            ->from('dsp_processo')
            ->join('LEFT JOIN', 'dsp_person', 'dsp_person.id=dsp_processo.dsp_person_id')
            ->orderBy('dsp_person.nome')
            ->groupBy(['dsp_person.id']);


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
            'dsp_processo.user_id' => $this->user_id,
            'dsp_processo.status' => $this->status,
            'dsp_processo.dsp_person_id' => $this->dsp_person_id,
        ]);
        $query->andFilterWhere(['>=', 'dsp_processo.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'dsp_processo.data', $this->dataFim]);
        if ($this->comPl == 0) {
            $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl]);
        }
        if ($this->comPl == 1) {
            $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl])
                ->andFilterWhere(['dsp_processo.status' => [1, 2, 3, 4, 5, 7]]);
        }
        if ($this->comPl == 2) {
            $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl])
                ->andFilterWhere(['dsp_processo.status' => [6, 8, 9]]);
        }



        return $dataProvider;
    }
}
