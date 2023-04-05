<?php
namespace app\modules\dsp\components;

use yii\data\ActiveDataProvider;

use yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;

use app\modules\dsp\models\DespachoDocumento;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\ProcessoDespachoDocumento;
use app\modules\dsp\models\ProcessoObs;
use app\modules\dsp\models\ProcessoHistorico;
use app\modules\dsp\models\ProcessoStatus;
use app\modules\fin\models\FaturaProvisoria;


/**
* 
*/
class Dsp extends Component
{   

     

    /**
     * Lists all User models.
     * @return mixed
     */
    public function isCompleteProcessoDocument($dsp_processo_id)
    {
        $ok = true;
        $processoDocumentos = DespachoDocumento::find()->select(['id'])->where(['obrigatorio'=>1])->all();
        
        foreach ($processoDocumentos as $key => $processoDocumento) {
            if ((ProcessoDespachoDocumento::find()->where(['dsp_processo_id' =>$dsp_processo_id,'dsp_processo_documento_id'=>$processoDocumento->id])->one()) == null) {
                $ok = false;
            }
        }
        return $ok;
        
        
        
    }

    /**
     * Lists all User models.
     * @return mixed
     */
     public function updateProcessoStatus($dsp_processo_id)
     {
         $model = Processo::findOne(['id'=>$dsp_processo_id]);
         if (empty($model->user_id)) {
            $model->status = ProcessoStatus::NAO_ATRIBUIDO;
         }else {
             $model->status = ProcessoStatus::EM_EXECUCAO;
             $obs = ProcessoObs::find()->where(['dsp_processo_id'=>$model->id,'status'=>0])->count();
             if ($obs>0) {
                $model->status = ProcessoStatus::PENDENTE;
             }
              if (!empty($model->nord->despacho->id)) {
                 $model->status =  ProcessoStatus::REGISTRADO;
             }
             if (!empty($model->nord->despacho->numero_liquidade)) {
                 $model->status = ProcessoStatus::LIQUIDADO;
             }
             if (!empty($model->nord->despacho->n_receita)) {
                 $model->status = ProcessoStatus::RECEITADO;
             }
             
         }
                  
        if ($model->save(false)) {
            $processoHistorico = new ProcessoHistorico();
            $processoHistorico->dsp_processo_id = $dsp_processo_id;
            $processoHistorico->descricao = 'Estado atualizado para '.$model->processoStatus->descricao;
            $processoHistorico->save();
         return $model;
        }
         
         return false;
         
         
         
     }

     public function ProcessoUpdateAllStatus()
     {
         $models = Processo::find()
                   ->where(['in', 'status', [1,2,3,4,5,7,6]])
                   ->all();
            foreach ($models as $key => $model) {
                if (empty($model->user_id)) {
                   $model->status = 1;
                }else {
                    $model->status = 2;
                    $obs = ProcessoObs::find()->where(['dsp_processo_id'=>$model->id,'status'=>0])->count();
                    if ($obs>0) {
                       $model->status = 3;
                    }
                     if (!empty($model->nord->despacho->id)||$model->n_registro_tn>0) {
                        $model->status = 4;
                    }
                    if (!empty($model->nord->despacho->numero_liquidade)) {
                        $model->status = 5;
                    }
                    if (!empty($model->nord->despacho->n_receita)) {
                        $model->status = 8;
                    }
                    
                }
                $model->save();
            }
     }


    /**
     * Lists all User models.
     * @return mixed
     */
     public function processPdfReport($user_id,array $status=null,$dsp_person_id=null,$bas_ano_id,$dataInicio=null, $dataFim=null,$comPl =null)
     { 
         $query = Processo::find()
               ->andFilterWhere(['user_id'=>$user_id])
               ->andFilterWhere(['dsp_person_id'=>$dsp_person_id])
               ->andFilterWhere(['status'=>$status])
               ->andFilterWhere(['bas_ano_id'=>$bas_ano_id])
               ->andFilterWhere(['>=','data',$dataInicio])
               ->andFilterWhere(['<=','data',$dataFim]);
               if( $comPl==0){
                    $query->andFilterWhere(['>', 'n_levantamento', $comPl]);
                }
                if( $comPl==1){
                    $query->andFilterWhere(['>', 'n_levantamento', $comPl])
                        ->andFilterWhere(['status'=>[1,2,3,4,5,7]]);
                }
                if( $comPl==2){
                    $query->andFilterWhere(['>', 'n_levantamento', $comPl])
                        ->andFilterWhere(['status'=>[6,8,9]]);
                }
                $query->orderBy('status');
         
               $dataProvider = new ActiveDataProvider([
                 'query' => $query,  
                 'pagination' => [
                     'pageSize' => 90000000000,
                 ],          
             ]);
         
         return $dataProvider;
     }
 
