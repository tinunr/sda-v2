<?php

namespace app\modules\cnt\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\models\Ano;
use yii\db\Query;
use yii\db\Expression;
/**
 * RazaoItemSearch represents the model behind the search form of `app\models\RazaoItem`.
 */
class RazaoItemSearch extends RazaoItem
{
    /**
     * {@inheritdoc}
     */

     public $globalSearch;
     public $dataInicio;
     public $dataFim;
     public $cnt_diario_id;
     public $cnt_documento_id;
     public $bas_ano_id;
     public $bas_mes_id;
     public $bas_formato_id;
     public $bas_template_id;
     public $begin_ano;
     public $end_ano;
     public $begin_mes;
     public $end_mes;
     public $begin_cnt_plano_conta_id;
     public $end_cnt_plano_conta_id;
     public $cnt_plano_terceiro_id;


     
    public function rules()
    {
        return [
            [['id','cnt_razao_id','cnt_plano_conta_id','begin_ano','end_ano'], 'integer'],
            [['globalSearch'], 'safe'],
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
    public function extratoConta($params)
    {
        
        $query = PlanoConta::find()->orderBy('codigo');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10000000,
            ],
        ]);

        $this->load($params);
        // print_r($this);die();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->begin_cnt_plano_conta_id,
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
    public function extratoContaFl($params)
    {
        
        $query = PlanoFluxoCaixa::find()->orderBy('codigo');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
        ]);

        $this->load($params);
        // print_r($this);die();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->cnt_plano_fluxo_caixa_id,
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
    public function extratoFornecedor($params)
    {
        
        $query = new Query();
        $query->select(['C.codigo','descricao'=>'C.descricao','C.cnt_plano_conta_tipo_id','C.cnt_natureza_id','C.cnt_plano_conta_id','D.nome','A.cnt_plano_terceiro_id'])
        ->from('cnt_razao_item A')
        ->leftJoin('cnt_razao B','B.id = A.cnt_razao_id')
        ->leftJoin('cnt_plano_conta C','C.id = A.cnt_plano_conta_id')
        ->leftJoin('dsp_person D','D.id = A.cnt_plano_terceiro_id')
        ->where(['B.status'=>1])
        ->groupBy(['C.codigo', 'A.cnt_plano_terceiro_id'])
        ->orderBy('D.nome');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
        ]);

        $this->load($params);
        // print_r($this);die();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'B.bas_ano_id' => $this->bas_ano_id,
            'B.bas_mes_id' => $this->begin_mes,
            'A.cnt_plano_terceiro_id' => $this->cnt_plano_terceiro_id,
            'A.cnt_plano_fluxo_caixa_id' => $this->cnt_plano_fluxo_caixa_id,
        ]);

        $query->andFilterWhere(['like', 'A.cnt_plano_conta_id', $this->begin_cnt_plano_conta_id.'%', false]);
        if ($this->bas_formato_id ==2) {
            $query->andFilterWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
        }

        // print_r($dataProvider->getModels());die();

        return $dataProvider;
    }



    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function extrato($params)
    {
        
        $query = new Query();
        $query->select(['A.id','A.cnt_plano_conta_id','descricao'=>'A.descricao','B.cnt_diario_id','debito'=>new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='D' THEN A.valor ELSE 0 END"),'credito'=>new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='C' THEN A.valor ELSE 0 END"),'cnt_documento_id','data'=>'B.documento_origem_data','B.cnt_diario_id','num_doc'=>'B.documento_origem_numero','terceiro'=>'A.cnt_plano_terceiro_id'])
        ->from('cnt_razao_item A')
        ->leftJoin('cnt_razao B','B.id = A.cnt_razao_id')
        ->where(['B.status'=>1])
        ->orderBy('B.documento_origem_data');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10000000,
            ],
        ]);

        $this->load($params);
        // print_r($this);die();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'YEAR(B.documento_origem_data)' => $this->bas_ano_id,
            'MONTH(B.documento_origem_data)' => $this->begin_mes,
            'A.cnt_plano_terceiro_id' => $this->cnt_plano_terceiro_id,
            'A.cnt_plano_conta_id' => $this->cnt_plano_conta_id,
            'A.cnt_plano_fluxo_caixa_id' => $this->cnt_plano_fluxo_caixa_id,
        ]);

        if ($this->bas_formato_id ==2) {
            $query->andFilterWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
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
    public function extratoFluxoCaixa($params)
    {
        
        $query = new Query();
        $query->select(['A.id','A.cnt_plano_conta_id','descricao'=>'A.descricao','B.cnt_diario_id','debito'=>new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='D' THEN A.valor ELSE 0 END"),'credito'=>new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='C' THEN A.valor ELSE 0 END"),'cnt_documento_id','data'=>'B.documento_origem_data','B.cnt_diario_id','num_doc'=>'B.documento_origem_numero','terceiro'=>'A.cnt_plano_terceiro_id'])
        ->from('cnt_razao_item A')
        ->leftJoin('cnt_razao B','B.id = A.cnt_razao_id')
        ->where(['B.status'=>1])
        ->orderBy('B.documento_origem_data');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10000000,
            ],
        ]);

        $this->load($params);
        // print_r($this);die();

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'YEAR(B.documento_origem_data)' => $this->bas_ano_id,
            'MONTH(B.documento_origem_data)' => $this->begin_mes,
            'A.cnt_plano_terceiro_id' => $this->cnt_plano_terceiro_id,
            'A.cnt_plano_fluxo_caixa_id' => $this->cnt_plano_fluxo_caixa_id,
        ]);

        if ($this->bas_formato_id ==2) {
            $query->andFilterWhere(['>', 'A.cnt_plano_terceiro_id', 0]);
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
    public function balancete($params)
    {
       $query = new Query();
        $query->select(['C.id','descricao'=>'C.descricao','C.cnt_plano_conta_tipo_id','C.cnt_natureza_id','C.cnt_plano_conta_id'])
        ->from('cnt_razao_item A')
        ->leftJoin('cnt_razao B','B.id = A.cnt_razao_id')
        ->leftJoin('cnt_plano_conta C','C.id = A.cnt_plano_conta_id')
        ->where(['B.status'=>1])
        ->groupBy('C.id')
        ->orderBy('C.codigo');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10000000,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        /// grid filtering conditions
        $query->andFilterWhere([
            'YEAR(B.documento_origem_data)' => $this->bas_ano_id,
            'MONTH(B.documento_origem_data)' => $this->begin_mes,
        ]);

        return $dataProvider;
    }
   

}
