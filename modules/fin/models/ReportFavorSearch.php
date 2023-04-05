<?php 
namespace app\modules\fin\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\fin\models\Receita;
use app\modules\dsp\models\Processo;

class ReportFavorSearch extends Processo
{
    public $documento_id;
    public $dsp_person_id;
    public $bas_ano_id; 
    public $dataInicio;
    public $dataFim;
    public $globalSearch;
    public $por_person;
    

    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[  'documento_id','bas_ano_id', 'dsp_person_id'], 'integer'],
            [['globalSearch',   'dataInicio', 'dataFim'], 'safe'], 
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
    public function afavorProcesso($params)
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
            ->leftJoin('fin_despesa D', 'D.status = 1 AND D.of_acount=0 AND D.dsp_processo_id = A.id')
            ->leftJoin('dsp_person B', 'B.id=A.nome_fatura')
            ->leftJoin('dsp_nord C', 'C.dsp_processo_id=A.id')
            ->where('A.status!=11 AND A.id NOT IN(16595,13895,13895,13574,12948)') 
            ->groupBy('A.id')
            ->andFilterWhere(['A.dsp_person_id' => $this->dsp_person_id])
            ->andFilterWhere(['>=', 'A.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'A.data', $this->dataFim]);  

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
                'valor_despesa' => 'IFNULL(SUM(A.valor),0)',
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
                'fatura_id'=>'A.id',
                'valor_recebido' => 'IFNULL(SUM(B.valor_recebido),0)', 
                'W.valor_despesa', 
                'W.total_pago', 
                'fatura_provisorias' => "CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`)"
            ])
            ->from(['W' => $query2])
            ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
            ->leftJoin('fin_receita B', 'B.dsp_fataura_provisoria_id = A.id')
			->where(['>','W.valor_despesa',0])
            ->groupBy(['W.id','A.id']);


         /**
         * Calcular valor recebido atravez da fatura definitiva especial
         */
        $query4 = (new \yii\db\Query())
            ->select([
                'TI.id', 
                'TI.numero', 
                'TI.nord', 
                'TI.mercadoria', 
                'TI.person', 
                'TI.data', 
                'TI.valor_recebido',  
                'TI.valor_despesa',
                'TI.total_pago', 
                'TI.fatura_provisorias',
                'valor_total_item_da_despesa' => 'CASE WHEN SUM(B.valor) > TI.valor_recebido THEN TI.valor_recebido ELSE SUM(B.valor) END',
            ])
            ->from(['TI' => $query3])
            ->leftJoin('fin_fatura_provisoria_item B', 'B.dsp_fatura_provisoria_id = TI.fatura_id AND B.dsp_item_id NOT IN(1000,1002,1003,1004,1010,1016,1018,1088,1089)')  
            ->groupBy(['TI.id','TI.fatura_id']); 
		

             /**
         * Calcular valor recebido atravez da fatura definitiva especial
         */
        $query5 = (new \yii\db\Query())
            ->select([
                'A.id', 
                'A.numero', 
                'A.nord', 
                'A.mercadoria', 
                'A.person', 
                'A.data', 
                'fatura_provisorias' => "GROUP_CONCAT(A.fatura_provisorias SEPARATOR ',')",
                'valor_recebido'=> 'IFNULL(SUM(A.valor_recebido),0)',  
                'A.valor_despesa',
                'A.total_pago', 
                'valor_total_item_da_despesa' => 'IFNULL(SUM(A.valor_total_item_da_despesa),0)'
            ])
            ->from(['A' => $query4])
            ->groupBy('A.id');  
	
         /**
         * Calcular valor recebido atravez da fatura definitiva especial
         */
        /*$query6 = (new \yii\db\Query())
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
                'WW.valor_total_item_da_despesa', 
                'WW.fatura_provisorias' ,
                'fatura_definitivas' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
            ])
            ->from(['WW' => $query5])
            ->leftJoin('fin_fatura_definitiva A', 'A.dsp_processo_id = WW.id AND A.status = 1 AND A.fin_fatura_definitiva_serie =2')
            ->leftJoin('fin_receita B', 'B.fin_fataura_definitiva_id = A.id')
            ->groupBy('WW.id'); */ 


            



         /**
         * Calcular valor aviso de credito
         */
        $query7 = (new \yii\db\Query())
            ->select([
                'R.id', 
                'R.numero', 
                'R.nord', 
                'R.mercadoria', 
                'R.person', 
                'R.data', 
                'R.valor_recebido' ,
                'R.valor_despesa',
                'R.total_pago', 
                'R.fatura_provisorias',
                // 'R.fatura_definitivas',
                'aviso_creadito'=>'IFNULL(SUM(A.valor),0)',
                'R.valor_total_item_da_despesa'
            ])
            ->from(['R' => $query5])
            ->leftJoin('fin_nota_credito A', 'A.dsp_processo_id = R.id AND A.status = 1 ')
            ->groupBy('R.id'); 

        
          /**
         * Calcular valor aviso de credito
         */
        $query8 = (new \yii\db\Query())
            ->select([
                'V.id', 
                'V.numero', 
                'V.nord', 
                'V.mercadoria', 
                'V.person', 
                'V.data', 
                'V.valor_recebido',
                'V.valor_despesa',
                'V.total_pago', 
                'V.fatura_provisorias',
                // 'V.fatura_definitivas',
                'V.aviso_creadito',
                'nota_creadito'=>'IFNULL(SUM(A.valor),0)',
                'V.valor_total_item_da_despesa'
            ])
            ->from(['V' => $query7])
            ->leftJoin('fin_aviso_credito A', 'A.dsp_processo_id = V.id AND A.status = 1 ')
            ->groupBy('V.id'); 

        /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $query9 = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'P.valor_recebido', 
                'devolucao_alfandega' => 'IFNULL(SUM(B.valor_recebido),0)', 
                'P.valor_despesa', 
                'P.total_pago', 
                'P.fatura_provisorias',
                // 'P.fatura_definitivas',
                'P.aviso_creadito',
                'P.nota_creadito',
                'P.valor_total_item_da_despesa'
            ])
            ->from(['P' => $query8])
            ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = P.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
            ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC]);


            /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $query10 = (new \yii\db\Query())
            ->select([
                'PP.id',
                'PP.numero', 
                'PP.nord', 
                'PP.mercadoria', 
                'PP.person', 
                'PP.data', 
                'PP.valor_recebido', 
                'PP.devolucao_alfandega', 
                'PP.valor_despesa', 
                'PP.total_pago', 
                'PP.fatura_provisorias',
                // 'P.fatura_definitivas',
                'PP.aviso_creadito',
                'PP.nota_creadito',
                'PP.valor_total_item_da_despesa',
                 'valor_recebido_para_despesa'=>'CASE WHEN PP.valor_recebido > 0 AND PP.valor_recebido > PP.valor_total_item_da_despesa THEN  PP.valor_total_item_da_despesa + IFNULL(PP.devolucao_alfandega,0) - IFNULL(PP.aviso_creadito, 0) - IFNULL(PP.nota_creadito, 0) ELSE PP.valor_recebido + IFNULL(SUM(PP.devolucao_alfandega),0) - IFNULL(PP.aviso_creadito, 0) - IFNULL(PP.nota_creadito, 0) END'
            ])
            ->from(['PP' => $query9]) 
            ->groupBy('PP.id')
            ->orderBy(['PP.id'=>SORT_DESC]);
 


        /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $query = (new \yii\db\Query())
            ->select([
                'PW.id',
                'PW.numero', 
                'PW.nord', 
                'PW.mercadoria', 
                'PW.person', 
                'PW.data', 
                'PW.valor_recebido', 
                'PW.devolucao_alfandega' , 
                'PW.valor_despesa', 
                'PW.total_pago',  // b
                'PW.fatura_provisorias',
                // 'P.fatura_definitivas',
                'PW.aviso_creadito',
                'PW.nota_creadito',
                'PW.valor_total_item_da_despesa',
                'PW.valor_recebido_para_despesa' , // a
                'saldo'=>'(PW.valor_recebido_para_despesa-PW.total_pago)'
            ])
            ->from(['PW' => $query10]) 
            ->groupBy('PW.id')
            ->orderBy(['PW.id'=>SORT_DESC])
            ->having(['>','saldo',0]); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        } 

       


