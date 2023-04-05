<?php
namespace app\modules\dsp\components;


use yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yiidb\Query;
use yii\helpers\Json;
use yii\web\JsonParser;


use app\modules\dsp\models\Regime;
use app\modules\dsp\models\RegimeItem;
use app\modules\dsp\models\RegimeItemItem;

/**
* 
*/
class DspHonorario extends Component
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
    public function nordDataByXml($nord)
    {
        // print_r($nord);die();
        if(!empty($nord)){
          if (file_exists(Yii::getAlias('@nords/').$nord.'.xml')){
            return simplexml_load_file(Yii::getAlias('@nords/').$nord.'.xml');
            }
        }
        return false;
    }

    public function unidade($nord)
    {
        $unidade = 0;
        $nordData = $this->nordDataByXml($nord);                     
        foreach ($nordData->Item as $item) { 
            $unidade = $unidade + $item->Packages->Number_of_packages;
        }
        return $unidade;
    }

    /**
     * @var integer
     */
    public function tonelada($nord)
    {
        return (ceil($this->kiloGrama($nord)/1000));        
    }

    /**
     * @var integer
     */
    public function hetlitro($nord)
    {
        return (ceil($this->litro($nord)/100));        
    }

    

    public function valorAduaneiro($nord)
    {
        $valor = 0;
        $nordData = $this->nordDataByXml($nord);                     
        foreach ($nordData->Item as $item) { 
            $valor = $valor + $item->Valuation_item->Statistical_value;
        }
        //print_r($valor);die();
        return $valor;
    }

    public function kiloGrama($nord)
    {
        $kiloGrama =0;
        $nordData = $this->nordDataByXml($nord);                     
        foreach ($nordData->Item as $item) { 
            $kiloGrama = $kiloGrama + $item->Valuation_item->Weight_itm->Gross_weight_itm;
        }
        return $kiloGrama;
    }

    public function litro($nord)
    {
        $litro = 0;
        $nordData = $this->nordDataByXml($nord);                     
        foreach ($nordData->Item as $item) { 
            $litro = $litro + $item->Tarification->Supplementary_unit->Suppplementary_unit_quantity;
        }
        return $litro;

    }

    public function regimeItem($id)
    {
        return RegimeItem::findOne(['id'=>$id]);
    }

    public function regimeItemItem($id)
    {
        return RegimeItemItem::findOne(['id'=>$id]);
    }

    public function subRegime($nord)
    {
        $nordData = $this->nordDataByXml($nord); 
        if(!empty($nordData->Item))  {                  
                foreach ($nordData->Item as $item) { 
                    $sub_regime_id = (int)$item->Tarification->National_customs_procedure;
                };
            }
    }

    public function totalNumberOfItems($nord)
    {
        $nordData = $this->nordDataByXml($nord);  
        if($nordData){                   
        foreach ($nordData->Property->Nbers as $key => $Number_of_loading_lists) {
            $Total_number_of_items = $Number_of_loading_lists->Total_number_of_items;
        }
        return $Total_number_of_items;
        }
        return 0;
    }

    public function honorarioValorBaseTonelada($id , $nord)
   {
       # Tonelada
        $regimeItem = $this->regimeItem($id);
        $tonelada = $this->tonelada($nord);
        $honorario = ($tonelada * $regimeItem->valor);
        $valorBase = $tonelada.' Tonelada' ;

        return [
            'honorario'=>$honorario,
            'valorBase'=>$valorBase,
        ];
   }

    public function regimeItemItemS($id)
    {
        return RegimeItemItem::find()
                             ->where(['dsp_regime_item_id'=>$id])
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
   public function getHonorarioValueRegimeTabelaAnexa($id,$nord)
   {
        $nordData = $this->nordDataByXml($nord);
        $regimeItem = $this->regimeItem($id);

        $valoBase = round($regimeItem->valor*100).' % da tabela Anexa';               
            
       return [
            'table'=>$valoBase,
       ];
   }





   /**
    * Finds the User model based on its primary key value.
    * If the model is not found, a 404 HTTP exception will be thrown.
    * @param integer $id
    * @return User the loaded model
    * @throws NotFoundHttpException if the model cannot be found
    */
   public function getHonorarioValueRegimeItem($id,$nord)
   {
        $nordData = $this->nordDataByXml($nord);
        $honorario = 0; 
        $regimeItem = $this->regimeItem($id);

        // print_r($id);die();

            if ($regimeItem->dsp_item_unidade==self::UNIDADE_TONELADA) {
                # TONELADA
                $tonelada = $this->tonelada($nord);
                $honorario = ($tonelada * $regimeItem->valor);
                $valoBase = $tonelada.' Tonelada';

            }elseif ($regimeItem->dsp_item_unidade==self::UNIDADE_HECTOLITRO) {
                # Hetlitro
                $Hetlitro = $this->hetlitro($nord);
                $honorario = ($Hetlitro * $regimeItem->valor);
                $valoBase = $Hetlitro.' Hetlitro';

                
            }
            elseif ($regimeItem->dsp_item_unidade==self::UNIDADE_UNIDADE) {
                # unidade                
                $unidade = $this->unidade($nord);
                $honorario = $unidade * $regimeItem->valor;
                $valoBase = $unidade.' Unidade';
                
            }elseif ($regimeItem->dsp_item_unidade==self::UNIDADE_AD_VALOREM) {
               # Ad-valorem
               $valorAduaneiro = $this->valorAduaneiro($nord);
               $honorario = ($valorAduaneiro * $regimeItem->valor);
               $valoBase = $valorAduaneiro.' Ad-valorem';
                
            }
           return [
                 'honorario'=>$honorario,
                 'table'=>$valoBase,
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
        $nordData = $this->nordDataByXml($nord);
        $regimeItemItemS = $this->regimeItemItemS($id);

        $honorario = 0;
        $valorAduaneiro = $this->valorAduaneiro($nord);  
        #print_r($valorAduaneiro);die();                   
        $valoBase = '';
                if (!empty($regimeItemItemS)) {
                    foreach ($regimeItemItemS as $key => $regimeItemItem) {
                     
                        $dsp_regime_item_valor = $regimeItemItem->forma;
                        if ($regimeItemItem->dsp_item_unidade== self::UNIDADE_TONELADA  && $valorAduaneiro<=$regimeItemItem->valor_produto ) {
                                
                                $tonelada = $this->tonelada($nord);
                                $honorario = ($tonelada * $regimeItemItem->valor);
                                $valoBase = $tonelada.' Tonelada';

                                break;

                
                            }elseif ($regimeItemItem->dsp_item_unidade==self::UNIDADE_HECTOLITRO &&$valorAduaneiro<=$regimeItemItem->valor_produto) {
                                # Hetlitro
                                $Hetlitro = $this->hetlitro($nord);
                                $honorario = ($Hetlitro * $regimeItemItem->valor);
                                $valoBase = $Hetlitro.' Hetlitro';
                                break;


                                
                            }elseif ($regimeItemItem->dsp_item_unidade==self::UNIDADE_UNIDADE &&$valorAduaneiro<=$regimeItemItem->valor_produto) {
                                # unidade
                                $unidade = $this->unidade($nord);
                                $honorario = ($unidade * $regimeItemItem->valor);
                                $valoBase = $unidade.' Unidade';
                                break;

                                
                            }elseif ($regimeItemItem->dsp_item_unidade==self::UNIDADE_AD_VALOREM &&$valorAduaneiro<=$regimeItemItem->valor_produto&&$regimeItemItem->condicao=='<=') {
                                # Ad-valorem
                                if ($valorAduaneiro >self::UM_MILHAO && $regimeItemItem->id== self::MERCADORIA_NAO_ESPECIFICADA_4000) {
                                     $honorario = self::UM_PORCENTO_DE_UM_MILHAO+(($valorAduaneiro-self::UM_MILHAO) * $regimeItemItem->valor);
                                }else{
                                    $honorario = ($valorAduaneiro * $regimeItemItem->valor);
                                }
                                $valoBase = $valorAduaneiro.' Ad-valorem';
                                break;

                                
                        }elseif ($regimeItemItem->dsp_item_unidade==self::UNIDADE_AD_VALOREM &&$valorAduaneiro>$regimeItemItem->valor_produto&&$regimeItemItem->condicao=='>') {
                                # Ad-valorem                                
                                if ($valorAduaneiro >self::UM_MILHAO && $regimeItemItem->dsp_regime_item_id== self::MERCADORIA_NAO_ESPECIFICADA_4000) {
                                     $honorario = self::UM_PORCENTO_DE_UM_MILHAO+(($valorAduaneiro-self::UM_MILHAO) * $regimeItemItem->valor);
#print_r($regimeItemItem->valor);die();

                                }else{
                                    $honorario = ($valorAduaneiro * $regimeItemItem->valor);
                                }
                                $valoBase = $valorAduaneiro.' Ad-valorem';

                                break;
                                
                        }
                }
            }
        return [
            'honorario'=>$honorario,
            'table'=>$valoBase,
        ];
    }


     
}
?>



