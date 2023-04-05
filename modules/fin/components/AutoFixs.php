<?php
namespace app\modules\fin\components;


use yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;
use yii\db\Expression;


use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\Receita;

/**
* 
*/
class AutoFixs extends Component
{   

    /**
     * @var integer
     */
    public function bugApdateValorRecebido($fin_fatura_provisoria)
    {
        $valor_recebido = 0;
        $fatauraProvisoria = FaturaProvisoria::findOne($fin_fatura_provisoria);
        $receita = Receita::findOne(['dsp_fataura_provisoria_id'=>$fatauraProvisoria->id]);

        // valor recebido atraver do recebimento
        $valorRecebidoRecebimento = (new \yii\db\Query())
                ->from('fin_recebimento_item A')
                ->leftJoin('fin_recebimento B','A.fin_recebimento_id=B.id')
                ->where(['A.fin_receita_id'=>$receita->id])
                ->andWhere('B.status=1')
                ->sum('A.valor_recebido');

        $valor_recebido = $valor_recebido + $valorRecebidoRecebimento;
        // valor recebido atraver de encontro de conta
        $valorRecebidoEncontroContaA = (new \yii\db\Query())
                ->from('fin_of_accounts_item A')
                ->leftJoin('fin_of_accounts B','A.fin_of_account_id=B.id')
                ->where(['A.fin_receita_id'=>$receita->id])
                ->andWhere('B.status=1')
                ->sum('A.valor');
        $valor_recebido = $valor_recebido + $valorRecebidoEncontroContaA;
        // print_r($valorRecebidoEncontroContaA);die();

        $valorRecebidoEncontroContaB = (new \yii\db\Query())
                ->from('fin_of_accounts A')
                ->where(['A.fin_receita_id'=>$receita->id])
                ->andWhere('A.status=1')
                ->sum('A.valor');
        $valor_recebido = $valor_recebido + $valorRecebidoEncontroContaB;

        $receita->valor_recebido = $valor_recebido;
        $receita->saldo = $receita->valor-$valor_recebido;
        if($receita->save()){
            return true;
        }
        return false;
    } 

     
}
?>



