<?php
namespace app\commands;
use yii;
use yii\console\Controller;
use app\modules\cnt\models\Documento;
use app\modules\cnt\components\RazaoAutoCreate;

class RazaoController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionAutoUpdate($cnt_documento_id, $dataInicio, $dataFim)
    {
        $ok = 0;
        echo $cnt_documento_id.' - data inicio: '. $dataInicio. ' - data fim: '.$dataFim;
                    # DOCUMENTO FATURA DEFINITIVA
                    if ($cnt_documento_id == Documento::FATURA_DEFINITIVA) {
                        $ok = $ok + RazaoAutoCreate::createByFaturaDefinitiva($dataInicio, $dataFim) ; 
                    }
                    // end lisat dos documentos



                    # DOCUMENTO createByMovimentoInterno
                    if ($cnt_documento_id == Documento::MOVIMENTO_INTERNO) {
                        $ok = $ok + RazaoAutoCreate::createByMovimentoInterno($dataInicio, $dataFim) ; 
                    }
                    // end createByMovimentoInterno


                    // # DOCUMENTODESPESA_FATURA_FORNECEDOR
                    if ($cnt_documento_id == Documento::DESPESA_FATURA_FORNECEDOR) {
                        $ok = $ok + RazaoAutoCreate::createByFaturaFornecidor($dataInicio, $dataFim) ; 
                    }
                    // end DESPESA_FATURA_FORNECEDOR

                    // # FATURA_FORNECEDOR_INVESTIMENTO
                    if ($cnt_documento_id == Documento::FATURA_FORNECEDOR_INVESTIMENTO) {
                        $ok = $ok + RazaoAutoCreate::createByFaturaFornecidorInvestimento($dataInicio, $dataFim) ; 
                    }
                    // end FATURA_FORNECEDOR_INVESTIMENTO

                     // # RECEBIMENTO_TESOURARIO
                    if ($cnt_documento_id == Documento::RECEBIMENTO_TESOURARIO) {
                        $ok = $ok + RazaoAutoCreate::createByRecebimentoTesouraria($dataInicio, $dataFim) ; 
                    }
                    // end RECEBIMENTO_TESOURARIO

                     // # RECEBIMENTO_FATURA_PROVISORIA
                    if ($cnt_documento_id == Documento::RECEBIMENTO_FATURA_PROVISORIA) {
                        $ok = $ok + RazaoAutoCreate::createByRecebimentoFaturaProviria($dataInicio, $dataFim) ; 
                    }
                    // end RECEBIMENTO_FATURA_PROVISORIA

                    // # RECEBIMENTO_FATURA_PROVISORIA
                    if ($cnt_documento_id == Documento::RECEBIMENTO_ADIANTAMENTO) {
                        $ok = $ok + RazaoAutoCreate::createByRecebimentoAdiantamento($dataInicio, $dataFim) ; 
                    }
                    // end RECEBIMENTO_FATURA_PROVISORIA 

                    // # RECEBIMENTO_FATURA_PROVISORIA
                    if ($cnt_documento_id == Documento::RECEBIMENTO_REEMBOLSO) {
                        $ok = $ok + RazaoAutoCreate::createByRecebimentoReembolso( $dataInicio, $dataFim) ; 
                    }
                    // end RECEBIMENTO_FATURA_PROVISORIA



                    # DOCUMENTO FATURA PAGAMENTO
                    if ($cnt_documento_id == Documento::PAGAMENTO) {
                         $ok = $ok + RazaoAutoCreate::createByPagamento($dataInicio, $dataFim);
                    }
                    // end lisat dos documentos
              echo $ok.' Documento porcessado com sucesso.';  
    }
        
}
