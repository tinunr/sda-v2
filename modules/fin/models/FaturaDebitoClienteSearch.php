<?php

namespace app\modules\fin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\FaturaDebitoCliente;

/**
 * FaturaDebitoClienteSearch represents the model behind the search form about `backend\models\FaturaDebitoCliente`.
 */
class FaturaDebitoClienteSearch extends FaturaDebitoCliente
{
    public $globalSearch;
    public $dsp_person_id;
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
            [['id', 'dsp_person_id', 'bas_ano_id'], 'integer'],
            [['data', 'globalSearch'], 'safe'],

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
        $query = FaturaDebitoCliente::find()
            //    ->joinWith('processo')
            ->leftJoin('dsp_processo', 'dsp_processo.id=fin_fatura_debito_cliente.dsp_processo_id')
            ->orderBy('numero DESC');

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


        $query->orFilterWhere(['like', 'fin_fatura_debito_cliente.numero', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_debito_cliente.nord', $this->globalSearch])
            ->orFilterWhere(['like', 'dsp_processo.numero', $this->globalSearch]);
        $query->andFilterWhere([
            'fin_fatura_debito_cliente.dsp_person_id' => $this->dsp_person_id,
            'fin_fatura_debito_cliente.bas_ano_id' => $this->bas_ano_id,
        ]);

        return $dataProvider;
    }

    /**
     * Relatório de FATURA provisória
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchReport($params)
    {
        $query = FaturaDebitoCliente::find()
            ->joinWith(['processo', 'receita'])
            ->leftJoin('dsp_nord', 'dsp_processo.id=dsp_nord.dsp_processo_id')
            ->where(['fin_fatura_debito_cliente.status' => 1])
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
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->orFilterWhere(['like', 'fin_fatura_debito_cliente.numero', $this->globalSearch])
            ->orFilterWhere(['like', 'dsp_nord.id', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_debito_cliente.valor', $this->globalSearch])
            ->orFilterWhere(['like', 'fin_fatura_debito_cliente.send', $this->send])
            ->orFilterWhere(['like', 'dsp_processo.numero', $this->globalSearch]);

        $query->andFilterWhere([
            'fin_fatura_debito_cliente.dsp_person_id' => $this->dsp_person_id,
            'fin_fatura_debito_cliente.bas_ano_id' => $this->bas_ano_id,
        ]);
        $query->andFilterWhere(['>=', 'fin_fatura_debito_cliente.data', $this->beginDate]);
        $query->andFilterWhere(['<=', 'fin_fatura_debito_cliente.data', $this->endDate]);

        return $dataProvider;
    }
}
