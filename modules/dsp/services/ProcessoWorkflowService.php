<?php
namespace app\modules\dsp\services;
use yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\ProcessoDespachoDocumento;
use app\modules\dsp\models\ProcessoObs;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\dsp\models\ProcessoStatusFinanceiro;
use app\modules\dsp\models\ProcessoStatusOperacional;
use app\modules\fin\models\FaturaProvisoria;
use app\modules\dsp\models\ProcessoWorkflow;


/**
* 
*/
class ProcessoWorkflowService 
{  
    /**
     * Cria ou atualiza histórico de estado oeracionais.
     * @return int
     */
    public static function totalClassificacao($user_id, $dataInicio, $dataFim)
    {
        $numero_artigo = 0;
        $subQuery =(new \yii\db\Query())
                 ->select([
                    'A.dsp_processo_id',
                    'A.id',
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
                ->where(['>','A.user_id', 0])
                ->andFilterWhere(['>=', 'D.data', $dataInicio])
                ->andFilterWhere(['<=', 'D.data', $dataFim])
                ->orderBy(['A.id' => SORT_DESC])
                ->groupBy('A.dsp_processo_id,A.dsp_setor_id');

                 $query = (new \yii\db\Query())
                        ->select([ 
                                'A.id',
                                'A.numero',
                                'A.bas_ano_id', 
                                'A.mercadoria', 
                                'A.cliente',
                                'A.user_id',  
                                'A.data_faturacao',
                                'A.nord',
                                'B.name'
                        ])
                        ->from(['A'=>$subQuery])
                        ->leftJoin('user B','B.id = A.user_id') 
                        ->where(['A.user_id'=>$user_id]) 
                        ->all();
                        
        $queryAA =(new \yii\db\Query())
                ->select([
                    'B.id',
                    'B.numero',
                    'B.bas_ano_id', 
                    'mercadoria'=>'B.descricao', 
                    'cliente'=>'E.nome',
                    'A.user_id', 'C.name', 
                    'data_faturacao'=>'D.data' ,
                    'nord'=>'BB.id' 
                ])    
                ->from('dsp_processo_workflow A')
                ->leftJoin('dsp_processo B','B.id = A.dsp_processo_id') 
                ->leftJoin('dsp_nord BB','B.id = BB.dsp_processo_id') 
                ->innerJoin('fin_fatura_provisoria D','D.dsp_processo_id = B.id') 
                ->leftJoin('user C','C.id = A.user_id') 
                ->leftJoin('dsp_person E','B.dsp_person_id = E.id')
                ->where(['A.user_id' => $user_id])
                ->andWhere(['>=', 'D.data', $dataInicio])
                ->andWhere(['<=', 'D.data', $dataFim])
                ->groupBy(['A.dsp_processo_id'])
                ->all();

        foreach($query as $value){
            $numero_artigo = $numero_artigo + \app\modules\dsp\services\NordService::totalNumberOfItems($value['nord']);
        }

        return $numero_artigo==0 ?0:$numero_artigo;


    }

     /**
     * Cria ou atualiza histórico de estado oeracionais.
     * @return int
     */
    public static function totalClassificacaoSetor($dsp_setor_id, $user_id, $dataInicio, $dataFim)
    {
        $numero_artigo = 0;                
        $subQueryMaxId =(new \yii\db\Query())
                ->select([
                    'id'=>'MAX(A.id)',
                    'A.dsp_processo_id',
                    'A.dsp_setor_id'
                ])    
                ->from('dsp_processo_workflow A')
                ->leftJoin('dsp_processo B','B.id = A.dsp_processo_id') 
                ->leftJoin('dsp_nord C','B.id = C.dsp_processo_id') 
                ->leftJoin('fin_fatura_provisoria D','D.dsp_processo_id = B.id')  
                ->where(['>','A.user_id', 0]) 
                ->andWhere(['>=', 'D.data', $dataInicio])
                ->andWhere(['<=', 'D.data', $dataFim])
                ->orderBy(['A.dsp_processo_id'=>SORT_DESC ,'A.id' => SORT_DESC])
                ->groupBy('A.dsp_processo_id,A.dsp_setor_id');
                
        $subQuery =(new \yii\db\Query())
                ->select([
                    'A.id',
                    'A.dsp_processo_id',
                    'C.numero',
                    'C.bas_ano_id', 
                    'A.user_id',  
                    'user'=>'G.name',
                    'A.dsp_setor_id',
                    'setor'=>'F.descricao',
                    'A.data_inicio',
                    'cliente'=>'E.nome' ,
                    'mercadoria'=>'C.descricao', 
                    'data_faturacao'=>'D.data',
                    'nord'=>'BB.id'
                ])    
                ->from('dsp_processo_workflow A')
                ->innerJoin(['B'=>$subQueryMaxId],'A.id=B.id AND A.dsp_processo_id=B.dsp_processo_id AND A.dsp_setor_id=B.dsp_setor_id')
                // ->addParams([':subQueryMaxId' => $subQueryMaxId])
                ->leftJoin('dsp_processo C','C.id = A.dsp_processo_id') 
                ->leftJoin('dsp_nord BB','C.id = BB.dsp_processo_id') 
                ->leftJoin('fin_fatura_provisoria D','D.dsp_processo_id = C.id')  
                ->leftJoin('dsp_person E','C.dsp_person_id = E.id')
                ->leftJoin('dsp_setor F','A.dsp_setor_id = F.id')
                ->leftJoin('user G','A.user_id = G.id')
                ->where(['>','A.user_id',0])
                ->andWhere(['>=', 'D.data', $dataInicio])
                ->andWhere(['<=', 'D.data', $dataFim])
                ->orderBy(['A.dsp_processo_id'=>SORT_DESC ,'A.id' => SORT_DESC])
                ->groupBy('A.dsp_processo_id,A.dsp_setor_id');
                 $query = (new \yii\db\Query())
                        ->select([ 
                                'A.id',
                                'A.numero',
                                'A.bas_ano_id', 
                                'A.mercadoria', 
                                'A.cliente',
                                'A.user_id',  
                                'A.data_faturacao',
                                'A.nord',
                                'B.name'
                        ])
                        ->from(['A'=>$subQuery])
                        ->leftJoin('user B','B.id = A.user_id') 
                        ->where(['A.user_id'=>$user_id]) 
                        ->andWhere(['A.dsp_setor_id'=>$dsp_setor_id])
                        ->all();

        foreach($query as $value){
            $numero_artigo = $numero_artigo + \app\modules\dsp\services\NordService::totalNumberOfItems($value['nord']);
        }

        return $numero_artigo==0 ?0:$numero_artigo;
    }


    public function temProcessoExecucao(int $user_id)
    {
        $retult= false;
        $workflowProcesso = ProcessoWorkflow::find()
                          ->where(['status'=>[1,2,4]])
                          ->andWhere(['user_id'=>$user_id])
                          ->all();
        foreach($workflowProcesso as $value){
            if($value->processo->status==2){
                $retult = TRUE;
            }
        }
        return $retult;
    }     
}
?>
