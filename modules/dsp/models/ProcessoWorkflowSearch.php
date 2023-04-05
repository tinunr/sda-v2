<?php

namespace app\modules\dsp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\dsp\models\ProcessoWorkflow;
use app\modules\dsp\models\ProcessoWorkflowStatus;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\Setor;

/**
 * ProcessoWorkflowSearch represents the model behind the search form of `app\models\ProcessoWorkflow`.
 */
class ProcessoWorkflowSearch extends ProcessoWorkflow
{
    public $processo;
    public $globalSearch;
    public $processoStatus;
    public $dataInicio;
    public $dataFim;
    public $bas_ano_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','dsp_setor_id','bas_ano_id'], 'integer'],
            [['globalSearch','descricao','status','processoStatus','processo','dataInicio','dataFim'], 'safe'],

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
        
        $query = ProcessoWorkflow::find()
               ->joinWith('processo')
               ->orderBy('dsp_processo.id desc');

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
            'dsp_processo_workflow.user_id' => Yii::$app->user->identity->id,
            // 'dsp_processo_workflow.status' =>[1,2,4],
            'dsp_processo_workflow.status' =>$this->status,
            // 'dsp_processo.status' =>$this->processoStatus,
            'dsp_processo.numero' =>$this->globalSearch,
            'dsp_processo.bas_ano_id' =>$this->bas_ano_id,
        ]);
        // $query->andFilterWhere(['not in', 'dsp_processo_workflow.dsp_setor_id',[Setor::ARQUIVO_ID,Setor::ARQUIVO_PROVISORIO_ID]]);
        $query->andFilterWhere(['!=', 'dsp_processo.status', ProcessoStatus::STATUS_ANULADO]);
        

        return $dataProvider;
    }

     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchArquivo($params)
    {
        $query = ProcessoWorkflow::find()
                ->joinWith('processo')
                ->orderBy('dsp_processo.numero desc');

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
            'dsp_processo_workflow.dsp_setor_id' => \app\modules\dsp\models\SetorUser::find()->select(['dsp_setor_id'])->where(['user_id'=>Yii::$app->user->identity->id,'dsp_setor_id'=>[setor::ARQUIVO_ID, Setor::ARQUIVO_PROVISORIO_ID]])->asArray(),
            'dsp_processo.numero'=>$this->globalSearch
            // 'dsp_processo_workflow.status' =>[5],
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
    public function searchProcessoSetor($params)
    {
        $query = ProcessoWorkflow::find()
               ->orderBy('prioridade DESC, id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['status' =>[ProcessoWorkflowStatus::DISPONIVEL_SETOR]]);
        $query->andFilterWhere(['>','dsp_setor_id' ,0]);
        if(!Yii::$app->user->can('dsp/processo-workflow/set-prioridade')){
            $query->andFilterWhere([
                'dsp_setor_id' => \app\modules\dsp\models\SetorUser::find()->select(['dsp_setor_id'])->where(['user_id'=>Yii::$app->user->identity->id])->asArray(),
            ]);
        }
        $query->andFilterWhere(['dsp_setor_id' =>$this->dsp_setor_id]);


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
        $query = ProcessoWorkflow::find()
        ->joinWith('processo')
        ->orderBy('dsp_processo.id desc,dsp_processo_workflow.id');

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
            'dsp_processo_workflow.user_id' =>$this->user_id,
            'dsp_processo_workflow.status' =>$this->status,
            'dsp_processo_workflow.dsp_setor_id' =>$this->dsp_setor_id,
            'dsp_processo.status' =>$this->processoStatus,
            'dsp_processo.numero' =>$this->globalSearch,
            'dsp_processo.bas_ano_id' =>$this->bas_ano_id,
        ]);
        // $query->andFilterWhere(['!=', 'dsp_processo_workflow.dsp_setor_id',[Setor::ARQUIVO_ID,Setor::ARQUIVO_PROVISORIO_ID]]);
        $query->andFilterWhere(['!=', 'dsp_processo.status', ProcessoStatus::STATUS_ANULADO]);
        

        $query->andFilterWhere(['>=', 'data_inicio', $this->data_inicio])
            ->andFilterWhere(['<=', 'data_fim', $this->data_fim]);


        

        return $dataProvider;
    }



     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchReceberBox($params)
    {
        
        $query = ProcessoWorkflow::find()
               ->joinWith('processo')
               ->orderBy('dsp_processo.id desc');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => FALSE,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'dsp_processo_workflow.user_id' => Yii::$app->user->identity->id,
            'dsp_processo_workflow.status' =>1,
            'dsp_processo.numero' =>$this->globalSearch,

        ]);
        
        $query->andFilterWhere(['!=', 'dsp_processo.status', ProcessoStatus::STATUS_ANULADO]);

        return $dataProvider;
    }

     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchEnviarBox($params)
    {
        
        $query = ProcessoWorkflow::find()
               ->joinWith('processo')
               ->orderBy('dsp_processo.id desc');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => FALSE,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }      
        $query->andFilterWhere([
            'dsp_processo_workflow.user_id' => Yii::$app->user->identity->id,
            'dsp_processo_workflow.status' =>2,
            'dsp_processo.numero' =>$this->globalSearch,
        ]);
        $query->andFilterWhere(['!=', 'dsp_processo.status', ProcessoStatus::STATUS_ANULADO]);
        // $query->andFilterWhere(['or',
        //             ['!=', 'dsp_processo.status', ProcessoStatus::PENDENTE],
        //             ['dsp_processo_workflow.prioridade'=>1]]
        //         );

        

        return $dataProvider;
    }





    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchRealocarBox($params)
    {
        
        $query = ProcessoWorkflow::find()
               ->joinWith('processo')
               ->orderBy('dsp_processo.id desc');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => FALSE,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        

       
        $query->andFilterWhere([
            'dsp_processo_workflow.status' =>2,
            'dsp_processo.numero' =>$this->globalSearch,

        ]);
        $query->andFilterWhere(['!=', 'dsp_processo_workflow.user_id', Yii::$app->user->identity->id]);
        $query->andFilterWhere(['!=', 'dsp_processo.status', ProcessoStatus::STATUS_ANULADO]);

        

        return $dataProvider;
    }



     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchArchiveBox($params)
    {
        
        $query = ProcessoWorkflow::find()
               ->joinWith('processo')
               ->orderBy('dsp_processo.numero desc');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => FALSE,
        ]);
        // print_r($this);die();

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'dsp_processo_workflow.user_id' => Yii::$app->user->identity->id,
            'dsp_processo_workflow.status' =>2,
            'dsp_processo.numero' =>$this->globalSearch,

            // 'dsp_processo.status' =>$this->status,
        ]);
        $query->andFilterWhere(['!=', 'dsp_processo.status', ProcessoStatus::STATUS_ANULADO]);

        

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchClassificacao($params)
    {
        
        $query =(new \yii\db\Query())
                ->select(['A.user_id', 'C.name'])    
                ->from('dsp_processo_workflow A')
                ->leftJoin('dsp_processo B','B.id = A.dsp_processo_id') 
                ->leftJoin('dsp_nord BB','B.id = BB.dsp_processo_id') 
                ->innerJoin('fin_fatura_provisoria D','D.dsp_processo_id = B.id') 
                ->leftJoin('user C','C.id = A.user_id') 
                ->leftJoin('dsp_person E','B.dsp_person_id = E.id')
                ->where(['is not','A.user_id',null])
                ->groupBy(['A.user_id']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'A.user_id' => $this->user_id,
        ]);
        $query->andFilterWhere(['>=', 'A.data_inicio', $this->dataInicio]);
        $query->andFilterWhere(['<=', 'A.data_inicio', $this->dataFim]);
        

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchClassificacaoSetor($params)
    {
        

        $query =(new \yii\db\Query())
                ->select(['A.user_id', 'C.name'])    
                ->from('dsp_processo_workflow A')
                ->leftJoin('dsp_processo B','B.id = A.dsp_processo_id') 
                ->leftJoin('dsp_nord BB','B.id = BB.dsp_processo_id') 
                ->innerJoin('fin_fatura_provisoria D','D.dsp_processo_id = B.id') 
                ->leftJoin('user C','C.id = A.user_id') 
                ->leftJoin('dsp_person E','B.dsp_person_id = E.id')
                ->where(['is not','A.user_id',null])
                ->groupBy(['A.user_id']);

        // $query =(new \yii\db\Query())
        //         ->select(['A.user_id', 'C.name'])  
        //         ->from('dsp_setor_user A')
        //         ->leftJoin('user C','C.id = A.user_id') 
        //         ->groupBy(['A.user_id','A.dsp_setor_id']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>false,
        ]);

        $this->load($params);

        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'A.dsp_setor_id' => $this->dsp_setor_id,
        ]);
        $query->andFilterWhere(['>=', 'A.data_inicio', $this->dataInicio]);
        $query->andFilterWhere(['<=', 'A.data_inicio', $this->dataFim]);
        

        return $dataProvider;
    }




    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchClassificacaoUser($params)
    {
        
        $this->load($params);

        
        $subQuery =(new \yii\db\Query())
                ->select([
                    'B.id',
                    'B.numero',
                    'B.bas_ano_id', 
                    'mercadoria'=>'B.descricao', 
                    'cliente'=>'E.nome',
                    'A.user_id',  
                    'A.dsp_processo_id',
                    'A.dsp_setor_id',
                    'setor'=>'F.descricao',
                    'A.data_inicio',
                    'data_faturacao'=>'D.data' ,
                    'nord'=>'BB.id' 
                ])    
                ->from('dsp_processo_workflow A')
                ->leftJoin('dsp_processo B','B.id = A.dsp_processo_id') 
                ->leftJoin('dsp_nord BB','B.id = BB.dsp_processo_id') 
                ->leftJoin('fin_fatura_provisoria D','D.dsp_processo_id = B.id')  
                ->leftJoin('dsp_person E','B.dsp_person_id = E.id')
                ->leftJoin('dsp_setor F','A.dsp_setor_id = F.id')
                // ->where(['!=','BB.id', NULL])
                // ->where(['A.dsp_setor_id'=>  $dsp_setor_id])
                ->andFilterWhere(['>=', 'D.data', $this->dataInicio])
                ->andFilterWhere(['<=', 'D.data', $this->dataFim])
                ->orderBy('A.id DESC, A.dsp_processo_id,A.dsp_setor_id')
                ->groupBy('A.dsp_processo_id,A.dsp_setor_id');
//print_r($subQuery->all());die();
                 $query = (new \yii\db\Query())
                        ->select([ 
                                'A.id',
                                'A.numero',
                                'A.bas_ano_id', 
                                'A.mercadoria', 
                                'A.cliente',
                                'A.user_id',  
                                'A.data_inicio',
                                'A.data_faturacao',
                                'A.nord',
                                'B.name',
                                'A.dsp_setor_id',
                                'A.setor'
                        ])
                        ->from(['A'=>$subQuery])
                        ->leftJoin('user B','B.id = A.user_id') 
                        ->andFilterWhere(['A.user_id'=>$this->user_id]) 
                        ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 250,
            ],
        ]);

        // $this->load($params);

        // if (!$this->validate()) {
        //     // uncomment the following line if you do not want to return any records when validation fails
        //     // $query->where('0=1');
        //     return $dataProvider;
        // }

        // // grid filtering conditions
        // $query->andFilterWhere([
        //     'A.user_id' => $this->user_id,
        // ]);
        // $query->andFilterWhere(['>=', 'A.data_inicio', $this->dataInicio]);
        // $query->andFilterWhere(['<=', 'A.data_inicio', $this->dataFim]);
        

        return $dataProvider;
    }
}
