<?php

namespace app\modules\dsp\services;

use yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;
use app\models\Ano;
use app\modules\dsp\models\Nord;
use app\modules\dsp\models\Desembaraco;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\RegimeItem;
use app\modules\dsp\models\RegimeItemItem;

class NordService extends Component
{

    const UM_MILHAO = 1000000;
    const UM_PORCENTO_DE_UM_MILHAO = 10000;
    const MERCADORIA_NAO_ESPECIFICADA_4000 = 50;

    const UNIDADE_TONELADA = 1;
    const UNIDADE_HECTOLITRO = 2;
    const UNIDADE_UNIDADE = 3;
    const UNIDADE_AD_VALOREM = 4;

    /**
     * @var integer
     */
    public static function nordDataByXml($nord)
    {
        if (!empty($nord)) {
            $model = Nord::findOne($nord);
            if (!empty($model)) {
                if (file_exists(Yii::getAlias('@nords/') . Ano::findOne($model->bas_ano_id)->ano . '/' . Desembaraco::findOne($model->dsp_desembaraco_id)->code . '/' . $model->id . '.xml')) {
                    return simplexml_load_file(Yii::getAlias('@nords/') . Ano::findOne($model->bas_ano_id)->ano . '/' . Desembaraco::findOne($model->dsp_desembaraco_id)->code . '/' . $model->id . '.xml');
                } elseif (file_exists(Yii::getAlias('@nords/') . $nord . '.xml')) {
                    return simplexml_load_file(Yii::getAlias('@nords/') . $nord . '.xml');
                }
            } elseif (file_exists(Yii::getAlias('@nords/') . $model->id . '.xml')) {
                return simplexml_load_file(Yii::getAlias('@nords/') . $nord . '.xml');
            }
        }
        return false;
    }

    public static function unidade($nord)
    {
        $unidade = 0;
        $nordData = self::nordDataByXml($nord);
        foreach ($nordData->Item as $item) {
            $unidade = $unidade + $item->Packages->Number_of_packages;
        }
        return $unidade;
    }

    /**
     * @var integer
     */
    public static function tonelada($nord)
    {
        return (ceil(self::kiloGrama($nord) / 1000));
    }

    /**
     * @var integer
     */
    public static function hetlitro($nord)
    {
        return (ceil(self::litro($nord) / 100));
    }



    public static function valorAduaneiro($nord)
    {
        $valor = 0;
        $nordData = self::nordDataByXml($nord);
        if (!empty($nordData->Item)) {
            foreach ($nordData->Item as $item) {
                $valor = $valor + $item->Valuation_item->Statistical_value;
            }
        }
        //print_r($valor);die();
        return $valor;
    }

    public static function kiloGrama($nord)
    {
        $kiloGrama = 0;
        $nordData = self::nordDataByXml($nord);
        foreach ($nordData->Item as $item) {
            $kiloGrama = $kiloGrama + $item->Valuation_item->Weight_itm->Gross_weight_itm;
        }
        return $kiloGrama;
    }

    public static function litro($nord)
    {
        $litro = 0;
        $nordData = self::nordDataByXml($nord);
        foreach ($nordData->Item as $item) {
            $litro = $litro + $item->Tarification->Supplementary_unit->Suppplementary_unit_quantity;
        }
        return $litro;
    }

    public function regimeItem($id)
    {
        return RegimeItem::findOne(['id' => $id]);
    }

    public function regimeItemItem($id)
    {
        return RegimeItemItem::findOne(['id' => $id]);
    }

    public function subRegime($nord)
    {
        $nordData = self::nordDataByXml($nord);
        if (!empty($nordData->Item)) {
            foreach ($nordData->Item as $item) {
                $sub_regime_id = (int) $item->Tarification->National_customs_procedure;
            };
        }
    }

    public static function totalNumberOfItems($nord)
    {
        $nordData = self::nordDataByXml($nord);
        if (!empty($nordData->Property->Nbers)) {
            foreach ($nordData->Property->Nbers as $key => $Number_of_loading_lists) {
                $Total_number_of_items = $Number_of_loading_lists->Total_number_of_items;
            }
            return $Total_number_of_items;
        }
        return 0;
    }

    public function honorarioValorBaseTonelada($id, $nord)
    {
        # Tonelada
        $regimeItem = self::regimeItem($id);
        $tonelada = self::tonelada($nord);
        $honorario = ($tonelada * $regimeItem->valor);
        $valorBase = $tonelada . ' Tonelada';

        return [
            'honorario' => $honorario,
            'valorBase' => $valorBase,
        ];
    }

