<?php

namespace app\modules\dsp\repositories;
use yii;
use app\modules\dsp\models\Processo;
/**
 * 
 */
class ProcessoRepository
{

    

     /**
     * Lists all User models.
     * @return mixed
     */
    public function listPersonProcessoInterno()
    {
        return (new \yii\db\Query())
                    ->select(['A.user_id','user_name'=>'B.name','total_processo'=>'COUNT(A.id)'])
                    ->from(['A'=>'dsp_processo'])
                    ->leftJoin(['B'=>'user'],'B.id = A.user_id')
                    ->where(['A.status'=>[1,2,3]])
                    ->andWhere(['not in','A.dsp_setor_id',[10,11]])
                    ->andWhere(['>','A.user_id',0])
                    ->groupBy('A.user_id')
                    ->all();
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function listProcessoInternoFuncionario($user_id)
    {
       return Processo::find()
                 ->where(['user_id' => $user_id])
                 ->andWhere(['status'=>[1,2,3]])
                 ->andWhere(['not in','dsp_setor_id',[10,11]])
                 ->all();
    }


     /**
     * Lists all User models.
     * @return mixed
     */
    public function listSetorProcessoInterno()
    {
        return  (new \yii\db\Query())
                    ->select(['A.dsp_setor_id','dsp_setor_name'=>'B.descricao','total_processo'=>'COUNT(A.id)'])
                    ->from(['A'=>'dsp_processo'])
                    ->leftJoin(['B'=>'dsp_setor'],'B.id = A.dsp_setor_id')
                    ->where(['A.status'=>[1,2,3]])
                    ->andWhere(['not in','A.dsp_setor_id',[10,11]])
                    ->andWhere(['>','dsp_setor_id',0])
                    ->groupBy('A.dsp_setor_id')
                    ->all();
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function listProcessoInternoSetor($dsp_setor_id)
    {
        return Processo::find()
                 ->where(['dsp_setor_id' => $dsp_setor_id])
                //  ->andWhere(['status'=>[4,5,8]])
                 ->andWhere(['not in','dsp_setor_id',[10,11]])
                 ->all();
    }





     /**
     * Lists all User models.
     * @return mixed
     */
    public function listEstadoProcessoExterno()
    {
        return  (new \yii\db\Query())
                    ->select(['A.status','status_name'=>'B.descricao','total_processo'=>'COUNT(A.id)'])
                    ->from(['A'=>'dsp_processo'])
                    ->leftJoin(['B'=>'dsp_processo_status'],'B.id = A.status')
                    ->where(['A.status'=>[4,5,8]])
                    ->andWhere(['not in','A.dsp_setor_id',[10,11]])
                    ->groupBy('A.status')
                    ->all();
    }

     /**
     * Lists all User models.
     * @return mixed
     */
    public function listProcessoExternoStatus($status)
    {
        return  Processo::find()
                 ->where(['status' => $status])
                 ->andWhere(['status'=>[4,5,8]])
                 ->andWhere(['not in','dsp_setor_id',[10,11]])
                 ->all();
    }

    
}
