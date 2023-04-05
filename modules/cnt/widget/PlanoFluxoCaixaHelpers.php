<?php 
namespace app\modules\cnt\widget;

use Yii;
use app\assets\AppAsset;
use app\modules\cnt\models\RazaoItemSearch;
use app\modules\cnt\models\PlanoFluxoCaixa;

class PlanoFluxoCaixaHelpers
{
	public static $total_credito ;
	public $total_debito ;
	public $total_saldo ;



    public static function getFilhos($cnt_plano_cluxo_caixa_id)
    {
        $result = static::geteTableData($cnt_plano_cluxo_caixa_id);
        return $result;
    }

   



	private static function geteTableData($cnt_plano_fluxo_caixa_id) 
    {	
    	$data = static::getFilhosRecrusive($cnt_plano_fluxo_caixa_id);
    	$html ='';
		foreach ($data as $key => $value) {
				$html .= static::getItems($value);
		}
    	return $html;
	}


     private static function getFilhosRecrusive($parent)
    {

        $items = PlanoFluxoCaixa::find()
            ->where(['cnt_plano_fluxo_caixa_id' => $parent])
            ->orderBy('codigo')
            ->asArray()
            ->all();

        $result = []; 

        foreach ($items as $item) {
            
            $result[] = [
                    'id' => $item['id'],
                    'pai_id' => $item['cnt_plano_fluxo_caixa_id'],
                    'descricao' => $item['descricao'],
                    'items' => static::getFilhosRecrusive($item['id']),
                ];
        }


        return $result;
    }


	public static function getItems(array $value)
    {	
    	$total_saldo = 0;
    	$total_debito = 0;
    	$total_credito = 0;
    	$formatter = Yii::$app->formatter;
        $html2 ='';
        $html ='<tr>';
		$html .='<th colspan="2">'.$value['id'].'</th>';
		$html .='<th colspan="7">'.$value['descricao'].'</th>';
    	$html .= '</tr>';
		if (!empty($value['items'])) {
			$html2 .=static::geteTableData($value['id']);
		}
		$html .= $html2;

		$data['RazaoItemSearch']['cnt_plano_fluxo_caixa_id']=$value['id'];
		$searchModel = new RazaoItemSearch($data['RazaoItemSearch']);
		// print_r($searchModel);die();
        $dataProvider = $searchModel->extratoFluxoCaixa(Yii::$app->request->queryParams);
		foreach ($dataProvider->getModels()  as $key => $value){
			$total_credito = $total_credito + $value['credito'];
			$total_debito = $total_debito + $value['debito'];
			$total_saldo = $total_saldo + ($value['debito']-$value['credito']);

		$html .='<tr>';
		$html .='<td> '.$formatter->asDate(strtotime($value['data']),'MM-Y').'</td>';
		$html .='<td> '.$formatter->asDate($value['data'],'dd').'</td>';
		$html .='<td> '.str_pad($value['cnt_diario_id'],2,'0',STR_PAD_LEFT).'</td>';
		$html .='<td> '.$value['num_doc'].'</td>';
		$html .='<td> '.str_pad($value['terceiro'],6,'0',STR_PAD_LEFT).'</td>';
		$html .='<td> '.$value['descricao'].'</td>';
		$html .='<td> '.$formatter->asCurrency($value['debito']).'</td>';
		$html .='<td> '.$formatter->asCurrency($value['credito']).'</td>';
		$html .='<td> '.$formatter->asCurrency($total_saldo).'</td>';
		$html .='</tr>';
		}
			

        return $html;
    }




    public static function getPais($cnt_plano_cluxo_caixa_id ,$nivel =0)
    {
        $plano = PlanoFluxoCaixa::findOne($cnt_plano_cluxo_caixa_id);
        $html = '';
        $html2 = '';if ($nivel==1) {
		        $html ='<tr>';
				$html .='<th colspan="2">'.$plano->id.'</th>';
				$html .='<th colspan="7">'.$plano->descricao.'</th>';
		    	$html .='</tr>';
		}
    	if (!empty($plano->cnt_plano_fluxo_caixa_id)&&$plano->cnt_plano_fluxo_caixa_id>0) {
    		$html2 =static::getPais($plano->cnt_plano_fluxo_caixa_id, 1);
    	}
    	$html2 .= $html;


        return $html2;
    }

}