<?php
namespace app\modules\fin\components;


use yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yiidb\Query;
use yii\helpers\Json;
use yii\web\JsonParser;


use app\modules\fin\models\Banco;
use app\modules\fin\models\BancoConta;
use app\modules\fin\models\Transferencia;
use app\modules\fin\models\BancoTransacao;

/**
* 
*/
class FinTransacao extends Component
{   
    const CONTA_CAIXA_ID = 1;

    /**
     * @var integer
     */
    public function saldoConta($fin_banco_conta_id=null)
    {
        if($fin_banco_conta_id){
            $conta = $fin_banco_conta_id;
        }else{
            $conta = self::CONTA_CAIXA_ID;
        }
        if(($model = BancoTransacao::findOne(['id'=>$conta]))!=null){
            return $model->saldo;
        }else{
            return 0;
        }
    }


    /**
     * @var integer
     */
    public function adicionarSaldoConta($fin_banco_conta_id_destino, $valor)
    {
        $destibo = BancoConta::findOne(['id'=>$fin_banco_conta_id_destino]);
        $destibo->saldo = $destibo->saldo + $valor;
        $destibo->save();
        return $destibo;
    }

    /**
     * @var integer
     */
    public function subtrairSaldoConta($fin_banco_conta_id_destino, $valor)
    {
        $destibo = BancoConta::findOne(['id'=>$fin_banco_conta_id_destino]);
        $destibo->saldo = $destibo->saldo - $valor;
        $destibo->save();
        return $destibo;
    }

    

     
}
?>



