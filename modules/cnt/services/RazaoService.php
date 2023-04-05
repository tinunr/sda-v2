<?php
namespace app\modules\cnt\services;


use yii;
use yii\helpers\Html;
use yii\helpers\Url;

use app\modules\cnt\models\Razao;
use app\modules\cnt\models\Documento;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\Pagamento;
use app\modules\fin\models\Transferencia;
use app\modules\fin\models\Recebimento;
/**
* 
*/
class RazaoService 
{   

     

    /**
     * Lists all User models.
     * @return mixed
     */
    public static function ficheiroData($id)
    {

        $model = Razao::findOne($id);
        switch ($model->cnt_documento_id) {
            case Documento::PAGAMENTO :// pagamento
              
                $origem = Pagamento::findOne($model->documento_origem_id);  
                $ano = \app\models\Ano::findOne($origem->bas_ano_id)->ano;
                if (!empty($origem->fin_pagamento_ordem_id)) {
                    $anoOp = \app\models\Ano::findOne($origem->pagamentoOrdem->bas_ano_id)->ano;
                }
                $item_id = $origem->id;
                $action_id = 'fin_pagamento'; 
                $dir = 'data/pagamentos/' . $ano . '/' . $origem->numero; 
                $dir_complemento =   !empty($origem->fin_pagamento_ordem_id) ? 'data/pagamentos_ordem/' . $anoOp . '/' . $origem->pagamentoOrdem->numero : ''; 
                break;
            case Documento::DESPESA_FATURA_FORNECEDOR :// despesas
               print_r($model->cnt_documento_id);die();
                $origem = Despesa::findOne($model->documento_origem_id);                
                $ano = \app\models\Ano::findOne($origem->bas_ano_id)->ano;
                $item_id = $origem->id;
                $action_id = 'fin_despesa';
                $dir = 'data/despesas/' . $ano . '/' . $origem->id;
                $dir_complemento = null; 
                break;
             case  Documento::FATURA_FORNECEDOR_INVESTIMENTO:// despesas
               print_r($model->cnt_documento_id);die();
                $origem = Despesa::findOne($model->documento_origem_id);                
                $ano = \app\models\Ano::findOne($origem->bas_ano_id)->ano;
                $item_id = $origem->id;
                $action_id = 'fin_despesa';
                $dir = 'data/despesas/' . $ano . '/' . $origem->id;
                $dir_complemento = null; 
                break;
             case  Documento::MOVIMENTO_INTERNO:// transferencia
                $origem = Transferencia::findOne($model->documento_origem_id);  
                $item_id = $origem->id;
                $action_id = 'fin_transferencia';
                $dir_complemento = null;
                $dir = 'data/transferencias/'.date('Y', strtotime($origem->data)) . '/' . $origem->numero; 
                break; 
            case Documento::RECEBIMENTO_FATURA_PROVISORIA:// Recebimento de Reembolso Alfândega
                $origem = Recebimento::findOne($model->documento_origem_id);  
                $ano = \app\models\Ano::findOne($origem->bas_ano_id)->ano;
                $item_id = $origem->id;
                $action_id = 'fin_recebimento';
                $dir = 'data/recebimentos/' . $ano . '/' . $origem->numero;
                $dir_complemento = null;
                break;  
             case Documento::RECEBIMENTO_ADIANTAMENTO:// Recebimento de Reembolso Alfândega
                $origem = Recebimento::findOne($model->documento_origem_id);  
                $ano = \app\models\Ano::findOne($origem->bas_ano_id)->ano;
                $item_id = $origem->id;
                $action_id = 'fin_recebimento';
                $dir = 'data/recebimentos/' . $ano . '/' . $origem->numero;
                $dir_complemento = null;
                break; 
             case Documento::RECEBIMENTO_REEMBOLSO:// Recebimento de Reembolso Alfândega
                $origem = Recebimento::findOne($model->documento_origem_id);  
                $ano = \app\models\Ano::findOne($origem->bas_ano_id)->ano;
                $item_id = $origem->id;
                $action_id = 'fin_recebimento';
                $dir = 'data/recebimentos/' . $ano . '/' . $origem->numero;
                $dir_complemento = null;
                break;
             case Documento::RECEBIMENTO_TESOURARIO:// Recebimento de Reembolso Alfândega
                $origem = Recebimento::findOne($model->documento_origem_id);  
                $ano = \app\models\Ano::findOne($origem->bas_ano_id)->ano;
                $item_id = $origem->id;
                $action_id = 'fin_recebimento';
                $dir = 'data/recebimentos/' . $ano . '/' . $origem->numero;
                $dir_complemento = null;
                break; 
            default:               
                $ano = \app\models\Ano::findOne($model->bas_ano_id)->ano; 
                $item_id = $model->id;
                $action_id = 'cnt_razao';
                $dir = 'data/contabilidade/' . $ano.'/'.$model->diario->dir_file . '/' .$model->bas_mes_id.'/'. $model->numero;
                $dir_complemento = null;
                break;
        }

        return [
            'item_id' => $item_id,
            'action_id' => $action_id,
            'dir' =>  $dir,
            'dir_complemento'=>$dir_complemento,
        ];
    }



   

     
}
?>