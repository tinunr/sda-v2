<?php

namespace app\modules\fin\services;

use app\modules\fin\models\FaturaDebitoCliente;
use app\modules\fin\models\FaturaDebitoClienteItem;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\FaturaEletronica;
use app\modules\fin\models\FaturaEletronicaItem;
use yii\web\NotFoundHttpException;

class FaturaService
{

    /**
     * Lists all FaturaProvisoria models.
     * @return mixed
     */
    public static function createFatura($id)
    { 
        $model = self::findFaturaDefinitivaModel($id);

        if (($fatura = FaturaEletronica::find()->where(['fin_fatura_definitiva_id' => $id])->one()) === null) { 
            $fatura = new FaturaEletronica();
            }
            $fatura->send = FaturaDefinitiva::ENVIADO;
            $fatura->data = $model->data; //date('Y-m-d');
            $fatura->fin_fatura_definitiva_id = $id;
            $fatura->dsp_processo_id = $model->dsp_processo_id;
            $fatura->bas_ano_id = $model->bas_ano_id;
            $fatura->dsp_person_id = $model->dsp_person_id;
            $fatura->n_registo = $model->n_registo;
            $fatura->n_receita = $model->n_receita;
            $fatura->acrescimo = $model->acrescimo;
            $fatura->descricao = $model->descricao;
            $fatura->regime = $model->regime;
            $fatura->nord = $model->nord;
            $fatura->formaula = $model->formaula;
            $fatura->posicao_tabela = $model->posicao_tabela;
            $fatura->dsp_regime_item_tabela_anexa_valor = $model->dsp_regime_item_tabela_anexa_valor;
            $fatura->dsp_regime_item_valor = $model->dsp_regime_item_valor;
            $fatura->data_registo = $model->data_registo;
            $fatura->data_receita = $model->data_receita;
            $fatura->impresso_principal = $model->impresso_principal;
            $fatura->pl = $model->pl;
            $fatura->gti = $model->gti;
            $fatura->tce = $model->tce;
            $fatura->form = $model->form;
            $fatura->regime_normal = $model->regime_normal;
            $fatura->regime_especial = $model->regime_especial;
            $fatura->exprevio_comercial = $model->exprevio_comercial;
            $fatura->expedente_matricula = $model->expedente_matricula; 
            $fatura->dv = $model->dv;
            $fatura->fotocopias = $model->fotocopias;
            $fatura->qt_estampilhas = $model->qt_estampilhas;
			$fatura->taxa_comunicaco = $model->taxa_comunicaco;
			$fatura->deslocacao_transporte = $model->deslocacao_transporte;
			$fatura->impresso = $model->impresso;
            $fatura->valor = $model->valorFaturaEletonica();
            if ($model->valorFaturaEletonica() > 0) {
                if ($fatura->save()) {
            FaturaEletronicaItem::deleteAll(['fin_fatura_eletronica_id'=>$fatura->id]);

                    foreach ($model->itemsFaturaEletronica() as $key => $value) {
                        $faturaEletronicaItem = new FaturaEletronicaItem();
                        $faturaEletronicaItem->fin_fatura_eletronica_id = $fatura->id;
                        $faturaEletronicaItem->dsp_item_id = $value['id'];
                        $faturaEletronicaItem->valor = $value['valor']; 
                        if (!$faturaEletronicaItem->save()) {
                            print_r($faturaEletronicaItem->errors);
                            die();
                        }
                    }
                } else {
                    print_r($fatura->errors);
                    die();
                }
            }


       
    }


