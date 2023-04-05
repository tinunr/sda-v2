<?php

namespace app\modules\fin\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaItem;
use app\modules\dsp\models\Item;

class DespesaItemBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_AFTER_INSERT => 'create',
           ActiveRecord::EVENT_BEFORE_INSERT => 'beforecreate',
           ActiveRecord::EVENT_BEFORE_DELETE => 'delete',
           ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
           ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
       ];
   }

  public function beforecreate(Event $event)
   {
   //     if(($despesa = Despesa::findOne(['id'=>$this->owner->fin_despesa_id]))!=null){
   //      $despesa->valor  = $despesa->valor + $this->owner->valor;
   //      $despesa->saldo  = $despesa->valor - $despesa->valor_pago;
   //      $despesa->save();
   //     }

       $this->owner->item_descricao = Item::findOne($this->owner->item_id)->descricao;
       // $this->owner->save();
      
  }
  public function create(Event $event)
   {
       if(($despesa = Despesa::findOne(['id'=>$this->owner->fin_despesa_id]))!=null){
        $despesa->valor  = $despesa->valor + $this->owner->valor;
        $despesa->saldo  = $despesa->valor - $despesa->valor_pago;
        $despesa->save();
       }

       // $this->owner->item_descricao = Item::findOne($this->owner->item_id)->descricao;
       // $this->owner->save();
      
  }

  public function delete(Event $event)
   {
       if(($despesa = Despesa::findOne(['id'=>$this->owner->fin_despesa_id]))!=null){
         $despesa->valor  =  DespesaItem::find()
                        ->where(['fin_despesa_id' =>$this->owner->fin_despesa_id])
                        ->sum('valor');
        $despesa->saldo  = $despesa->valor - $despesa->valor_pago;
        $despesa->save();
       }
      
  }

  public function beforeUpdate(Event $event)
   {
       // $this->owner->item_descricao = Item::findOne($this->owner->item_id)->descricao;
       if(($despesa = Despesa::findOne(['id'=>$this->owner->fin_despesa_id]))!=null){
         $despesa->valor  =  DespesaItem::find()
                        ->where(['fin_despesa_id' =>$this->owner->fin_despesa_id])
                        ->sum('valor');
        $despesa->saldo  = $despesa->valor - $despesa->valor_pago;
        $despesa->save();
       }
      
  }

  public function afterUpdate(Event $event)
   {
    if(($despesa = Despesa::findOne(['id'=>$this->owner->fin_despesa_id]))!=null){
      $despesa->valor  =  DespesaItem::find()
                        ->where(['fin_despesa_id' =>$this->owner->fin_despesa_id])
                        ->sum('valor');
      $despesa->saldo  = $despesa->valor - $despesa->valor_pago;
      $despesa->save();
    }
  }

  


}