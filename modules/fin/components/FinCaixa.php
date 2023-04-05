<?php
namespace app\modules\fin\components;


use yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;
use yii\db\Expression;


use app\modules\fin\models\Banco;
use app\modules\fin\models\BancoConta;
use app\modules\fin\models\Transferencia;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\CaixaTransacao;

/**
* 
*/
class FinCaixa extends Component
{   
    const CONTA_CAIXA_ID = 1;

    /**
     * Retorna caixa de uma determinada conta
     * 
     * @var integer fin_banco_conta_id
     * @return int
     */
    public function caixaId($fin_banco_conta_id)
    {
        $bancoConta = BancoConta::findOne(['id'=>$fin_banco_conta_id]);
        $max = (new \yii\db\Query())
               ->from('fin_caixa')
               ->where(['fin_banco_conta_id'=>$bancoConta->id])
               ->andWhere(['status'=>Caixa::OPEN_CAIXA])
               ->max('id');
        $caixa = Caixa::find()->where(['id'=>$max])->one();
        if ($caixa!=null) {
            return $caixa->id;   
        }
        else{
            $caixa = new Caixa;
            $caixa->status = Caixa::OPEN_CAIXA;
            $caixa->fin_banco_conta_id = $fin_banco_conta_id;
            $caixa->user_open_id = Yii::$app->user->identity->id;
            $caixa->data = date('Y-m-d');
            $caixa->data_abertura = new \yii\db\Expression('NOW()');
            $caixa->saldo_inicial = $this->saldoConta($fin_banco_conta_id);
            $caixa->descricao = $bancoConta->banco->descricao.' - Nº conta '.$bancoConta->numero.' - aberto aos '.new \yii\db\Expression('NOW()');
            if($caixa->save()){
                return $caixa->id;  
            }

        }
    }


     /**
     * @var integer
     */
    public function saldoConta($fin_banco_conta_id)
    {
        
        if(($model = BancoConta::findOne(['id'=>$fin_banco_conta_id]))!=null){
            return $model->saldo;
        }else{
            return 0;
        }
    }

    /**
     * @var integer
     */
    public function setSaldoConta($fin_banco_conta_id, $new_saldo)
    {
        
        if(($model = BancoConta::findOne(['id'=>$fin_banco_conta_id]))!=null){
            $model->saldo = $new_saldo;
            $model->save(false);
        }
    }


    /**
     * @var integer
     */
    public function adicionarSaldoConta($fin_banco_conta_id, $valor)
    {
        $destibo = BancoConta::findOne(['id'=>$fin_banco_conta_id]);
        $destibo->saldo = $destibo->saldo + $valor;
        $destibo->save(false);
        return $destibo;
    }

    /**
     * @var integer
     */
    public function subtrairSaldoConta($fin_banco_conta_id, $valor)
    {
        $destibo = BancoConta::findOne(['id'=>$fin_banco_conta_id]);
        $destibo->saldo = $destibo->saldo - $valor;
        $destibo->save(false);
        return $destibo;
    }




    /**
     * @var integer
     */
    public function caixaTransacaoSaldo($fin_caixa_transacao_id)
    {
        $caixatransacao = CaixaTransacao::findOne($fin_caixa_transacao_id);
        $caixa = Caixa::findOne($caixatransacao->fin_caixa_id);
        $bancoConta = BancoConta::findOne($caixa->fin_banco_conta_id);        
        if ($caixa->status == Caixa::OPEN_CAIXA) { 
            $entrada = (new \yii\db\Query())
                    ->from('fin_caixa_transacao')
                    ->where(['fin_caixa_id'=>$caixa->id])
                    ->andWhere(['<=','id', $fin_caixa_transacao_id])
                    ->orderBy('created_at')
                    ->sum('valor_entrada');
            $saida = (new \yii\db\Query())
                    ->from('fin_caixa_transacao')
                    ->where(['fin_caixa_id'=>$caixa->id])
                    ->andWhere(['<=','id', $fin_caixa_transacao_id])
                    ->orderBy('created_at')
                    ->sum('valor_saida');
            $caixatransacao->saldo=$caixa->saldo_inicial +$entrada-$saida;
            $caixatransacao->save();
            $bancoConta->saldo = $caixa->saldo_inicial +$entrada-$saida;
            $bancoConta->save();
        }
        return $caixatransacao->saldo;
    }

    /**
     * @var integer
     */
    public function caixaSaldoFecho($fin_caixa_id)
    {
        $caixa = Caixa::findOne($fin_caixa_id);
        $entrada = (new \yii\db\Query())
                ->from('fin_caixa_transacao')
                ->where(['fin_caixa_id'=>$caixa->id])
                ->orderBy('created_at')
                ->sum('valor_entrada');
        $saida = (new \yii\db\Query())
                ->from('fin_caixa_transacao')
                ->where(['fin_caixa_id'=>$caixa->id])
                ->orderBy('created_at')
                ->sum('valor_saida');
        return $caixa->saldo_inicial +(($entrada?$entrada:0)-($saida?$saida:0));
    }


   

    

     
}
?>