    /**
     * Lists all FaturaProvisoria models.
     * @return mixed
     */
    public static function createDebitoCliente($id)
    {
        $model = self::findFaturaDefinitivaModel($id);

        if (($faturaDC = FaturaDebitoCliente::find()->where(['fin_fatura_definitiva_id' => $id])->one()) === null) {
            $faturaDC = new FaturaDebitoCliente();
        }
            $faturaDC->send = FaturaDefinitiva::ENVIADO;
            $faturaDC->data = $model->data;
            $faturaDC->fin_fatura_definitiva_id = $id;
            $faturaDC->dsp_processo_id = $model->dsp_processo_id;
            $faturaDC->bas_ano_id = $model->bas_ano_id;
            $faturaDC->dsp_person_id = $model->dsp_person_id;
            $faturaDC->n_registo = $model->n_registo;
            $faturaDC->n_receita = $model->n_receita;
            $faturaDC->acrescimo = $model->acrescimo;
            $faturaDC->descricao = $model->descricao;
            $faturaDC->regime = $model->regime;
            $faturaDC->nord = $model->nord;
            $faturaDC->formaula = $model->formaula;
            $faturaDC->posicao_tabela = $model->posicao_tabela;
            $faturaDC->dsp_regime_item_tabela_anexa_valor = $model->dsp_regime_item_tabela_anexa_valor;
            $faturaDC->dsp_regime_item_valor = $model->dsp_regime_item_valor;
            $faturaDC->data_registo = $model->data_registo;
            $faturaDC->data_receita = $model->data_receita;
            $faturaDC->impresso_principal = $model->impresso_principal;
            $faturaDC->pl = $model->pl;
            $faturaDC->gti = $model->gti;
            $faturaDC->tce = $model->tce;
            $faturaDC->form = $model->form;
            $faturaDC->regime_normal = $model->regime_normal;
            $faturaDC->regime_especial = $model->regime_especial;
            $faturaDC->exprevio_comercial = $model->exprevio_comercial;
            $faturaDC->expedente_matricula = $model->expedente_matricula;
			$faturaDC->dv = $model->dv;
            $faturaDC->fotocopias = $model->fotocopias;
            $faturaDC->qt_estampilhas = $model->qt_estampilhas;
			$faturaDC->taxa_comunicaco = $model->taxa_comunicaco;
			$faturaDC->deslocacao_transporte = $model->deslocacao_transporte;
			$faturaDC->impresso = $model->impresso;
            $faturaDC->valor = $model->valorDebitoCliente();
            if ($model->valorDebitoCliente() > 0) { 
                if ($faturaDC->save()) {
                    FaturaDebitoClienteItem::deleteAll(['fin_fatura_debito_cliente_id'=> $faturaDC->id]);
                    foreach ($model->itemsDebitoCliente() as $key => $value) {
                        $FaturaDebitoClienteItem = new FaturaDebitoClienteItem();
                        $FaturaDebitoClienteItem->fin_fatura_debito_cliente_id = $faturaDC->id;
                        $FaturaDebitoClienteItem->dsp_item_id = $value['id'];
                        $FaturaDebitoClienteItem->valor = $value['valor'];
                        if (!$FaturaDebitoClienteItem->save()) {
                            print_r($FaturaDebitoClienteItem->errors);
                            die();
                        }
                    }
                } else {
                    print_r($faturaDC->errors);
                    die();
                }
            }

    }


     /**
     * Deletes an existing Pagamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public static function undoFatura($id)
     {
          if(($model = FaturaEletronica::find()->where(['id'=>$id])->one())!==null){
         if ($model->valor_pago >0) {
            Yii::$app->getSession()->setFlash('warning', 'Nota de debito recebido não pode ser anulado.');
         }else {
             $model->status = 0;
             if($model->save()){
               return true;
             }
         }
         }
        return false;
     }

     /**
     * Deletes an existing Pagamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public static function undoNotaDebito($id)
     {
         if(($model = FaturaDebitoCliente::find()->where(['id'=>$id])->one())!==null){
         if ($model->valor_pago >0) {
            Yii::$app->getSession()->setFlash('warning', 'Nota de debito recebido não pode ser anulado.');
         }else {
             $model->status = 0;
             if($model->save()){
               return true;
             }
         }
         }
        return false;
     }

      /**
     * Deletes an existing Pagamento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public static function updatAllFaturaNotaDebitoCliente()
     {
        $model = FaturaDefinitiva::find()->where(['send'=>2,'bas_ano_id'=>22])->all();
        foreach($model as $value){
            self::createFatura($value->id);
            self::createDebitoCliente($value->id);
        } 
        return true;
     }


    /**
     * Finds the FaturaDefinitiva model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $perfil_id
     * @return FaturaDefinitiva the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function findFaturaDefinitivaModel($id)
    {
        if (($model = FaturaDefinitiva::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