     /**
      * Lists all User models.
      * @return mixed
      */
     public function processPdfAll($user_id=null,array $status=null,$dsp_person_id=null,$bas_ano_id,$dataInicio=null, $dataFim =null,$comPl=null)
     { 
         $query = Processo::find()
               ->andFilterWhere(['user_id'=>$user_id])
               ->andFilterWhere(['dsp_person_id'=>$dsp_person_id])
               ->andFilterWhere(['status'=>$status])
               ->andFilterWhere(['bas_ano_id'=>$bas_ano_id])
               ->andFilterWhere(['>=','data',$dataInicio])
               ->andFilterWhere(['<=','data',$dataFim]);
               if( $comPl==0){
                    $query->andFilterWhere(['>', 'n_levantamento', $comPl]);
                }
                if( $comPl==1){
                    $query->andFilterWhere(['>', 'n_levantamento', $comPl])
                        ->andFilterWhere(['status'=>[1,2,3,4,5,7]]);
                }
                if( $comPl==2){
                    $query->andFilterWhere(['>', 'n_levantamento', $comPl])
                        ->andFilterWhere(['status'=>[6,8,9]]);
                }
                $query->orderBy('status');
         
             $dataProvider = new ActiveDataProvider([
                 'query' => $query,
                 'pagination' => false,            
             ]);
         
         return $dataProvider;
     }


     /**
     * Lists all User models.
     * @return mixed
     */
    public function statusImgDescricao($id)
    { 
        if ($id ==1) {
            $img =  Html::img(Url::to('@web/img/grayBal.png'));                      # code...
           }elseif($id ==2){
            $img =  Html::img(Url::to('@web/img/gray-greenBall.png'));  
            
           }elseif($id ==3){
            $img =  Html::img(Url::to('@web/img/warning.png'));                      
           }elseif($id ==4){
            $img =  Html::img(Url::to('@web/img/accept.png'));                      
           }elseif($id ==5){
            $img =  Html::img(Url::to('@web/img/greenBall.png')); }
            elseif($id ==6){
              $img =  Html::img(Url::to('@web/img/if_check.png.png')); 
            }
              elseif($id ==7){
                $img =  Html::img(Url::to('@web/img/recebido.png.png')); 
              }
        elseif($id ==8){
          $img =  Html::img(Url::to('@web/img/greenBall.png')); 
        }
          else{
          $img =  Html::img(Url::to('@web/img/greenBall.png')); 
        }
        
        return $img;
    }

     /**
     * Lists all User models.
     * @return mixed
     */
    public function processoObsPendenstes($id)
    { 
        $result = '';

        $obs = ProcessoObs::findAll([
                'dsp_processo_id'=>$id,
                'status'=>0,
                ]);
        if (!empty($obs)) {
            $i=1;
            foreach ($obs as $key => $value) {
                $result = $result.$i.'. '.$value->obs;
                $i++;
            }
            # code...
        }
        return $result;
    }



    /**
     * Lists all User models.
     * @return mixed
     */
    public function despachoDia($id)
    {
        $value = Processo::FindOne($id);
        if(!empty($value->pedidoLevantamento->data_proragacao)){
                $result = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($value->pedidoLevantamento->data_proragacao,'php:d/m/Y'));
              }else{
                if(!empty($value->pedidoLevantamento->data_regularizacao)){
                $result = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($value->pedidoLevantamento->data_regularizacao,'php:d/m/Y'));
              }
              }
              if(!empty($value->nord->despacho->data_liquidacao)){
                $result2 = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($value->nord->despacho->data_liquidacao,'php:d/m/Y'));
              }
              if (!empty($result)||!empty($result2)) {
                return (!empty($result)?'PL: '.$result.'</br>':'').(!empty($result2)?' LIQ: '.$result2:'');
              }else{
              return 'ESC: '.Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($value->data,'php:d/m/Y'));
              }
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function checkFaturaProvisoriaSend($id)
    {
        $send = false;
        $fps = FaturaProvisoria::find()->where(['dsp_processo_id'=>$id])->all();
        foreach ($fps as $key => $value) {
            if($value->send){
                $send = true;
            }
        }

        return $send;
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function CountWorkflowStatus($status, $user_id)
    {
        $count = \app\modules\dsp\models\ProcessoWorkflow::find()
            ->where(['user_id' => $user_id])
            ->andWhere(['status' => $status])
            ->count();

        return $count;
    }

     
}
?>