// print_r(dataProvider);die();
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function afavorProcessoOld($params)
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
            ->leftJoin('fin_despesa D', 'D.status = 1 AND D.of_acount=0 AND D.dsp_processo_id = A.id')
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
            // ->where(['>','total_pago',0])
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
			->where(['>','valor_despesa',0])
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
                'WW.fatura_provisorias' ,
                'fatura_definitivas' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
            ])
            ->from(['WW' => $query3])
            ->leftJoin('fin_fatura_definitiva A', 'A.dsp_processo_id = WW.id AND A.status = 1 AND A.fin_fatura_definitiva_serie =2')
            ->leftJoin('fin_receita B', 'B.fin_fataura_definitiva_id = A.id')
            ->groupBy('WW.id');  


            


        /**
         * Calcular valor recebido atravez da fatura definitiva especial
         */
        $queryTotalItemDespesa = (new \yii\db\Query())
            ->select([
                'TI.id', 
                'TI.numero', 
                'TI.nord', 
                'TI.mercadoria', 
                'TI.person', 
                'TI.data', 
                'TI.valor_recebido',  
                'TI.valor_despesa',
                'TI.total_pago', 
                'TI.fatura_provisorias' ,
                'TI.fatura_definitivas',
                'valor_total_item_da_despesa' => 'SUM(B.valor)'
            ])
            ->from(['TI' => $query4])
            ->leftJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = TI.id AND A.status = 1')
            ->leftJoin('fin_fatura_provisoria_item B', 'B.dsp_fatura_provisoria_id = A.id AND B.dsp_item_id NOT IN(1000,1002,1003,1004,1010,1016,1018,1088,1089)')  
            ->groupBy('TI.id');  






         /**
         * Calcular valor aviso de credito
         */
        $queryAvisoCredito = (new \yii\db\Query())
            ->select([
                'R.id', 
                'R.numero', 
                'R.nord', 
                'R.mercadoria', 
                'R.person', 
                'R.data', 
                'R.valor_recebido' ,
                'R.valor_despesa',
                'R.total_pago', 
                'R.fatura_provisorias',
                'R.fatura_definitivas',
                'aviso_creadito'=>' SUM(A.valor)',
                'R.valor_total_item_da_despesa'
            ])
            ->from(['R' => $queryTotalItemDespesa])
            ->leftJoin('fin_nota_credito A', 'A.dsp_processo_id = R.id AND A.status = 1 ')
            ->groupBy('R.id'); 
        
             /**
         * Calcular valor aviso de credito
         */
        $queryNotaCredito = (new \yii\db\Query())
            ->select([
                'V.id', 
                'V.numero', 
                'V.nord', 
                'V.mercadoria', 
                'V.person', 
                'V.data', 
                'V.valor_recebido',
                'V.valor_despesa',
                'V.total_pago', 
                'V.fatura_provisorias',
                'V.fatura_definitivas',
                'V.aviso_creadito',
                'nota_creadito'=>' SUM(A.valor)',
                'V.valor_total_item_da_despesa'
            ])
            ->from(['V' => $queryAvisoCredito])
            ->leftJoin('fin_aviso_credito A', 'A.dsp_processo_id = V.id AND A.status = 1 ')
            ->groupBy('V.id'); 

        /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $queryReembolco = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'P.valor_recebido', 
                'devolucao_alfandega' => 'SUM(B.valor_recebido)', 
                'P.valor_despesa', 
                'P.total_pago', 
                'P.fatura_provisorias',
                'P.fatura_definitivas',
                'P.aviso_creadito',
                'P.nota_creadito',
                'P.valor_total_item_da_despesa'
            ])
            ->from(['P' => $queryNotaCredito])
            ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = P.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
            ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC]);


            /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $queryValorRecebidoParaDespesa = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'P.valor_recebido', 
                'P.devolucao_alfandega', 
                'P.valor_despesa', 
                'P.total_pago', 
                'P.fatura_provisorias',
                'P.fatura_definitivas',
                'P.aviso_creadito',
                'P.nota_creadito',
                'P.valor_total_item_da_despesa',
                 'valor_recebido_para_despesa'=>'CASE WHEN P.valor_recebido > 0 AND P.valor_recebido > P.valor_total_item_da_despesa THEN  P.valor_total_item_da_despesa + IFNULL(P.devolucao_alfandega,0) - IFNULL(P.aviso_creadito, 0) - IFNULL(P.nota_creadito, 0) ELSE P.valor_recebido + IFNULL(SUM(P.devolucao_alfandega),0) - IFNULL(P.aviso_creadito, 0) - IFNULL(P.nota_creadito, 0) END'
            ])
            ->from(['P' => $queryReembolco]) 
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC]);
 

        /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $query = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'P.valor_recebido', 
                'P.devolucao_alfandega' , 
                'P.valor_despesa', 
                'P.total_pago',  // b
                'P.fatura_provisorias',
                'P.fatura_definitivas',
                'P.aviso_creadito',
                'P.nota_creadito',
                'P.valor_total_item_da_despesa',
                'P.valor_recebido_para_despesa' , // a
                'saldo'=>'(P.valor_recebido_para_despesa-P.total_pago)'
            ])
            ->from(['P' => $queryValorRecebidoParaDespesa]) 
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC])
            ->having(['>','saldo',0]);



            
            // ->having('saldo > 0');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        } 

        $query->andFilterWhere(['P.dsp_person_id' => $this->dsp_person_id])
            ->andFilterWhere(['>=', 'P.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'P.data', $this->dataFim]);


// print_r(dataProvider);die();
        return $dataProvider;
    }
	
	
	
	/**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function afavorFatuaProvisoria($params)
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
            ->innerJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = W.id AND A.status = 1')
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
                'WW.fatura_provisorias' ,
                'fatura_definitivas' => "GROUP_CONCAT(CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`) SEPARATOR ',')"
            ])
            ->from(['WW' => $query3])
            ->leftJoin('fin_fatura_definitiva A', 'A.dsp_processo_id = WW.id AND A.status = 1 AND A.fin_fatura_definitiva_serie =2')
            ->leftJoin('fin_receita B', 'B.fin_fataura_definitiva_id = A.id')
            ->groupBy('WW.id');  


            


        /**
         * Calcular valor recebido atravez da fatura definitiva especial
         */
        $queryTotalItemDespesa = (new \yii\db\Query())
            ->select([
                'TI.id', 
                'TI.numero', 
                'TI.nord', 
                'TI.mercadoria', 
                'TI.person', 
                'TI.data', 
                'TI.valor_recebido',  
                'TI.valor_despesa',
                'TI.total_pago', 
                'TI.fatura_provisorias' ,
                'TI.fatura_definitivas',
                'valor_total_item_da_despesa' => 'SUM(B.valor)'
            ])
            ->from(['TI' => $query4])
            ->innerJoin('fin_fatura_provisoria A', 'A.dsp_processo_id = TI.id AND A.status = 1')
            ->leftJoin('fin_fatura_provisoria_item B', 'B.dsp_fatura_provisoria_id = A.id AND B.dsp_item_id NOT IN(1000,1002,1003,1004,1010,1016,1018,1088,1089)')  
            ->groupBy('TI.id');  






         /**
         * Calcular valor aviso de credito
         */
        $queryAvisoCredito = (new \yii\db\Query())
            ->select([
                'R.id', 
                'R.numero', 
                'R.nord', 
                'R.mercadoria', 
                'R.person', 
                'R.data', 
                'R.valor_recebido' ,
                'R.valor_despesa',
                'R.total_pago', 
                'R.fatura_provisorias',
                'R.fatura_definitivas',
                'aviso_creadito'=>' SUM(A.valor)',
                'R.valor_total_item_da_despesa'
            ])
            ->from(['R' => $queryTotalItemDespesa])
            ->leftJoin('fin_nota_credito A', 'A.dsp_processo_id = R.id AND A.status = 1 ')
            ->groupBy('R.id'); 
        
             /**
         * Calcular valor aviso de credito
         */
        $queryNotaCredito = (new \yii\db\Query())
            ->select([
                'V.id', 
                'V.numero', 
                'V.nord', 
                'V.mercadoria', 
                'V.person', 
                'V.data', 
                'V.valor_recebido' ,
                'V.valor_despesa',
                'V.total_pago', 
                'V.fatura_provisorias',
                'V.fatura_definitivas',
                'V.aviso_creadito',
                'nota_creadito'=>' SUM(A.valor)',
                'V.valor_total_item_da_despesa'
            ])
            ->from(['V' => $queryAvisoCredito])
            ->leftJoin('fin_aviso_credito A', 'A.dsp_processo_id = V.id AND A.status = 1 ')
            ->groupBy('V.id'); 

        /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $queryReembolco = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'P.valor_recebido', 
                'devolucao_alfandega' => 'SUM(B.valor_recebido)', 
                'P.valor_despesa', 
                'P.total_pago', 
                'P.fatura_provisorias',
                'P.fatura_definitivas',
                'P.aviso_creadito',
                'P.nota_creadito',
                'P.valor_total_item_da_despesa'
            ])
            ->from(['P' => $queryNotaCredito])
            ->leftJoin('fin_recebimento A', 'A.dsp_processo_id = P.id AND A.status = 1 AND A.fin_recebimento_tipo_id = 4')
            ->leftJoin('fin_recebimento_item B', 'B.fin_recebimento_id = A.id')
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC]);


            /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $queryValorRecebidoParaDespesa = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'P.valor_recebido', 
                'P.devolucao_alfandega', 
                'P.valor_despesa', 
                'P.total_pago', 
                'P.fatura_provisorias',
                'P.fatura_definitivas',
                'P.aviso_creadito',
                'P.nota_creadito',
                'P.valor_total_item_da_despesa',
                'valor_recebido_para_despesa'=>'CASE WHEN P.valor_recebido > 0 AND P.valor_recebido > P.valor_total_item_da_despesa THEN  P.valor_total_item_da_despesa + IFNULL(P.devolucao_alfandega,0) - IFNULL(P.aviso_creadito, 0) - IFNULL(P.nota_creadito, 0) ELSE P.valor_recebido + IFNULL(SUM(P.devolucao_alfandega),0) - IFNULL(P.aviso_creadito, 0) - IFNULL(P.nota_creadito, 0) END'
            ])
            ->from(['P' => $queryReembolco]) 
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC]);
 

        /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $query = (new \yii\db\Query())
            ->select([
                'P.id',
                'P.numero', 
                'P.nord', 
                'P.mercadoria', 
                'P.person', 
                'P.data', 
                'P.valor_recebido', 
                'P.devolucao_alfandega' , 
                'P.valor_despesa', 
                'P.total_pago',  // b
                'P.fatura_provisorias',
                'P.fatura_definitivas',
                'P.aviso_creadito',
                'P.nota_creadito',
                'P.valor_total_item_da_despesa',
                'P.valor_recebido_para_despesa' , // a
                'saldo'=>'(P.valor_recebido_para_despesa-P.total_pago)'
            ])
            ->from(['P' => $queryValorRecebidoParaDespesa]) 
            ->groupBy('P.id')
            ->orderBy(['P.id'=>SORT_DESC])
            ->having(['>','saldo',0]);



            
            // ->having('saldo > 0');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        }



        $query->andFilterWhere(['P.dsp_person_id' => $this->dsp_person_id])
                ->andFilterWhere(['>=', 'P.data', $this->dataInicio])
            ->andFilterWhere(['<=', 'P.data', $this->dataFim]);



        return $dataProvider;
    }
}