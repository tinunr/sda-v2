<?php

namespace app\modules\fin\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\BancoConta;

class CaixaTransacaoBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_BEFORE_INSERT => 'create',
           ActiveRecord::EVENT_BEFORE_UPDATE => 'update',
           ActiveRecord::EVENT_BEFORE_DELETE => 'delete',
       ];
   }

   public function create(Event $event)
   {

      if($this->owner->valor_entrada > 0 ){
        Yii::$app->FinCaixa->adicionarSaldoConta($this->owner->caixa->fin_banco_conta_id, $this->owner->valor_entrada);
      }
      if($this->owner->valor_saida >0){
        Yii::$app->FinCaixa->subtrairSaldoConta($this->owner->caixa->fin_banco_conta_id, $this->owner->valor_saida);
      }
      $this->owner->saldo =  Yii::$app->FinCaixa->saldoConta($this->owner->caixa->fin_banco_conta_id);

  }


  public function update(Event $event)
  {
      // if($this->owner->valor_entrada > 0 && $this->owner->status==1){
      //   Yii::$app->FinCaixa->adicionarSaldoConta($this->owner->caixa->fin_banco_conta_id, $this->owner->valor_entrada);
      // }
      // if($this->owner->valor_saida >0 && $this->owner->status==1){
      //   Yii::$app->FinCaixa->subtrairSaldoConta($this->owner->caixa->fin_banco_conta_id, $this->owner->valor_saida);
      // }
      // $this->owner->saldo =  Yii::$app->FinCaixa->saldoConta($this->owner->caixa->fin_banco_conta_id);

  }

   public function delete(Event $event)
   {
      if($this->owner->valor_entrada > 0 ){
        $new_saldo = $this->owner->saldo - $this->owner->valor_entrada;
        // Yii::$app->FinCaixa->setSaldoConta($this->owner->caixa->fin_banco_conta_id, $new_saldo);
        BancoConta::atualizarSaldo($this->owner->caixa->fin_banco_conta_id, $new_saldo);

      }
      if($this->owner->valor_saida > 0){
        $new_saldo = $this->owner->saldo + $this->owner->valor_saida;
        // Yii::$app->FinCaixa->setSaldoConta($this->owner->caixa->fin_banco_conta_id, $new_saldo);
        BancoConta::atualizarSaldo($this->owner->caixa->fin_banco_conta_id, $new_saldo);

      }


      $caixa = Caixa::findOne($this->owner->fin_caixa_id);
      $caixaConta = Caixa::find()
              ->where(['fin_banco_conta_id'=>$caixa->fin_banco_conta_id])
              ->andWhere(['>=','id',$this->owner->fin_caixa_id])
              ->orderBy('id')
              ->all();
      if (!empty($caixaConta)) {     
          foreach ($caixaConta as $key => $value) {
            $caixaTransacao = CaixaTransacao::find()
                          ->where(['fin_caixa_id'=>$value->id])
                          ->orderBy('created_at')
                          ->all();
          if (!empty($caixaTransacao)) {
              foreach ($caixaTransacao as $key => $value) {
                $new_saldo =  Yii::$app->FinCaixa->saldoConta($value->caixa->fin_banco_conta_id); 
                if($value->valor_entrada > 0 ){
                Yii::$app->FinCaixa->adicionarSaldoConta($value->caixa->fin_banco_conta_id, $value->valor_entrada);
                  $value->saldo = $new_saldo + $value->valor_entrada ;
                }
                if($value->valor_saida >0 ){
                  Yii::$app->FinCaixa->subtrairSaldoConta($value->caixa->fin_banco_conta_id, $value->valor_saida);
                  $value->saldo = $new_saldo - $value->valor_saida ;
                }
                $value->save(false);
              }
            }
          } 
    }


        // Defenir novo saldo de uma conta
        // Yii::$app->FinCaixa->setSaldoConta($this->owner->caixa->fin_banco_conta_id, $new_saldo);
        BancoConta::atualizarSaldo($this->owner->caixa->fin_banco_conta_id, $new_saldo);

      
      

  }

  


}