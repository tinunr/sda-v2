<?php

namespace app\modules\cnt\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\cnt\models\Razao;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\PlanoConta;

class RazaoItemBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_AFTER_INSERT => 'after_create',
           ActiveRecord::EVENT_BEFORE_INSERT => 'before_create',
           ActiveRecord::EVENT_AFTER_UPDATE => 'after_update',
           ActiveRecord::EVENT_BEFORE_UPDATE => 'before_update',
           ActiveRecord::EVENT_AFTER_DELETE => 'after_delete',
       ];
   }

  public function before_create(Event $event){      
     $planoConta = PlanoConta::findOne($this->owner->cnt_plano_conta_id);
     if (!$planoConta->tem_plano_externo) {
      $this->owner->cnt_plano_terceiro_id = NULL;
     }
     if (!$planoConta->tem_plano_fluxo_caixa) {
      $this->owner->cnt_plano_fluxo_caixa_id = NULL;
     }
   //   if (!$planoConta->is_plano_conta_iva) {
   //    $this->owner->cnt_plano_iva_id = NULL;
   //   }
  }


  public function after_create(Event $event){
       if(($razao = Razao::findOne(['id'=>$this->owner->cnt_razao_id]))!=NULL){
        $query = RazaoItem::find()
                ->where(['cnt_razao_id' =>$this->owner->cnt_razao_id])
                ->andWhere(['cnt_natureza_id' =>$this->owner->cnt_natureza_id]);

               //  print_r($this->owner);die();
        if ($this->owner->cnt_natureza_id == 'C') {
           $razao->valor_credito = $query->sum('valor')==null?0:$query->sum('valor') ; ;
        }else{
           $razao->valor_debito = $query->sum('valor')==null?0:$query->sum('valor') ;
        }
        
        $razao->save(FALSE);
       }
      
  }

  public function after_delete(Event $event){
    if(($razao = Razao::findOne(['id'=>$this->owner->cnt_razao_id]))!=NULL){
        $query = RazaoItem::find()
                ->where(['cnt_razao_id' =>$this->owner->cnt_razao_id])
                ->andWhere(['cnt_natureza_id' =>$this->owner->cnt_natureza_id]);
        if ($this->owner->cnt_natureza_id == 'C') {
           $razao->valor_credito = $query->sum('valor')==null?0:$query->sum('valor') ; ;
        }else{
           $razao->valor_debito = $query->sum('valor')==null?0:$query->sum('valor') ; ;
        }
        
        $razao->save(FALSE);
       }
      
  }

  public function before_update(Event $event){
      $planoConta = PlanoConta::findOne($this->owner->cnt_plano_conta_id);
      if (!$planoConta->tem_plano_externo) {
         $this->owner->cnt_plano_terceiro_id = NULL;
      }
      if (!$planoConta->tem_plano_fluxo_caixa) {
         $this->owner->cnt_plano_fluxo_caixa_id = NULL;
      }
      // if (!$planoConta->is_plano_conta_iva) {
      // $this->owner->cnt_plano_iva_id = NULL;
      // }    
  }

  public function after_update(Event $event){
    if(($razao = Razao::findOne(['id'=>$this->owner->cnt_razao_id]))!=NULL){
        $query = RazaoItem::find()
                ->where(['cnt_razao_id' =>$this->owner->cnt_razao_id])
                ->andWhere(['cnt_natureza_id' =>$this->owner->cnt_natureza_id]);
        if ($this->owner->cnt_natureza_id == 'C') {
           $razao->valor_credito = $query->sum('valor')==null?0:$query->sum('valor') ; ;
        }else{
           $razao->valor_debito = $query->sum('valor')==null?0:$query->sum('valor') ; ;
        }
        
        $razao->save(FALSE);
       }
  }

  


}