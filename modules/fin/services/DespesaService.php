<?php
namespace app\modules\fin\services;


use yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;
use yii\db\Expression;


use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\FaturaDefinitivaProvisoria;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\PagamentoItem;
use app\modules\fin\models\PagamentoOrdemItem;

/**
* 
*/
class DespesaService extends Component
{   

    /**
     * Vereficar se a despesa tem fatura definitiva
     * @var integer
     */
    public function temFaturaFefinitiva($fin_despesa_id)
    {
        $model = Despesa::findOne($fin_despesa_id);
        if (!empty($model->dsp_fatura_provisoria_id)) {
            if(FaturaDefinitivaProvisoria::find()->where(['fin_fatura_provisoria_id'=>$model->dsp_fatura_provisoria_id])->count() >=1 ){
                return true;
            }
        }
        return false;
    }

    /**
     * Vereficar se a despesa tem fatura definitiva
     * @var integer
     */
    public function temPagamento($fin_despesa_id)
    {
        $response = false;
        $modelsAll = PagamentoItem::find()
                ->where(['fin_despesa_id'=>$fin_despesa_id])
                ->all();
        
        if (!empty($modelsAll)) {
            foreach($modelsAll as $pagamentoItem){
                if($pagamentoItem->pagamento->status){
                    $response = true;
                }
            }
        }
        $modelsAll = PagamentoOrdemItem::find()
                ->where(['fin_despesa_id'=>$fin_despesa_id])
                ->all();
        
        if (!empty($modelsAll)) {
            foreach($modelsAll as $pagamentoOrdemItem){
                if($pagamentoOrdemItem->pagamentoOrdem->status){
                    $response = true;
                }
            }
        }
        return $response;
    }
    

    /**
     * Vereficar se a despesa foi criado antes da FP e agora já tem FP
     * @var integer
     */
    public function foiCriadoAntesFaturaProvisoria($fin_despesa_id)
    {
        $model = Despesa::findOne($fin_despesa_id);
        if (empty($model->fin_recebimento_id)&&!empty($model->dsp_processo_id)&&!empty($model->dsp_fatura_provisoria_id)) {
            $fatura = FaturaProvisoria::find()->where(['id'=>$model->dsp_fatura_provisoria_id])->one();
            if (!empty($fatura)) {
                if($model->created_at < $fatura->created_at){
                    return true;
                }
            }
        }
        return false;
    } 

     
}
?>



