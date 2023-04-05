<?php
namespace app\modules\fin\services;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\CaixaTransacao;

/**
* 
*/
class CaixaService 
{   

    /**
     * Vereficar se a despesa tem fatura definitiva
     * @var integer
     */
    public static function saldoCaixa($fin_caixa_id)
    {
        $caixa = Caixa::findOne($fin_caixa_id);
        $transacao = CaixaTransacao::find()
                ->where(['fin_caixa_id'=>$caixa->id])
                ->orderBy('id')
                ->one();
        return $transacao->valor_saida;
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public static function ImgCaixaStaus($caixa)
    {
        if (($oldCaixa = Caixa::find()
            ->where(['fin_banco_conta_id' => $caixa->fin_banco_conta_id])
            ->andWhere(['<', 'id', $caixa->id])
            ->orderBy('id DESC')
            ->one()) != null) {
            if ($caixa->saldo_inicial != $oldCaixa->saldo_fecho) {
                return  Html::img(Url::to('@web/img/24/status-busy.png'));
            }
        }
        if ($caixa->status == 1) {
            $img =  Html::img(Url::to('@web/img/24/open.png'));
        } elseif ($caixa->status == 2) {
            $img =  Html::img(Url::to('@web/img/24/lock.png'));
        }

        return $img;
    }

    

     
}
?>



