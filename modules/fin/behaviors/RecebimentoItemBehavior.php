<?php

namespace app\modules\fin\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\CaixaOperacao;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\DespesaItem;
use app\models\Documento;
use app\models\DocumentoNumero;

class RecebimentoItemBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_AFTER_INSERT => 'aftercreate',
           ActiveRecord::EVENT_BEFORE_UPDATE => 'update',
       ];
   }




   public function aftercreate(Event $event)
   {

    #print_r($this->owner->receita->dsp_fataura_provisoria_id);die();
    /********************** gerar despesas ****************/
    /*if(($despesa = Despesa::findOne(['dsp_fatura_provisoria_id'=>$this->owner->receita->dsp_fataura_provisoria_id, 'status'=>1,'fin_nota_credito_id'=>null]))==null){


       $pagamentos = [];                     
       $i = 0;
       $j = 0;
      $faturaProvisoriaItems = $this->owner->receita->faturaProvisoria->faturaProvisoriaItem;
      //----

      foreach ($faturaProvisoriaItems as $key => $modelFaturaProvisoriaItem) {
        $i= $modelFaturaProvisoriaItem->item->dsp_person_id;

             if (array_key_exists($i,$pagamentos)) {
                    $pagamentos[$i][$j]=   [
                        'dsp_processo_id'=>$this->owner->receita->faturaProvisoria->dsp_processo_id,
                        'dsp_fatura_provisoria_id'=>$this->owner->receita->dsp_fataura_provisoria_id,
                        'dsp_person_id'=>$modelFaturaProvisoriaItem->item->dsp_person_id,
                        'dsp_item_id'=>$modelFaturaProvisoriaItem->item->id,
                        'valor'=>$modelFaturaProvisoriaItem->valor,
                        'item_origem_id'=>$modelFaturaProvisoriaItem->item_origem_id,
                    ] ;
                    $j++;
                }else{
                    $pagamentos[$i][$j]= [
                        'dsp_processo_id'=>$this->owner->receita->faturaProvisoria->dsp_processo_id,
                        'dsp_fatura_provisoria_id'=>$this->owner->receita->dsp_fataura_provisoria_id,
                        'dsp_person_id'=>$modelFaturaProvisoriaItem->item->dsp_person_id,
                        'dsp_item_id'=>$modelFaturaProvisoriaItem->item->id,
                        'valor'=>$modelFaturaProvisoriaItem->valor,
                        'item_origem_id'=>$modelFaturaProvisoriaItem->item_origem_id,
                    ] ;
                    $j++;
                    
            }
        }

        foreach ($pagamentos as $key => $data) {

            if ($key!=1) {

                        $despesaNumero = DocumentoNumero::findByDocumentId(Documento::DESPESA);
                        // print_r($despesaNumero);die();
                        $despesa = new Despesa();
                        $despesa->dsp_processo_id = $this->owner->receita->faturaProvisoria->dsp_processo_id;                        
                        $despesa->fin_recebimento_id = $this->owner->fin_recebimento_id;
                        $despesa->dsp_fatura_provisoria_id = $this->owner->receita->dsp_fataura_provisoria_id;
                        $despesa->dsp_person_id = $key;
                        $despesa->numero = $despesaNumero->getNexNumber();
                        $despesa->valor = 0;
                        $despesa->valor_pago = 0;
                        $despesa->saldo =0;
                        $despesa->bas_ano_id = $this->owner->receita->faturaProvisoria->bas_ano_id;
                        $despesa->recebido = 1;
                        $despesa->descricao = 'Despesa gerada FP nº '.$this->owner->receita->faturaProvisoria->numero;

                        if($despesa->save()){
                          $despesaNumero->saveNexNumber();
                          Yii::$app->getSession()->setFlash('success', 'DESPESA '.$despesa->numero.'/'.$despesa->bas_ano_id.' GERADO COM SUCESSO.');

                          foreach ($data as $id => $value) {
                              if ($value['item_origem_id']!='D') { 
                                  $despesaItem = new DespesaItem();
                                  $despesaItem->fin_despesa_id = $despesa->id;
                                  $despesaItem->dsp_fatura_provisoria_id = $this->owner->receita->dsp_fataura_provisoria_id;
                                  $despesaItem->item_id = $value['dsp_item_id'];
                                  $despesaItem->valor = $value['valor'];
                                  $despesaItem->save();
                              }

                          }
                      }else{
                          Yii::$app->getSession()->setFlash('errr', 'HOUVE UM ERRO AO GERAR A DESPESA.');

                      }

                 
            }


        }
      }else{
          Yii::$app->getSession()->setFlash('errr', 'Este processo já tinha uma despesa criada.');

      }*/
    /********************** end gerar despesas ****************/

    }


  





  public function update(Event $event)
   {
      
  }



}