    public function regimeItemItemS($id)
    {
        return RegimeItemItem::find()
            ->where(['dsp_regime_item_id' => $id])
            ->orderBy('id')
            ->all();
    }


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function getHonorarioValueRegimeTabelaAnexa($id, $nord)
    {
        $nordData = self::nordDataByXml($nord);
        $regimeItem = self::regimeItem($id);

        $valoBase = round($regimeItem->valor * 100) . ' % da tabela Anexa';

        return [
            'table' => $valoBase,
        ];
    }





    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function getHonorarioValueRegimeItem($id, $nord)
    {
        $nordData = self::nordDataByXml($nord);
        $honorario = 0;
        $regimeItem = self::regimeItem($id);

        // print_r($id);die();

        if ($regimeItem->dsp_item_unidade == self::UNIDADE_TONELADA) {
            # TONELADA
            $tonelada = self::tonelada($nord);
            $honorario = ($tonelada * $regimeItem->valor);
            $valoBase = $tonelada . ' Tonelada';
        } elseif ($regimeItem->dsp_item_unidade == self::UNIDADE_HECTOLITRO) {
            # Hetlitro
            $Hetlitro = self::hetlitro($nord);
            $honorario = ($Hetlitro * $regimeItem->valor);
            $valoBase = $Hetlitro . ' Hetlitro';
        } elseif ($regimeItem->dsp_item_unidade == self::UNIDADE_UNIDADE) {
            # unidade                
            $unidade = self::unidade($nord);
            $honorario = $unidade * $regimeItem->valor;
            $valoBase = $unidade . ' Unidade';
        } elseif ($regimeItem->dsp_item_unidade == self::UNIDADE_AD_VALOREM) {
            # Ad-valorem
            $valorAduaneiro = self::valorAduaneiro($nord);
            $honorario = ($valorAduaneiro * $regimeItem->valor);
            $valoBase = $valorAduaneiro . ' Ad-valorem';
        }
        return [
            'honorario' => $honorario,
            'table' => $valoBase,
        ];
    }





    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function getHonorarioValueRegimeItemItem($id, $nord)
    {
        $nordData = self::nordDataByXml($nord);
        $regimeItemItemS = self::regimeItemItemS($id);

        $honorario = 0;
        $valorAduaneiro = self::valorAduaneiro($nord);
        #print_r($valorAduaneiro);die();                   
        $valoBase = '';
        if (!empty($regimeItemItemS)) {
            foreach ($regimeItemItemS as $key => $regimeItemItem) {

                $dsp_regime_item_valor = $regimeItemItem->forma;
                if ($regimeItemItem->dsp_item_unidade == self::UNIDADE_TONELADA  && $valorAduaneiro <= $regimeItemItem->valor_produto) {

                    $tonelada = self::tonelada($nord);
                    $honorario = ($tonelada * $regimeItemItem->valor);
                    $valoBase = $tonelada . ' Tonelada';

                    break;
                } elseif ($regimeItemItem->dsp_item_unidade == self::UNIDADE_HECTOLITRO && $valorAduaneiro <= $regimeItemItem->valor_produto) {
                    # Hetlitro
                    $Hetlitro = self::hetlitro($nord);
                    $honorario = ($Hetlitro * $regimeItemItem->valor);
                    $valoBase = $Hetlitro . ' Hetlitro';
                    break;
                } elseif ($regimeItemItem->dsp_item_unidade == self::UNIDADE_UNIDADE && $valorAduaneiro <= $regimeItemItem->valor_produto) {
                    # unidade
                    $unidade = self::unidade($nord);
                    $honorario = ($unidade * $regimeItemItem->valor);
                    $valoBase = $unidade . ' Unidade';
                    break;
                } elseif ($regimeItemItem->dsp_item_unidade == self::UNIDADE_AD_VALOREM && $valorAduaneiro <= $regimeItemItem->valor_produto && $regimeItemItem->condicao == '<=') {
                    # Ad-valorem
                    if ($valorAduaneiro > self::UM_MILHAO && $regimeItemItem->id == self::MERCADORIA_NAO_ESPECIFICADA_4000) {
                        $honorario = self::UM_PORCENTO_DE_UM_MILHAO + (($valorAduaneiro - self::UM_MILHAO) * $regimeItemItem->valor);
                    } else {
                        $honorario = ($valorAduaneiro * $regimeItemItem->valor);
                    }
                    $valoBase = $valorAduaneiro . ' Ad-valorem';
                    break;
                } elseif ($regimeItemItem->dsp_item_unidade == self::UNIDADE_AD_VALOREM && $valorAduaneiro > $regimeItemItem->valor_produto && $regimeItemItem->condicao == '>') {
                    # Ad-valorem                                
                    if ($valorAduaneiro > self::UM_MILHAO && $regimeItemItem->dsp_regime_item_id == self::MERCADORIA_NAO_ESPECIFICADA_4000) {
                        $honorario = self::UM_PORCENTO_DE_UM_MILHAO + (($valorAduaneiro - self::UM_MILHAO) * $regimeItemItem->valor);
                        #print_r($regimeItemItem->valor);die();

                    } else {
                        $honorario = ($valorAduaneiro * $regimeItemItem->valor);
                    }
                    $valoBase = $valorAduaneiro . ' Ad-valorem';

                    break;
                }
            }
        }
        return [
            'honorario' => $honorario,
            'table' => $valoBase,
        ];
    }

    /**
     * Cria ou atualiza histórico de estado oeracionais.
     * @return mixed
     */
    public function getProtocoloProcessoData($dsp_nord_id)
    {
        $dv = 0;
        $exame_previo_comercial = 0;
        $requerimento_espeical = 0;
        $requerimento_normal = 0;

        if (file_exists(Yii::getAlias('@app/web/nords/') . $dsp_nord_id . '.xml')) {
            $data = simplexml_load_file(Yii::getAlias('@app/web/nords/') . $dsp_nord_id . '.xml');

            $requerimento_espeical = $data->Property->Forms->Number_of_the_form;
            $requerimento_normal = $data->Property->Forms->Total_number_of_forms - $data->Property->Forms->Number_of_the_form;
        }



        return [
            'dv' => $dv,
            'exame_previo_comercial' => $exame_previo_comercial,
            'requerimento_espeical' => $requerimento_espeical,
            'requerimento_normal' => $requerimento_normal,
        ];
    }
}
