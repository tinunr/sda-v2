<?php

namespace app\modules\cnt\components;


use yii;
use yii\base\Component;
use app\modules\dsp\models\Item;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\Recebimento;
use app\modules\fin\models\Pagamento;
use app\modules\fin\models\Transferencia;
use app\modules\cnt\models\Diario;
use app\modules\cnt\models\Documento;
use app\modules\cnt\models\Natureza;
use app\modules\cnt\models\Razao;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoIva;
use app\modules\fin\models\BancoConta;
use app\modules\fin\models\Despesa;
use app\modules\fin\models\RecebimentoTipo;
use app\models\Parameter;
use app\modules\fin\components\FinQuery;
use app\modules\cnt\components\CntQuery;
use app\modules\fin\models\FaturaDebitoCliente;
use app\modules\fin\models\FaturaEletronica;

class RazaoAutoCreate extends Component
{




     /**
     * Sincronizar lançamento da razaão dos documentos Fatura  [\app\modules\fin\models\FaturaEletronica] num determinado periodo 
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     **/
    public function createByFatura($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        try {
            $documento = Documento::findOne(['id' => Documento::FACTURA]);
            $diario = Diario::findOne($documento->cnt_diario_id);
            $faturas = FaturaEletronica::find()
                ->where(['status' => 1])
                ->andWhere(['>=', 'data', $dataInicio])
                ->andWhere(['<=', 'data', $dataFim])
                ->andWhere(['>', 'valor', 0])
                ->all();
            if (!empty($faturas)) {
                foreach ($faturas as $fatura) {
                    $transaction = Yii::$app->db->beginTransaction();
                    if (($model = Razao::findOne(['cnt_documento_id' => Documento::FACTURA, 'documento_origem_id' => $fatura->id])) !== null) {
                        foreach ($model->razaoItem as  $value) {
                            RazaoItem::findOne($value->id)->delete();
                        }
                    } else {
                        $model = new Razao();
                        $model->cnt_documento_id = Documento::FACTURA;
                        $model->documento_origem_id = $fatura->id;
                    }
                    $model->documento_origem_numero = $fatura->numero . '/' . $fatura->bas_ano_id;
                    $model->status = 1;
                    $model->documento_origem_data = $fatura->data;
                    $model->valor_debito = $fatura->valor;
                    $model->valor_credito = $fatura->valor;
                    $model->cnt_diario_id = $diario->id;
                    $model->data = date('Y-m-d');
                    $model->descricao = $fatura->descricao;
                    if ($model->updated_at  =  $fatura->updated_at) {
                        $count = $count + 1;
                        if (!($model->save())) {
                            $errors = [
                                'errors' => $model->errors,
                                'model' => $model,
                                'faura' => $fatura,
                            ];
                            $transaction->rollBack();
                        }

                        $fpRc = FinQuery::fpFaturaDefinitiva($fatura->fin_fatura_definitiva_id);
                        $modelItem = new RazaoItem();
                        $modelItem->descricao = $documento->codigo . '-' . $fatura->processo->numero . ' FD ' . $fatura->numero . ' FP ' . $fpRc['fatura_provisorias'] . ' RC ' . $fpRc['recibos'];
                        $modelItem->cnt_razao_id = $model->id;
                        $modelItem->cnt_plano_conta_id = $documento->cnt_plano_conta_id;
                        $modelItem->cnt_natureza_id = $documento->cnt_natureza_id;
                        $modelItem->valor = $fatura->valor;
                        $modelItem->cnt_plano_terceiro_id = $documento->tem_plano_externo ? $fatura->dsp_person_id : null;
                        $modelItem->cnt_plano_iva_id = $documento->cnt_plano_iva_id;
                        $modelItem->cnt_plano_fluxo_caixa_id = $documento->cnt_plano_fluxo_caixa_id;
                        $modelItem->documento_origem_numero = $fatura->numero . '/' . $fatura->bas_ano_id;
                        $modelItem->documento_origem_tipo = $documento->codigo;
                        if (!($modelItem->save())) {
                            $errors = [
                                'errors' => $modelItem->errors,
                                'fatura' => $fatura,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }
                        foreach ($fatura->faturaEletronicaItem as $key => $faturaEletronicaItem) {
                            $item = Item::findOne(['id' => $faturaEletronicaItem->dsp_item_id]);
                            $modelItemTo = new RazaoItem();
                            $modelItemTo->descricao =  'PR  ' . $fatura->processo->numero . ' FD ' . $fatura->numero . ' FP ' . $fpRc['fatura_provisorias'] . ' RC ' . $fpRc['recibos'] . '-' . $item->descricao;
                            $modelItemTo->cnt_razao_id = $model->id;
                            $modelItemTo->cnt_plano_conta_id = $item->cnt_plano_conta_id;
                            $modelItemTo->cnt_natureza_id = ($documento->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;
                            $modelItemTo->valor = $faturaEletronicaItem->valor;
                            $modelItemTo->cnt_plano_terceiro_id = $item->planoConta->tem_plano_externo ? $fatura->dsp_person_id : null;
                            $modelItemTo->cnt_plano_iva_id = $item->cnt_plano_iva_id;
                            $modelItemTo->cnt_plano_fluxo_caixa_id = null;
                            $modelItemTo->documento_origem_numero = $fatura->numero . '/' . $fatura->bas_ano_id;
                            $modelItemTo->documento_origem_tipo = $documento->codigo;
                            $modelItemTo->dsp_item_id = $item->id;
                            if (!($modelItemTo->save())) {
                                $errors = [
                                    'errors' => $modelItemTo->errors,
                                    'fatura' => $faturaEletronicaItem,
                                ];
                                print_r($errors);
                                die();
                                $transaction->rollBack();
                            }
                        } 
                    }
                    $transaction->commit();
                }
            }
            return $count;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            die();
            Yii::warning($e->getMessage());
            $transaction->rollBack();
            throw $e;
        }
    }


    /**
     * Sincronizar lançamento da razaão dos documentos Fatura Debito cliente [\app\modules\fin\models\FaturaDebitoCliente] num determinado periodo 
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     **/
    public function createByFaturaDebitoCliente($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        try {
            $documento = Documento::findOne(['id' => Documento::NOTA_DE_DEBITO_CLIENTE]);
            $diario = Diario::findOne($documento->cnt_diario_id);
            $faturas = FaturaDebitoCliente::find()
                ->where(['status' => 1])
                ->andWhere(['>=', 'data', $dataInicio])
                ->andWhere(['<=', 'data', $dataFim])
                ->andWhere(['>', 'valor', 0])
                ->all();
            if (!empty($faturas)) {
                foreach ($faturas as $fatura) {
                    $transaction = Yii::$app->db->beginTransaction();
                    if (($model = Razao::findOne(['cnt_documento_id' => Documento::NOTA_DE_DEBITO_CLIENTE, 'documento_origem_id' => $fatura->id])) !== null) {
                        foreach ($model->razaoItem as  $value) {
                            RazaoItem::findOne($value->id)->delete();
                        }
                    } else {
                        $model = new Razao();
                        $model->cnt_documento_id = Documento::NOTA_DE_DEBITO_CLIENTE;
                        $model->documento_origem_id = $fatura->id;
                    }
                    $model->documento_origem_numero = $fatura->numero . '/' . $fatura->bas_ano_id;
                    $model->status = 1;
                    $model->documento_origem_data = $fatura->data;
                    $model->valor_debito = $fatura->valor;
                    $model->valor_credito = $fatura->valor;
                    $model->cnt_diario_id = $diario->id;
                    $model->data = date('Y-m-d');
                    $model->descricao = $fatura->descricao;
                    if ($model->updated_at  =  $fatura->updated_at) {
                        $count = $count + 1;
                        if (!($model->save())) {
                            $errors = [
                                'errors' => $model->errors,
                                'model' => $model,
                                'fatura_definitiva' => $fatura,
                            ];
                            $transaction->rollBack();
                        }

                        $fpRc = FinQuery::fpFaturaDefinitiva($fatura->id);
                        $modelItem = new RazaoItem();
                        $modelItem->descricao = $documento->codigo . '-' . $fatura->processo->numero . ' FDC ' . $fatura->numero.'/'.$fatura->bas_ano_id . ' FP ' . $fpRc['fatura_provisorias'] . ' RC ' . $fpRc['recibos'];
                        $modelItem->cnt_razao_id = $model->id;
                        $modelItem->cnt_plano_conta_id = $documento->cnt_plano_conta_id;
                        $modelItem->cnt_natureza_id = $documento->cnt_natureza_id;
                        $modelItem->valor = $fatura->valor;
                        $modelItem->cnt_plano_terceiro_id = $documento->tem_plano_externo ? $fatura->dsp_person_id : null;
                        $modelItem->cnt_plano_iva_id = $documento->cnt_plano_iva_id;
                        $modelItem->cnt_plano_fluxo_caixa_id = $documento->cnt_plano_fluxo_caixa_id;
                        $modelItem->documento_origem_numero = $fatura->numero . '/' . $fatura->bas_ano_id;
                        $modelItem->documento_origem_tipo = $documento->codigo;
                        if (!($modelItem->save())) {
                            $errors = [
                                'errors' => $modelItem->errors,
                                'fatura' => $fatura,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }
                        foreach ($fatura->faturaDebitoClienteItem as $key => $faturaDebitoClienteItem) {
                            $item = Item::findOne(['id' => $faturaDebitoClienteItem->dsp_item_id]);
                            $modelItemTo = new RazaoItem();
                            $modelItemTo->descricao =  'PR  ' . $fatura->processo->numero . ' FD ' . $fatura->numero . ' FP ' . $fpRc['fatura_provisorias'] . ' RC ' . $fpRc['recibos'] . '-' . $item->descricao;
                            $modelItemTo->cnt_razao_id = $model->id;
                            $modelItemTo->cnt_plano_conta_id = $item->cnt_plano_conta_id;
                            $modelItemTo->cnt_natureza_id = ($documento->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;
                            $modelItemTo->valor = $faturaDebitoClienteItem->valor;
                            $modelItemTo->cnt_plano_terceiro_id = $item->planoConta->tem_plano_externo ? $fatura->dsp_person_id : null;
                            $modelItemTo->cnt_plano_iva_id = $item->cnt_plano_iva_id;
                            $modelItemTo->cnt_plano_fluxo_caixa_id = null;
                            $modelItemTo->documento_origem_numero = $fatura->numero . '/' . $fatura->bas_ano_id;
                            $modelItemTo->documento_origem_tipo = $documento->codigo;
                            $modelItemTo->dsp_item_id = $item->id;
                            if (!($modelItemTo->save())) {
                                $errors = [
                                    'errors' => $modelItemTo->errors,
                                    'fatura_definitiva' => $faturaDebitoClienteItem,
                                ];
                                print_r($errors);
                                die();
                                $transaction->rollBack();
                            }
                        }
                    }
                    $transaction->commit();
                }
            }
            return $count;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            die();
            Yii::warning($e->getMessage());
            $transaction->rollBack();
            throw $e;
        }
    }


    /**
     * Sincronizar lançamento da razaão dos documentos Fatura definitiva [\app\modules\fin\models\FaturaDefinitiva] num determinado periodo
     * OBS: [Com implementação da Fatura eletronica so serão sincronizados faturas definitivas ate ano de 2021]
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     **/
    public function createByFaturaDefinitiva($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        try {
            $documento = Documento::findOne(['id' => Documento::FATURA_DEFINITIVA]);
            $diario = Diario::findOne($documento->cnt_diario_id);
            $faturaDefinitivas = FaturaDefinitiva::find()
                ->where(['status' => 1])
                ->andWhere(['>=', 'data', $dataInicio])
                ->andWhere(['<=', 'data', $dataFim])
                ->andWhere(['>', 'valor', 0])
                ->andWhere(['<=', 'bas_ano_id', 21])
                ->all();
            if (!empty($faturaDefinitivas)) {
                foreach ($faturaDefinitivas as $faturaDefinitiva) {
                    $transaction = Yii::$app->db->beginTransaction();
                    if (($model = Razao::findOne(['cnt_documento_id' => Documento::FATURA_DEFINITIVA, 'documento_origem_id' => $faturaDefinitiva->id])) !== null) {
                        foreach ($model->razaoItem as  $value) {
                            RazaoItem::findOne($value->id)->delete();
                        }
                    } else {
                        $model = new Razao();
                        $model->cnt_documento_id = Documento::FATURA_DEFINITIVA;
                        $model->documento_origem_id = $faturaDefinitiva->id;
                    }

                    $model->documento_origem_numero = $faturaDefinitiva->numero . '/' . $faturaDefinitiva->bas_ano_id;
                    $model->status = 1;
                    $model->documento_origem_data = $faturaDefinitiva->data;
                    $model->valor_debito = $faturaDefinitiva->valor;
                    $model->valor_credito = $faturaDefinitiva->valor;
                    $model->cnt_diario_id = $diario->id;
                    $model->data = date('Y-m-d');
                    $model->descricao = $faturaDefinitiva->descricao;
                    if ($model->updated_at  =  $faturaDefinitiva->updated_at) {
                        $count = $count + 1;

                        if (!($model->save())) {
                            $errors = [
                                'errors' => $model->errors,
                                'model' => $model,
                                'fatura_definitiva' => $faturaDefinitiva,
                            ];
                            $transaction->rollBack();
                        }

                        $fpRc = FinQuery::fpFaturaDefinitiva($faturaDefinitiva->id);
                        $modelItem = new RazaoItem();
                        $modelItem->descricao = $documento->codigo . '-' . $faturaDefinitiva->processo->numero . ' FD ' . $faturaDefinitiva->numero . ' FP ' . $fpRc['fatura_provisorias'] . ' RC ' . $fpRc['recibos'];
                        $modelItem->cnt_razao_id = $model->id;
                        $modelItem->cnt_plano_conta_id = $documento->cnt_plano_conta_id;
                        $modelItem->cnt_natureza_id = $documento->cnt_natureza_id;
                        $modelItem->valor = $faturaDefinitiva->valor;
                        $modelItem->cnt_plano_terceiro_id = $documento->tem_plano_externo ? $faturaDefinitiva->dsp_person_id : null;
                        $modelItem->cnt_plano_iva_id = $documento->cnt_plano_iva_id;
                        $modelItem->cnt_plano_fluxo_caixa_id = $documento->cnt_plano_fluxo_caixa_id;
                        $modelItem->documento_origem_numero = $faturaDefinitiva->numero . '/' . $faturaDefinitiva->bas_ano_id;
                        $modelItem->documento_origem_tipo = $documento->codigo;
                        if (!($modelItem->save())) {
                            $errors = [
                                'errors' => $modelItem->errors,
                                'fatura_definitiva' => $faturaDefinitiva,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }
                        foreach ($faturaDefinitiva->faturaDefinitivaItem as $key => $faturaDefinitivaItem) {
                            $item = Item::findOne(['id' => $faturaDefinitivaItem->dsp_item_id]);
                            $modelItemTo = new RazaoItem();
                            $modelItemTo->descricao =  'PR  ' . $faturaDefinitiva->processo->numero . ' FD ' . $faturaDefinitiva->numero . ' FP ' . $fpRc['fatura_provisorias'] . ' RC ' . $fpRc['recibos'] . '-' . $item->descricao;
                            $modelItemTo->cnt_razao_id = $model->id;
                            $modelItemTo->cnt_plano_conta_id = $item->cnt_plano_conta_id;
                            $modelItemTo->cnt_natureza_id = ($documento->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;
                            $modelItemTo->valor = $faturaDefinitivaItem->valor;
                            $modelItemTo->cnt_plano_terceiro_id = $item->planoConta->tem_plano_externo ? $faturaDefinitiva->dsp_person_id : null;
                            $modelItemTo->cnt_plano_iva_id = $item->cnt_plano_iva_id;
                            $modelItemTo->cnt_plano_fluxo_caixa_id = null;
                            $modelItemTo->documento_origem_numero = $faturaDefinitiva->numero . '/' . $faturaDefinitiva->bas_ano_id;
                            $modelItemTo->documento_origem_tipo = $documento->codigo;
                            $modelItemTo->dsp_item_id = $item->id;
                            if (!($modelItemTo->save())) {
                                $errors = [
                                    'errors' => $modelItemTo->errors,
                                    'fatura_definitiva' => $faturaDefinitivaItem,
                                ];
                                print_r($errors);
                                die();
                                $transaction->rollBack();
                            }
                        }
                    }
                    $transaction->commit();
                }
            }
            return $count;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            die();
            Yii::warning($e->getMessage());
            $transaction->rollBack();
            throw $e;
        }
    }







    /**
     * Sincronizar lançamento da razaão dos documentos Movimento Interno [\app\modules\fin\models\Transferencia] num determinado periodo
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     */
    public function createByMovimentoInterno($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        $flag = false;
        try {

            $documento = Documento::findOne(['id' => Documento::MOVIMENTO_INTERNO]);
            $planoConta = PlanoConta::findOne($documento->cnt_plano_conta_id);
            $movimentoInternos = Transferencia::find()
                ->where(['status' => 1])
                ->andWhere(['>=', 'data', $dataInicio])
                ->andWhere(['<=', 'data', $dataFim])
                ->andWhere(['!=', 'fin_banco_conta_id_origem', 8])
                ->andWhere(['!=', 'fin_banco_conta_id_destino', 8])
                ->orderBy('id')
                ->all();
            if (!empty($movimentoInternos)) {
                foreach ($movimentoInternos as $key => $movimentoInterno) {
                    $transaction = Yii::$app->db->beginTransaction();
                    $bancoContaOrigem = BancoConta::findOne($movimentoInterno->fin_banco_conta_id_origem);
                    $bancoContaDestino = BancoConta::findOne($movimentoInterno->fin_banco_conta_id_destino);

                    if ($bancoContaOrigem->cnt_diario_id == 2) {
                        $cnt_diario_id = 2;
                        $tem_plano_fluxo_caixa_id = $bancoContaOrigem->cnt_plano_fluxo_caixa_id;
                    } elseif ($bancoContaDestino->cnt_diario_id == 2) {
                        $cnt_diario_id = 2;
                        $tem_plano_fluxo_caixa_id = $bancoContaDestino->cnt_plano_fluxo_caixa_id;
                    } else {
                        $cnt_diario_id = 1;
                        $tem_plano_fluxo_caixa_id = $bancoContaDestino->cnt_plano_fluxo_caixa_id;
                    }



                    if (($model = Razao::findOne(['cnt_documento_id' => Documento::MOVIMENTO_INTERNO, 'documento_origem_id' => $movimentoInterno->id])) !== null) {
                    } else {
                        $model = new Razao();
                        $model->cnt_documento_id = Documento::MOVIMENTO_INTERNO;
                        $model->documento_origem_id = $movimentoInterno->id;
                    }

                    $model->documento_origem_numero = (string)$movimentoInterno->numero;
                    $model->status = 1;
                    $model->documento_origem_data = $movimentoInterno->data;
                    $model->valor_debito = $movimentoInterno->valor;
                    $model->valor_credito = $movimentoInterno->valor;
                    $model->cnt_diario_id = $cnt_diario_id;
                    $model->data = date('Y-m-d');
                    $model->descricao = $movimentoInterno->descricao;
                    if ($model->updated_at = $movimentoInterno->updated_at) {
                        $count = $count + 1;
                        foreach ($model->razaoItem as $key => $value) {
                            RazaoItem::findOne($value->id)->delete();
                        }
                        if (!$model->save()) {
                            $erros = [
                                'errors' => $model->errors,
                                'movimento_interno' => $movimentoInterno,
                            ];
                            print_r($erros);
                            die();

                            $transaction->rollBack();
                        }
                        $modelItemA = new RazaoItem();
                        $modelItemA->descricao = $documento->codigo . '-' . $movimentoInterno->numero . '(' . $bancoContaOrigem->banco->sigla . '/' . $bancoContaDestino->banco->sigla . ')';
                        $modelItemA->cnt_razao_id = $model->id;
                        $modelItemA->cnt_plano_conta_id = $bancoContaOrigem->cnt_plano_conta_id;
                        $modelItemA->cnt_natureza_id = 'C';
                        $modelItemA->valor = $movimentoInterno->valor;
                        $modelItemA->cnt_plano_terceiro_id = null;
                        $modelItemA->cnt_plano_iva_id = null;
                        $modelItemA->cnt_plano_fluxo_caixa_id = $tem_plano_fluxo_caixa_id;
                        $modelItemA->documento_origem_numero = (string)$movimentoInterno->numero;
                        $modelItemA->documento_origem_tipo = $documento->codigo;

                        if (!($flag = $modelItemA->save())) {
                            $errors = [
                                'errors' => $modelItemA->errors,
                                'movimento_interno' => $movimentoInterno,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }


                        $modelItemB = new RazaoItem();
                        $modelItemB->descricao = $documento->codigo . '-' . $movimentoInterno->numero . '(' . $bancoContaOrigem->banco->sigla . '/' . $bancoContaDestino->banco->sigla . ')';
                        $modelItemB->cnt_razao_id = $model->id;
                        $modelItemB->cnt_plano_conta_id = $bancoContaDestino->cnt_plano_conta_id;
                        $modelItemB->cnt_natureza_id = 'D';
                        $modelItemB->valor = $movimentoInterno->valor;
                        $modelItemB->cnt_plano_terceiro_id = null;
                        $modelItemB->cnt_plano_iva_id = null;
                        $modelItemB->cnt_plano_fluxo_caixa_id = $tem_plano_fluxo_caixa_id;
                        $modelItemB->documento_origem_numero = (string)$movimentoInterno->numero;
                        $modelItemB->documento_origem_tipo = $documento->codigo;
                        if (!($flag = $modelItemB->save())) {
                            $errors = [
                                'errors' => $modelItemB->errors,
                                'movimento_interno' => $movimentoInterno,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }
                    }
                    $transaction->commit();
                }
            }
            if ($flag) {
                return $count;
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
            die();
            Yii::warning($e->getMessage());
            $transaction->rollBack();
            throw $e;
        }
    } 


    /**
     * Sincronizar lançamento da razaão dos documentos Factura Fornecedor num determinado periodo
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     */
    public function createByFaturaFornecidor($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        try {
            $documento = Documento::findOne(['id' => Documento::DESPESA_FATURA_FORNECEDOR]);
            $planoConta = PlanoConta::findOne($documento->cnt_plano_conta_id);
            $diario = Diario::findOne($documento->cnt_diario_id);
            $despesas = Despesa::find()
                ->where(['status' => 1])
                ->andWhere(['cnt_documento_id' => Documento::DESPESA_FATURA_FORNECEDOR])
                ->andWhere(['>=', 'data', $dataInicio])
                ->andWhere(['<=', 'data', $dataFim])
                ->all();
            if (!empty($despesas)) {
                foreach ($despesas as $key => $despesa) {
                    $transaction = Yii::$app->db->beginTransaction();
                    // $count = $count + 1;
                    if (($model = Razao::findOne(['cnt_documento_id' => Documento::DESPESA_FATURA_FORNECEDOR, 'documento_origem_id' => $despesa->id])) !== null) {
                    } else {
                        $model = new Razao();
                        $model->cnt_documento_id = Documento::DESPESA_FATURA_FORNECEDOR;
                        $model->documento_origem_id = $despesa->id;
                    }
                    if ($model->updated_at = $despesa->updated_at) {
                        $count = $count + 1;
                        foreach ($model->razaoItem as $key => $value) {
                            RazaoItem::findOne($value->id)->delete();
                        }
                        $model->documento_origem_numero = $despesa->numero . '/' . $despesa->bas_ano_id;
                        $model->status = 1;
                        $model->documento_origem_data = $despesa->data;
                        $model->valor_debito = $despesa->valor;
                        $model->valor_credito = $despesa->valor;
                        $model->cnt_diario_id = $diario->id;
                        $model->data = date('Y-m-d');
                        $model->descricao = $despesa->descricao;

                        if (!$model->save()) {
                            print_r($model->errors);
                            die();
                            $transaction->rollBack();
                        }
                        $planoConta = PlanoConta::find()->where(['id' => $documento->cnt_plano_conta_id])->asArray()->one();
                        $modelItem = new RazaoItem();
                        $modelItem->descricao = $documento->codigo . '-' . $despesa->numero;
                        $modelItem->cnt_razao_id = $model->id;
                        $modelItem->cnt_plano_conta_id = $documento->cnt_plano_conta_id;
                        $modelItem->cnt_natureza_id = $documento->cnt_natureza_id;
                        $modelItem->valor = $despesa->valor;
                        $modelItem->cnt_plano_terceiro_id = $documento->tem_plano_externo ? $despesa->dsp_person_id : null;
                        $modelItem->cnt_plano_iva_id = $documento->cnt_plano_iva_id;
                        $modelItem->cnt_plano_fluxo_caixa_id = $planoConta['tem_plano_fluxo_caixa'] ? $documento->cnt_plano_fluxo_caixa_id : null;
                        $modelItem->documento_origem_numero = $despesa->numero . '/' . $despesa->bas_ano_id;
                        $modelItem->documento_origem_tipo = $documento->codigo;
                        if (!$modelItem->save()) {
                            $errors = [
                                'erros' => $modelItem->errors,
                                'fatura_fornecedor' => $despesa,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }
                        foreach ($despesa->despesaItem as $key => $despesaItem) {
                            $item = Item::findOne(['id' => $despesaItem->item_id]);
                            $modelItemTo = new RazaoItem();
                            $modelItemTo->descricao = $documento->codigo . '-' . $despesa->numero . '-' . $despesaItem->item_descricao;
                            $modelItemTo->cnt_razao_id = $model->id;
                            $modelItemTo->cnt_plano_conta_id = $item->cnt_plano_conta_id;
                            $modelItemTo->cnt_natureza_id = ($documento->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;
                            $modelItemTo->valor = $despesaItem->valor - $despesaItem->valor_iva;
                            $modelItemTo->cnt_plano_terceiro_id = $item->planoConta->tem_plano_externo ? $despesa->dsp_person_id : null;
                            $modelItemTo->cnt_plano_iva_id = $item->cnt_plano_iva_id;
                            $modelItemTo->cnt_plano_fluxo_caixa_id = $item->planoConta->tem_plano_fluxo_caixa ? $documento->cnt_plano_fluxo_caixa_id : null;
                            $modelItemTo->documento_origem_numero = $despesa->numero . '/' . $despesa->bas_ano_id;
                            $modelItemTo->dsp_item_id = $item->id;
                            $modelItemTo->documento_origem_tipo = $documento->codigo;
                            if (!$modelItemTo->save()) {
                                $errors = [
                                    'erros' => $modelItemTo->errors,
                                    'fatra_fornecedor' => $modelItemTo,
                                ];
                                print_r($errors);
                                die();
                                $transaction->rollBack();
                            }

                            if ($despesaItem->valor_iva > 0) {
                                $modelItemIva = new RazaoItem();
                                $modelItemIva->descricao = $documento->codigo . '-' . $despesa->numero . '-' . $despesaItem->item_descricao;
                                $modelItemIva->cnt_razao_id = $model->id;
                                $modelItemIva->cnt_plano_conta_id = $item->planoIva->cnt_plano_conta_id;
                                $modelItemIva->cnt_natureza_id = ($documento->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;
                                $modelItemIva->valor = $despesaItem->valor_iva;
                                $modelItemIva->cnt_plano_terceiro_id = $item->planoConta->tem_plano_externo ? $despesa->dsp_person_id : null;
                                $modelItemIva->cnt_plano_iva_id = $item->cnt_plano_iva_id;
                                $modelItemIva->cnt_plano_fluxo_caixa_id = null;
                                $modelItemIva->documento_origem_numero = (string)$despesa->numero;
                                $modelItemIva->dsp_item_id = $item->id;
                                $modelItemIva->documento_origem_tipo = $documento->codigo;
                                if (!$modelItemIva->save()) {
                                    $errors = [
                                        'origem' => $despesa,
                                        'fatura_fornecedor' => $modelItemIva->errors,
                                    ];
                                    print_r($errors);
                                    die();
                                    $transaction->rollBack();
                                }
                            }
                        }
                    }
                    $transaction->commit();
                }
            }
            return $count;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            die();
            Yii::warning($e->getMessage());
            $transaction->rollBack();
            throw $e;
        }
    }










    /**
     * Sincronizar lançamento da razaão dos documentos Factura Fornecedor de investimento num determinado periodo
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     */
    public function createByFaturaFornecidorInvestimento($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0; 
        $documento = Documento::findOne(['id' => Documento::FATURA_FORNECEDOR_INVESTIMENTO]);
        $planoConta = PlanoConta::findOne($documento->cnt_plano_conta_id);
        $diario = Diario::findOne($documento->cnt_diario_id);
        $despesas = Despesa::find()
            ->where(['status' => 1])
            ->andWhere(['cnt_documento_id' => Documento::FATURA_FORNECEDOR_INVESTIMENTO])
            ->andWhere(['>=', 'data', $dataInicio])
            ->andWhere(['<=', 'data', $dataFim])
            ->all();
        if (!empty($despesas)) {
            foreach ($despesas as $key => $despesa) {
                $transaction = Yii::$app->db->beginTransaction(); 
                if (($model = Razao::findOne(['cnt_documento_id' => Documento::FATURA_FORNECEDOR_INVESTIMENTO, 'documento_origem_id' => $despesa->id])) !== null) {
                } else {
                    $model = new Razao();
                    $model->cnt_documento_id = Documento::FATURA_FORNECEDOR_INVESTIMENTO;
                    $model->documento_origem_id = $despesa->id;
                }
                if ($model->updated_at = $despesa->updated_at) {
                    $count = $count + 1;
                    foreach ($model->razaoItem as $key => $value) {
                        RazaoItem::findOne($value->id)->delete();
                    }
                    $model->documento_origem_numero = $despesa->numero . '/' . $despesa->bas_ano_id;
                    $model->status = 1;
                    $model->documento_origem_data = $despesa->data;
                    $model->valor_debito = $despesa->valor;
                    $model->valor_credito = $despesa->valor;
                    $model->cnt_diario_id = $diario->id;
                    $model->data = date('Y-m-d');
                    $model->descricao = $despesa->descricao . ' - ' . $despesa->descricao;

                    if (!$model->save()) {
                        $errors = [
                            'origem' => $despesa,
                            'errors' => $model->errors,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }
                    $planoConta = PlanoConta::find()->where(['id' => $documento->cnt_plano_conta_id])->asArray()->one();
                    $modelItem = new RazaoItem();
                    $modelItem->descricao = $documento->codigo . ' ' . $despesa->numero;
                    $modelItem->cnt_razao_id = $model->id;
                    $modelItem->cnt_plano_conta_id = $planoConta['id'];
                    $modelItem->cnt_natureza_id = $documento->cnt_natureza_id;
                    $modelItem->valor = $despesa->valor;
                    $modelItem->cnt_plano_terceiro_id = $planoConta['tem_plano_externo'] ? $despesa->dsp_person_id  : null;
                    $modelItem->cnt_plano_iva_id = $documento->cnt_plano_iva_id;
                    $modelItem->cnt_plano_fluxo_caixa_id = $planoConta['tem_plano_fluxo_caixa'] ? $documento->cnt_plano_fluxo_caixa_id : null;
                    $modelItem->documento_origem_numero = (string)$despesa->numero;
                    $modelItem->documento_origem_tipo = $documento->codigo;
                    if (!$modelItem->save()) {
                        $errors = [
                            'fatura_fornecedor_ivestimento_a' => $modelItem->errors,
                            'origem' => $despesa,
                        ];
                        print_r($errors);
                        die();
                    }

                    foreach ($despesa->despesaItem as $key => $despesaItem) {
                        $item = Item::findOne(['id' => $despesaItem->item_id]);
                        $modelItemTo = new RazaoItem();
                        $modelItemTo->descricao = $documento->codigo . '-' . $despesa->numero . '-' . $despesaItem->item_descricao;
                        $modelItemTo->cnt_razao_id = $model->id;
                        $modelItemTo->cnt_plano_conta_id = $item->cnt_plano_conta_id;
                        $modelItemTo->cnt_natureza_id = ($documento->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;
                        $modelItemTo->valor = $despesaItem->valor - $despesaItem->valor_iva;
                        $modelItemTo->cnt_plano_terceiro_id = $item->planoConta->tem_plano_externo ? $despesa->dsp_person_id : null;
                        $modelItemTo->cnt_plano_iva_id = $item->cnt_plano_iva_id;
                        $modelItemTo->cnt_plano_fluxo_caixa_id = null;
                        $modelItemTo->documento_origem_numero = (string)$despesa->numero;
                        $modelItemTo->dsp_item_id = $item->id;
                        $modelItemTo->documento_origem_tipo = $documento->codigo;
                        if (!$modelItemTo->save()) {
                            $errors = [
                                'fatura_fornecedor_ivestimento' => $modelItemTo->errors,
                                'origem' => $item,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }

                        if ($despesaItem->valor_iva > 0) {
                            $modelItemIva = new RazaoItem();
                            $modelItemIva->descricao = $documento->codigo . '-' . $despesa->numero . '-' . $despesaItem->item_descricao;;
                            $modelItemIva->cnt_razao_id = $model->id;
                            $modelItemIva->cnt_plano_conta_id = $item->cnt_plano_conta_id;
                            $modelItemIva->cnt_natureza_id = ($documento->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;
                            $modelItemIva->valor = $despesaItem->valor_iva;
                            $modelItemIva->cnt_plano_terceiro_id = $item->planoConta->tem_plano_externo ? $despesa->dsp_person_id : null;
                            $modelItemIva->cnt_plano_iva_id = $item->cnt_plano_iva_id;
                            $modelItemIva->cnt_plano_fluxo_caixa_id = null;
                            $modelItemIva->documento_origem_numero = (string)$despesa->numero;
                            $modelItemIva->dsp_item_id = $item->id;
                            $modelItemIva->documento_origem_tipo = $documento->codigo;
                            if (!$modelItemIva->save()) {
                                print_r($modelItemIva->errors);
                                die();
                                $errors = [
                                    'fatura_fornecedor_ivestimento' => $modelItemIva->errors,
                                    'origem' => $item,
                                ];
                                print_r($errors);
                                die();
                                $transaction->rollBack();
                            }
                        }
                    }
                }
                $transaction->commit();
            }
        }
        return $count;
        // } catch (\Exception $e) {
        //     print_r($e->getMessage());die();
        //     Yii::warning($e->getMessage());
        //     $transaction->rollBack();
        //     throw $e;
        // } 

    }








    /**
     * Sincronizar lançamento da razaão dos documentos Recebimento Tesouraria [\app\modules\fin\models\Recebimento] num determinado periodo 
     *  ->andWhere(['fin_recebimento_tipo_id' => RecebimentoTipo::TESOURARIA])
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     **/
    public function createByRecebimentoTesouraria($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        // try {
        $documento = Documento::findOne(['id' => Documento::RECEBIMENTO_TESOURARIO]);
        $recebimentos = Recebimento::find()
            ->where(['status' => 1])
            ->andWhere(['fin_recebimento_tipo_id' => RecebimentoTipo::TESOURARIA])
            ->andWhere(['>=', 'data', $dataInicio])
            ->andWhere(['<=', 'data', $dataFim])
            ->all();
        if (!empty($recebimentos)) {
            foreach ($recebimentos as $key => $recebimento) {
                $transaction = Yii::$app->db->beginTransaction();
                $bancoConta = BancoConta::findOne($recebimento->fin_banco_conta_id);
                $planoContaBanco = PlanoConta::findOne($bancoConta->cnt_plano_conta_id);
                $diario = Diario::findOne($bancoConta->cnt_diario_id);
                // $count = $count + 1;
                if (($model = Razao::findOne(['cnt_documento_id' => Documento::RECEBIMENTO_TESOURARIO, 'documento_origem_id' => $recebimento->id])) !== null) {
                } else {
                    $model = new Razao();
                    $model->cnt_documento_id = Documento::RECEBIMENTO_TESOURARIO;
                    $model->documento_origem_id = $recebimento->id;
                }
                if (Parameter::getValue('CONTABILIDADE', 'ATUALIZAR_TODOS_LANCAMENTOS') || $model->updated_at <= $recebimento->updated_at) {
                    $count = $count + 1;
                    foreach ($model->razaoItem as $key => $value) {
                        RazaoItem::findOne($value->id)->delete();
                    }
                    $model->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $model->status = 1;
                    $model->documento_origem_data = $recebimento->data;
                    $model->valor_debito = $recebimento->valor;
                    $model->valor_credito = $recebimento->valor;
                    $model->cnt_diario_id = $diario->id;
                    $model->data = date('Y-m-d');
                    $model->descricao = $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;

                    if (!$model->save()) {
                        $errors = [
                            'errors' => $model->errors,
                            'origem' => $model,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }

                    $fluxos = [];
                    $query = CntQuery::cntRecebimentoTesourariaItem($recebimento->id);
                    foreach ($query as $key => $flx) {
                        if (array_key_exists($flx['cnt_plano_fluxo_caixa_id'], $fluxos)) {
                            $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] =   $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] + $flx['valor'];
                        } else {
                            $fluxos[$flx['cnt_plano_fluxo_caixa_id']] = [
                                'cnt_plano_fluxo_caixa_id' => $flx['cnt_plano_fluxo_caixa_id'],
                                'total' => $flx['valor'],
                            ];
                        }
                    }
                    foreach ($fluxos as $fluxo) {
                        $modelItem = new RazaoItem();
                        $modelItem->descricao = $documento->codigo . '-' . $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - fluxo  de caixa ' . $fluxo['cnt_plano_fluxo_caixa_id'] . ' - ' . $recebimento->descricao;
                        $modelItem->cnt_razao_id = $model->id;
                        $modelItem->cnt_plano_conta_id = $planoContaBanco->id;
                        $modelItem->cnt_natureza_id = $planoContaBanco->cnt_natureza_id;
                        $modelItem->valor = $fluxo['total'];
                        $modelItem->cnt_plano_terceiro_id = $planoContaBanco->tem_plano_externo ? $recebimento->dsp_person_id : null;
                        $modelItem->cnt_plano_iva_id = null;
                        $modelItem->cnt_plano_fluxo_caixa_id = $planoContaBanco->tem_plano_fluxo_caixa ? $fluxo['cnt_plano_fluxo_caixa_id'] : null;
                        $modelItem->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                        $modelItem->documento_origem_tipo = $documento->codigo;
                        if (!$modelItem->save()) {
                            $errors = [
                                'errors' => $modelItem->errors,
                                'origem' => $recebimento,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }
                    }

                    foreach ($recebimento->recebimentoItem as $key => $recebimentoItem) {
                        $item = Item::findOne(['id' => $recebimentoItem->dsp_item_id]);
                        $modelItemTo = new RazaoItem();
                        $modelItemTo->descricao = $documento->codigo . '-' . $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $item->descricao;
                        $modelItemTo->cnt_razao_id = $model->id;
                        $modelItemTo->cnt_plano_conta_id = $item->cnt_plano_conta_id;
                        $modelItemTo->cnt_natureza_id = ($planoContaBanco->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;
                        $modelItemTo->valor = $recebimentoItem->valor;
                        $modelItemTo->cnt_plano_terceiro_id = $item->planoConta->tem_plano_externo ? $recebimentoItem->dsp_person_id : null;
                        $modelItemTo->cnt_plano_iva_id = $item->cnt_plano_iva_id;
                        $modelItemTo->cnt_plano_fluxo_caixa_id = null;
                        $modelItemTo->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                        $modelItemTo->documento_origem_tipo = $documento->codigo;
                        if (!$modelItemTo->save()) {
                            $errors = [
                                'errors' => $modelItemTo->errors,
                                'origem' => $recebimentoItem,
                            ];
                            print_r($errors);
                            die();
                            $transaction->rollBack();
                        }
                    }
                }
                $transaction->commit();
            }
        }
        return $count;
        // } catch (\Exception $e) {
        //     print_r($e->getMessage());die();
        //     Yii::warning($e->getMessage());
        //     $transaction->rollBack();
        //     throw $e;
        // } 

    }









    /**
     * Sincronizar lançamento da razaão dos documentos Recebimento Proviria [\app\modules\fin\models\Recebimento] num determinado periodo 
     *  ->andWhere(['fin_recebimento_tipo_id' => RecebimentoTipo::CONTA_CORENTE])
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     **/
    public function createByRecebimentoFaturaProviria($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        // try {
        $documento = Documento::findOne(['id' => Documento::RECEBIMENTO_FATURA_PROVISORIA]);
        $planoConta = PlanoConta::findOne(['id' => $documento->cnt_plano_conta_id]);
        $recebimentos = Recebimento::find()
            ->where(['status' => 1])
            ->andWhere(['fin_recebimento_tipo_id' => RecebimentoTipo::CONTA_CORENTE])
            ->andWhere(['>=', 'data', $dataInicio])
            ->andWhere(['<=', 'data', $dataFim])
            ->andWhere(['!=', 'fin_banco_conta_id', 8])
            ->all();
        if (!empty($recebimentos)) {
            foreach ($recebimentos as $key => $recebimento) {
                $transaction = Yii::$app->db->beginTransaction();
                $bancoConta = BancoConta::findOne($recebimento->fin_banco_conta_id);
                $planoContaBanco = PlanoConta::findOne($bancoConta->cnt_plano_conta_id);
                $diario = Diario::findOne($bancoConta->cnt_diario_id);
                // $count = $count + 1;
                if (($model = Razao::findOne(['cnt_documento_id' => Documento::RECEBIMENTO_FATURA_PROVISORIA, 'documento_origem_id' => $recebimento->id])) !== null) {
                } else {
                    $model = new Razao();
                    $model->cnt_documento_id = Documento::RECEBIMENTO_FATURA_PROVISORIA;
                    $model->documento_origem_id = $recebimento->id;
                }
                if ($model->updated_at = $recebimento->updated_at) {
                    $count = $count + 1;
                    foreach ($model->razaoItem as $key => $value) {
                        RazaoItem::findOne($value->id)->delete();
                    }

                    $model->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $model->status = 1;
                    $model->valor_debito = $recebimento->valor;
                    $model->valor_credito = $recebimento->valor;
                    $model->cnt_diario_id = $bancoConta->cnt_diario_id;
                    $model->data = date('Y-m-d');
                    $model->documento_origem_data = $recebimento->data;
                    $model->descricao = $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;

                    if (!$model->save()) {
                        print_r($model->errors);
                        die();
                        $transaction->rollBack();
                    }
                    $modelItem_on = new RazaoItem();
                    $modelItem_on->descricao = $documento->codigo . '-' . $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;
                    $modelItem_on->cnt_razao_id = $model->id;
                    $modelItem_on->cnt_plano_conta_id = $planoContaBanco->id;
                    $modelItem_on->cnt_natureza_id = $planoContaBanco->cnt_natureza_id;
                    $modelItem_on->valor = $recebimento->valor;
                    $modelItem_on->cnt_plano_terceiro_id = $planoContaBanco->tem_plano_externo ? $recebimento->dsp_person_id : null;
                    $modelItem_on->cnt_plano_iva_id = null;
                    $modelItem_on->cnt_plano_fluxo_caixa_id = $planoContaBanco->tem_plano_fluxo_caixa ? $documento->cnt_plano_fluxo_caixa_id : null;
                    $modelItem_on->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $modelItem_on->documento_origem_tipo = $documento->codigo;
                    if (!$modelItem_on->save()) {
                        print_r($modelItem_on->errors);
                        die();
                        $transaction->rollBack();
                    }



                    $modelItem = new RazaoItem();
                    $modelItem->descricao = $documento->codigo . '-' . $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;
                    $modelItem->cnt_razao_id = $model->id;
                    $modelItem->cnt_plano_conta_id = $documento->cnt_plano_conta_id;
                    $modelItem->cnt_natureza_id = ($planoConta->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;;
                    $modelItem->valor = $recebimento->valor;
                    $modelItem->cnt_plano_terceiro_id = $planoConta->tem_plano_externo ? $recebimento->dsp_person_id : null;
                    $modelItem->cnt_plano_iva_id = null;
                    $modelItem->cnt_plano_fluxo_caixa_id = null;
                    $modelItem->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $modelItem->documento_origem_tipo = $documento->codigo;
                    if (!$modelItem->save()) {
                        print_r($modelItem->errors);
                        die();
                        $transaction->rollBack();
                    }
                }
                $transaction->commit();
            }
        }

        return $count;
        // } catch (\Exception $e) {
        //     print_r($e->getMessage());die();
        //     Yii::warning($e->getMessage());
        //     $transaction->rollBack();
        //     throw $e;
        // } 

    }







    /**
     * Sincronizar lançamento da razaão dos documentos Recebimento Adiantamento [\app\modules\fin\models\Recebimento] num determinado periodo 
     *  ->andWhere(['fin_recebimento_tipo_id' => RecebimentoTipo::ADIANTAMENTO])
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     **/
    public function createByRecebimentoAdiantamento($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        // try {
        $documento = Documento::findOne(['id' => Documento::RECEBIMENTO_ADIANTAMENTO]);
        $planoConta = PlanoConta::findOne(['id' => $documento->cnt_plano_conta_id]);
        $recebimentos = Recebimento::find()
            ->where(['status' => 1])
            ->andWhere(['fin_recebimento_tipo_id' => RecebimentoTipo::ADIANTAMENTO])
            ->andWhere(['>=', 'data', $dataInicio])
            ->andWhere(['<=', 'data', $dataFim])
            ->andWhere(['!=', 'fin_banco_conta_id', 8])
            ->all();
        if (!empty($recebimentos)) {
            foreach ($recebimentos as $key => $recebimento) {
                $transaction = Yii::$app->db->beginTransaction();
                $bancoConta = BancoConta::findOne($recebimento->fin_banco_conta_id);
                $planoContaBanco = PlanoConta::findOne($bancoConta->cnt_plano_conta_id);
                // $count = $count + 1;
                if (($model = Razao::findOne(['cnt_documento_id' => Documento::RECEBIMENTO_ADIANTAMENTO, 'documento_origem_id' => $recebimento->id])) !== null) {
                } else {
                    $model = new Razao();
                    $model->cnt_documento_id = Documento::RECEBIMENTO_ADIANTAMENTO;
                    $model->documento_origem_id = $recebimento->id;
                }
                if ($model->updated_at = $recebimento->updated_at) {
                    $count = $count + 1;
                    foreach ($model->razaoItem as $key => $value) {
                        RazaoItem::findOne($value->id)->delete();
                    }
                    $model->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $model->status = 1;
                    $model->valor_debito = $recebimento->valor;
                    $model->valor_credito = $recebimento->valor;
                    $model->cnt_diario_id = $bancoConta->cnt_diario_id;
                    $model->data = date('Y-m-d');
                    $model->documento_origem_data = $recebimento->data;
                    $model->descricao = $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;

                    if (!$model->save()) {
                        print_r($model->errors);
                        die();
                        $transaction->rollBack();
                    }
                    $modelItem_on = new RazaoItem();
                    $modelItem_on->descricao = $documento->codigo . '-' . $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;
                    $modelItem_on->cnt_razao_id = $model->id;
                    $modelItem_on->cnt_plano_conta_id = $planoContaBanco->id;
                    $modelItem_on->cnt_natureza_id = $planoContaBanco->cnt_natureza_id;
                    $modelItem_on->valor = $recebimento->valor;
                    $modelItem_on->cnt_plano_terceiro_id = $planoContaBanco->tem_plano_externo ? $recebimento->dsp_person_id : null;
                    $modelItem_on->cnt_plano_iva_id = null;
                    $modelItem_on->cnt_plano_fluxo_caixa_id = $planoContaBanco->tem_plano_fluxo_caixa ? $documento->cnt_plano_fluxo_caixa_id : null;
                    $modelItem_on->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $modelItem_on->documento_origem_tipo = $documento->codigo;
                    if (!$modelItem_on->save()) {
                        $errors = [
                            'error' =>
                            $modelItem_on->errors,
                            'origem' => $recebimento,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }



                    $modelItem = new RazaoItem();
                    $modelItem->descricao = $documento->codigo . '-' . $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;
                    $modelItem->cnt_razao_id = $model->id;
                    $modelItem->cnt_plano_conta_id = $documento->cnt_plano_conta_id;
                    $modelItem->cnt_natureza_id = ($planoConta->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;;
                    $modelItem->valor = $recebimento->valor;
                    $modelItem->cnt_plano_terceiro_id = $planoConta->tem_plano_externo ? $recebimento->dsp_person_id : null;
                    $modelItem->cnt_plano_iva_id = null;
                    $modelItem->cnt_plano_fluxo_caixa_id = null;
                    $modelItem->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $modelItem->documento_origem_tipo = $documento->codigo;
                    if (!$modelItem->save()) {
                        $errors = [
                            'errors' => $modelItem->errors,
                            'origem' => $recebimento,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }
                }
                $transaction->commit();
            }
        }
        return $count;
        // } catch (\Exception $e) {
        //     print_r($e->getMessage());die();
        //     Yii::warning($e->getMessage());
        //     $transaction->rollBack();
        //     throw $e;
        // } 

    }






    /**
     * Sincronizar lançamento da razaão dos documentos Recebimento Reembolso [\app\modules\fin\models\Recebimento] num determinado periodo 
     *  ->andWhere(['fin_recebimento_tipo_id' => RecebimentoTipo::REEMBOLSO])
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     **/
    public function createByRecebimentoReembolso($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        // try {
        $documento = Documento::findOne(['id' => Documento::RECEBIMENTO_REEMBOLSO]);
        $planoConta = PlanoConta::findOne(['id' => $documento->cnt_plano_conta_id]);
        $recebimentos = Recebimento::find()
            ->where(['status' => 1])
            ->andWhere(['fin_recebimento_tipo_id' => RecebimentoTipo::REEMBOLSO])
            ->andWhere(['>=', 'data', $dataInicio])
            ->andWhere(['<=', 'data', $dataFim])
            ->andWhere(['!=', 'fin_banco_conta_id', 8])
            ->all();
        if (!empty($recebimentos)) {
            foreach ($recebimentos as $key => $recebimento) {
                $transaction = Yii::$app->db->beginTransaction();
                $bancoConta = BancoConta::findOne($recebimento->fin_banco_conta_id);
                $planoContaBanco = PlanoConta::findOne($bancoConta->cnt_plano_conta_id);
                if (($model = Razao::findOne(['cnt_documento_id' => Documento::RECEBIMENTO_REEMBOLSO, 'documento_origem_id' => $recebimento->id])) !== null) {
                } else {
                    $model = new Razao();
                    $model->cnt_documento_id = Documento::RECEBIMENTO_REEMBOLSO;
                    $model->documento_origem_id = $recebimento->id;
                }
                if ($model->updated_at = $recebimento->updated_at) {
                    $count = $count + 1;
                    foreach ($model->razaoItem as $key => $value) {
                        RazaoItem::findOne($value->id)->delete();
                    }
                    $model->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $model->status = 1;
                    $model->valor_debito = $recebimento->valor;
                    $model->valor_credito = $recebimento->valor;
                    $model->cnt_diario_id = $bancoConta->cnt_diario_id;
                    $model->data = date('Y-m-d');
                    $model->documento_origem_data = $recebimento->data;
                    $model->descricao = $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;

                    if (!$model->save()) {
                        $errors = [
                            'errors' => $model->errors,
                            'origem' => $recebimento,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }
                    $modelItem_on = new RazaoItem();
                    $modelItem_on->descricao = $documento->codigo . '-' . $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;
                    $modelItem_on->cnt_razao_id = $model->id;
                    $modelItem_on->cnt_plano_conta_id = $planoContaBanco->id;
                    $modelItem_on->cnt_natureza_id = $planoContaBanco->cnt_natureza_id;
                    $modelItem_on->valor = $recebimento->valor;
                    $modelItem_on->cnt_plano_terceiro_id = $planoContaBanco->tem_plano_externo ? $recebimento->processo->nome_fatura : null;
                    $modelItem_on->cnt_plano_iva_id = null;
                    $modelItem_on->cnt_plano_fluxo_caixa_id = $planoContaBanco->tem_plano_fluxo_caixa ? $documento->cnt_plano_fluxo_caixa_id : null;
                    $modelItem_on->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $modelItem_on->documento_origem_tipo = $documento->codigo;
                    if (!$modelItem_on->save()) {
                        $errors = [
                            'errors' => $modelItem_on->errors,
                            'origem' => $recebimento,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }



                    $modelItem = new RazaoItem();
                    $modelItem->descricao = $documento->codigo . '-' . $recebimento->numero . '/' . $recebimento->bas_ano_id . ' - ' . $recebimento->descricao;
                    $modelItem->cnt_razao_id = $model->id;
                    $modelItem->cnt_plano_conta_id = $documento->cnt_plano_conta_id;
                    $modelItem->cnt_natureza_id = ($planoConta->cnt_natureza_id == Natureza::DEBITO) ? Natureza::CREDITO : Natureza::DEBITO;;
                    $modelItem->valor = $recebimento->valor;
                    $modelItem->cnt_plano_terceiro_id = $planoConta->tem_plano_externo ? $recebimento->processo->nome_fatura : null;
                    $modelItem->cnt_plano_iva_id = null;
                    $modelItem->cnt_plano_fluxo_caixa_id = null;
                    $modelItem->documento_origem_numero = $recebimento->numero . '/' . $recebimento->bas_ano_id;
                    $modelItem->documento_origem_tipo = $documento->codigo;
                    if (!$modelItem->save()) {
                        print_r($modelItem->errors);
                        die();
                        $transaction->rollBack();
                    }
                }
                $transaction->commit();
            }
        }

        return $count;
        // } catch (\Exception $e) {
        //     print_r($e->getMessage());die();
        //     Yii::warning($e->getMessage());
        //     $transaction->rollBack();
        //     throw $e;
        // } 

    }  




    /**
     * Sincronizar lançamento da razaão dos documentos Pagametos [\app\modules\fin\models\Pagamento] num determinado periodo 
     * @param date $dataInicio 
     * @param date $dataFim 
     * @return mixed
     **/
    public function createByPagamento($dataInicio, $dataFim)
    {
        set_time_limit(0);
        $count = 0;
        // try {
        $documento = Documento::findOne(['id' => Documento::PAGAMENTO]);
        $planoConta = PlanoConta::findOne($documento->cnt_plano_conta_id);
        $pagamentos = Pagamento::find()
            ->where(['status' => 1])
            ->andWhere(['>=', 'data', $dataInicio])
            ->andWhere(['<=', 'data', $dataFim])
            ->andWhere(['!=', 'fin_banco_conta_id', 8])
            //   ->andWhere(['id'=> 4871])
            ->all();
        if (!empty($pagamentos)) {
            foreach ($pagamentos as $key => $pagamento) {
                $transaction = Yii::$app->db->beginTransaction();
                $bancoConta = BancoConta::findOne($pagamento->fin_banco_conta_id);
                $planoContaBanco = PlanoConta::findOne($bancoConta->cnt_plano_conta_id);
                $diario = Diario::findOne($bancoConta->cnt_diario_id);
                // $count = $count + 1;
                if (($model = Razao::findOne(['cnt_documento_id' => Documento::PAGAMENTO, 'documento_origem_id' => $pagamento->id])) !== null) {
                    RazaoItem::deleteAll(['cnt_razao_id' => $model->id]);
                } else {
                    $model = new Razao();
                    $model->cnt_documento_id = Documento::PAGAMENTO;
                    $model->documento_origem_id = $pagamento->id;
                }

                $count = $count + 1;


                $model->documento_origem_numero = $pagamento->numero . '/' . $pagamento->bas_ano_id;
                $model->status = 1;
                $model->valor_credito = $pagamento->valor;
                $model->valor_debito = $pagamento->valor;
                $model->cnt_diario_id = $diario->id;
                $model->documento_origem_data = $pagamento->data;
                $model->data = date('Y-m-d');
                $model->descricao = $pagamento->descricao;

                if (!$model->save()) {
                    $errors = [
                        'errors' => $model->errors,
                        'pagamento' => $pagamento,
                        'razao' => $model,
                    ];
                    print_r($errors);
                    die();
                    $transaction->rollBack();
                }

                $fluxos = [];
                // despesa de cliente não comtabilistico  com processo     
                $query0 = CntQuery::listPagamentoDespesasClienteItemsProcesso($pagamento->id);
                foreach ($query0 as $key => $flx) {
                    if (array_key_exists($flx['cnt_plano_fluxo_caixa_id'], $fluxos)) {
                        $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] =   $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] + $flx['valor'];
                    } else {
                        $fluxos[$flx['cnt_plano_fluxo_caixa_id']] = [
                            'cnt_plano_fluxo_caixa_id' => $flx['cnt_plano_fluxo_caixa_id'],
                            'total' => $flx['valor'],
                        ];
                    }
                }

                // despesa de cliente não comtabilistico  sem processo  
                $query1 = CntQuery::listPagamentoDespesasClienteItems($pagamento->id);
                foreach ($query1 as $key => $flx) {
                    if (array_key_exists($flx['cnt_plano_fluxo_caixa_id'], $fluxos)) {
                        $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] =   $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] + $flx['valor'];
                    } else {
                        $fluxos[$flx['cnt_plano_fluxo_caixa_id']] = [
                            'cnt_plano_fluxo_caixa_id' => $flx['cnt_plano_fluxo_caixa_id'],
                            'total' => $flx['valor'],
                        ];
                    }
                }
                // print_r($query1);die();

                // despesa de agencia contabilistico
                $query2 = CntQuery::listPagamentoDespesasAgenciaItems($pagamento->id);
                foreach ($query2 as $key => $flx) {
                    if (array_key_exists($flx['cnt_plano_fluxo_caixa_id'], $fluxos)) {
                        $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] =   $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] + $flx['valor'];
                    } else {
                        $fluxos[$flx['cnt_plano_fluxo_caixa_id']] = [
                            'cnt_plano_fluxo_caixa_id' => $flx['cnt_plano_fluxo_caixa_id'],
                            'total' => $flx['valor'],
                        ];
                    }
                }

                // despesa de agencia não contabilistico
                $query3 = CntQuery::listPagamentoDespesas($pagamento->id);
                foreach ($query3 as $key => $flx) {
                    // $fluxos[$flx['cnt_plano_fluxo_caixa_id']]=$flx['cnt_plano_fluxo_caixa_id'];
                    if (array_key_exists($flx['cnt_plano_fluxo_caixa_id'], $fluxos)) {
                        $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] =   $fluxos[$flx['cnt_plano_fluxo_caixa_id']]['total'] + $flx['valor'];
                    } else {
                        $fluxos[$flx['cnt_plano_fluxo_caixa_id']] = [
                            'cnt_plano_fluxo_caixa_id' => $flx['cnt_plano_fluxo_caixa_id'],
                            'total' => $flx['valor'],
                        ];
                    }
                }

                $fluxo_id = null;
                foreach ($fluxos as $key => $fluxo) {
                    // $fluxo_id = $fluxo['cnt_plano_fluxo_caixa_id'];
                    $modelItemA = new RazaoItem();
                    $modelItemA->cnt_razao_id = $model->id;
                    $modelItemA->descricao = $documento->codigo . ' - ' . $pagamento->numero . '/' . $pagamento->bas_ano_id;
                    $modelItemA->cnt_plano_conta_id = $planoContaBanco->id;
                    $modelItemA->cnt_natureza_id = $documento->cnt_natureza_id;
                    $modelItemA->valor = $fluxo['total'];
                    $modelItemA->cnt_plano_terceiro_id = null;
                    $modelItemA->cnt_plano_iva_id = null;
                    $modelItemA->cnt_plano_fluxo_caixa_id = $planoContaBanco->tem_plano_fluxo_caixa ? $fluxo['cnt_plano_fluxo_caixa_id'] : null;
                    $modelItemA->documento_origem_numero = $pagamento->numero . '/' . $pagamento->bas_ano_id;
                    $modelItemA->documento_origem_tipo = $documento->codigo;
                    if (!$modelItemA->save()) {
                        $errors = [
                            'errors' => $modelItemA->errors,
                            'fluxo' => $fluxo,
                            'pagamento' => $pagamento,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }
                }




                foreach ($query0 as $key => $item) {
                    // print_r($item);die();
                    $planoContaItem = PlanoConta::findOne($item['cnt_plano_conta_id']);
                    $modelItemB = new RazaoItem();
                    $modelItemB->cnt_razao_id = $model->id;
                    $modelItemB->cnt_natureza_id = Natureza::DEBITO;
                    $modelItemB->descricao = $documento->codigo . '-' . $item['descricao'];
                    $modelItemB->cnt_plano_conta_id = $item['cnt_plano_conta_id'];
                    $modelItemB->cnt_plano_terceiro_id = $planoContaItem->tem_plano_externo ? $item['cnt_plano_terceiro_id'] : null;
                    $modelItemB->cnt_plano_iva_id = $item['cnt_plano_iva_id'] ? $item['cnt_plano_iva_id'] : null;
                    $modelItemB->cnt_plano_fluxo_caixa_id = null;
                    $modelItemB->valor = $item['valor'];
                    $modelItemB->documento_origem_numero = (string)$item['numero'];
                    $modelItemB->documento_origem_tipo = $item['documento_origem_tipo'];
                    if (!$modelItemB->save()) {
                        $errors = [
                            'lancamento' => 'Pagamento ' . $pagamento->numero . '/' . $pagamento->bas_ano_id,
                            'errors' => $modelItemB->errors,
                            'item' => $item,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }
                }



                foreach ($query1 as $key => $item) {
                    $planoContaItemC = PlanoConta::findOne($item['cnt_plano_conta_id']);
                    $modelItemC = new RazaoItem();
                    $modelItemC->cnt_razao_id = $model->id;
                    $modelItemC->cnt_natureza_id = Natureza::DEBITO;
                    $modelItemC->descricao = $documento->codigo . '-' . $item['descricao'];
                    $modelItemC->cnt_plano_conta_id = $item['cnt_plano_conta_id'];
                    $modelItemC->cnt_plano_terceiro_id = $planoContaItemC->tem_plano_externo ? $item['cnt_plano_terceiro_id'] : null;
                    $modelItemC->cnt_plano_iva_id = $item['cnt_plano_iva_id'] ? $item['cnt_plano_iva_id'] : null;
                    $modelItemC->cnt_plano_fluxo_caixa_id = null;
                    $modelItemC->valor = $item['valor'];
                    $modelItemC->documento_origem_numero = (string)$item['numero'];
                    $modelItemC->documento_origem_tipo = $item['documento_origem_tipo'];
                    if (!$modelItemC->save()) {
                        $errors = [
                            'lancamento' => 'Pagamento ' . $pagamento->numero . '/' . $pagamento->bas_ano_id,
                            'errors' => $modelItemC->errors,
                            'item' => $item,
                        ];
                        print_r($errors);
                        die();
                        $transaction->rollBack();
                    }
                }

                // print_r($query);die();
                foreach ($query2 as $key => $item) {
                    $planoContaItemD = PlanoConta::findOne($item['cnt_plano_conta_id']);
                    $modelItemD = new RazaoItem();
                    $modelItemD->cnt_razao_id = $model->id;
                    $modelItemD->cnt_natureza_id = Natureza::DEBITO;
                    $modelItemD->descricao = $documento->codigo . '-' . $item['descricao'];
                    $modelItemD->cnt_plano_conta_id = $item['cnt_plano_conta_id'];
                    $modelItemD->cnt_plano_terceiro_id = $planoContaItemD->tem_plano_externo ? $item['cnt_plano_terceiro_id'] : null;
                    $modelItemD->cnt_plano_iva_id = $item['cnt_plano_iva_id'] ? $item['cnt_plano_iva_id'] : null;
                    $modelItemD->cnt_plano_fluxo_caixa_id = null;
                    $modelItemD->valor = ($item['valor_iva'] > 0) ? ($item['valor'] - $item['valor_iva']) : $item['valor'];
                    $modelItemD->documento_origem_numero = (string)$item['numero'];
                    $modelItemD->documento_origem_tipo = $item['documento_origem_tipo'];
                    $modelItemD->dsp_item_id = $item['id'];
                    if (!$modelItemD->save()) {
                        $error = [
                            'errors' => $modelItemD->errors,
                            'item' => $item,
                            'pagamento' => $pagamento,
                        ];
                        print_r($error);
                        die();
                        $transaction->rollBack();
                    }



                    $planoIvaItem = PlanoIva::findOne($item['cnt_plano_iva_id']);
                    if (!empty($planoIvaItem)) {

                        $planoContaItem = PlanoConta::findOne($planoIvaItem->cnt_plano_conta_id);
                        $modelItemE = new RazaoItem();
                        $modelItemE->cnt_razao_id = $model->id;
                        $modelItemE->cnt_natureza_id = Natureza::DEBITO;
                        $modelItemE->descricao = $documento->codigo . '-' . $item['descricao'];
                        $modelItemE->cnt_plano_conta_id = $planoContaItem->id;
                        $modelItemE->cnt_plano_terceiro_id = $planoContaItem->tem_plano_externo ? $item['cnt_plano_terceiro_id'] : null;
                        $modelItemE->cnt_plano_iva_id = $item['cnt_plano_iva_id'] ? $item['cnt_plano_iva_id'] : null;
                        $modelItemE->cnt_plano_fluxo_caixa_id = null;
                        $modelItemE->valor = $item['valor_iva'];
                        $modelItemE->documento_origem_numero = (string)$item['numero'];
                        $modelItemE->dsp_item_id = $item['id'];
                        $modelItemE->documento_origem_tipo = $item['documento_origem_tipo'];
                        if (!$modelItemE->save()) {
                            $error = [
                                'errors' => $modelItemE->errors,
                                'item' => $item,
                                'pagamento' => $pagamento,
                            ];
                            print_r($error);
                            die();
                            $transaction->rollBack();
                        }
                    }
                }




                // qando o despeza é contabilistico
                foreach ($query3 as $key => $item) {
                    $documentoItem = Documento::findOne($item['cnt_documento_id']);
                    $planoContaItem = PlanoConta::findOne($documentoItem->cnt_plano_conta_id);
                    $modelItemF = new RazaoItem();
                    $modelItemF->cnt_razao_id = $model->id;
                    $modelItemF->cnt_natureza_id = Natureza::DEBITO;
                    $modelItemF->descricao = $documento->codigo . '-' . $item['descricao'];
                    $modelItemF->cnt_plano_conta_id = $planoContaItem->id;
                    $modelItemF->cnt_plano_terceiro_id = $planoContaItem->tem_plano_externo ? $item['cnt_plano_terceiro_id'] : null;
                    $modelItemF->cnt_plano_iva_id = null;
                    $modelItemF->cnt_plano_fluxo_caixa_id = null;
                    $modelItemF->valor = $item['valor'];
                    $modelItemF->documento_origem_numero = (string)$item['numero'];
                    $modelItemF->dsp_item_id = $item['id'];
                    $modelItemF->documento_origem_tipo = $item['documento_origem_tipo'];
                    if (!$modelItemF->save()) {
                        $error = [
                            'errors' => $modelItemF->errors,
                            'item' => $item,
                            'pagamento' => $pagamento,
                        ];
                        print_r($error);
                        die();
                        $transaction->rollBack();
                    }
                }


                $transaction->commit();
            }
        }
        return $count;
        // } catch (\Exception $e) {
        //     print_r($e->getMessage());die();
        //     Yii::warning($e->getMessage());
        //     $transaction->rollBack();
        //     throw $e;
        // } 

    }
}
