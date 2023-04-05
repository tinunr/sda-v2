<?php
namespace app\modules\cnt\widget;

use yii;
use yii\base\Widget;
use yii\db\Query;
use yii\helpers\Html;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\cnt\models\RazaoItemSearch;

class ExtratoFluxoCaixaWidget extends Widget
{
    public $mes;
    public $ano;
    public $cnt_plano_fluxo_caixa_id;
    public $data = [];
    public $PlanoFluxoCaixas = [];
    public $total_debito = 0;
    public $total_credito = 0;
    public $total_saldo = 0;

    public function init(){
        parent::init();
        if($this->cnt_plano_fluxo_caixa_id===null){
            $this->PlanoFluxoCaixas  = PlanoFluxoCaixa::find()->where(['or',['cnt_plano_fluxo_caixa_id'=>0],['cnt_plano_fluxo_caixa_id'=>null]])->AsArray()->all();
        }else{
            $this->PlanoFluxoCaixas = PlanoFluxoCaixa::find()->where(['id'=>$this->cnt_plano_fluxo_caixa_id])->AsArray()->all();
        }
    }

    public function run()
    {   
         $html  = Html::beginTag('div',['class' => "row"]);
         $html .= Html::tag('p', 'MES:'.$this->mes.' - '.$this->ano, ['class' =>"text-center"]);
         $html .= Html::tag('p', 'Moeda: Nacional', ['class' =>"text-center"]);
         $html .= Html::beginTag('table', ['class' => "table table-striped"]);
         $html .= Html::beginTag('thead');
         $html .= Html::beginTag('tr');
         $html .= Html::tag('th', 'Mês');
         $html .= Html::tag('th', 'Dia');
         $html .= Html::tag('th', 'Diário');
         $html .= Html::tag('th', 'Docum.');
         $html .= Html::tag('th', 'Tereiro');
         $html .= Html::tag('th', 'Descritivo');
         $html .= Html::tag('th', 'Debito');
         $html .= Html::tag('th', 'Credito');
         $html .= Html::tag('th', 'Saldo');
         $html .= Html::endTag('tr');
         $html .= Html::endTag('thead');
         $html .= Html::beginTag('tbody');
        
         
        $arrayFilhos = static::array_fold(static::getFilhosArray($this->cnt_plano_fluxo_caixa_id));
        $max = sizeof($arrayFilhos);
        for($i = 0; $i < $max; $i++){
            // $html .=static::getPaisTable($arrayFilhos[$i]);
             $this->data['cnt_plano_fluxo_caixa_id'] = $arrayFilhos[$i];
             $html .= static::getFilhosTable($this->data);
        }

         $html .= Html::endTag('tbody');
         $html .= Html::endTag('table');
         $html .= Html::endTag('div');


        return $html;
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getPaisArray($id) 
    {    
        $array= [];
        $plano = PlanoFluxoCaixa::findOne(['id'=>$id]);
        $array[$plano->id]= [
            'id'=>$plano->id,
        ];
        // print_r($array);die();
        if ($plano->cnt_plano_fluxo_caixa_id >0) {
          $array[$plano->cnt_plano_fluxo_caixa_id]= [
                'id'=>$plano->cnt_plano_fluxo_caixa_id,
                'pai'=>static::getPaisArray($plano->cnt_plano_fluxo_caixa_id),
           ];
        }else{}
        // print_r($array);die();
        
        // ksort($array);
        return $array;
    }


    public static function getPaisTable2($cnt_plano_fluxo_caixa_id)
    {
        $plano = PlanoFluxoCaixa::findOne($cnt_plano_fluxo_caixa_id);
        $html ='<tr>';
        $html .='<th colspan="2">'.$plano->id.'</th>';
        $html .='<th colspan="7">'.$plano->descricao.'</th>';
        $html .='</tr>';
        return $html;
    }

    public static function getPaisTable($cnt_plano_fluxo_caixa_id ,$nivel =0)
    {
        $plano = PlanoFluxoCaixa::findOne($cnt_plano_fluxo_caixa_id);
        $html = '';
        $html2 = '';if ($nivel==1) {
                $html ='<tr>';
                $html .='<th colspan="2">'.$plano->id.'</th>';
                $html .='<th colspan="7">'.$plano->descricao.'</th>';
                $html .='</tr>';
        }
        if (!empty($plano->cnt_plano_fluxo_caixa_id)&&$plano->cnt_plano_fluxo_caixa_id>0) {
            $html2 =static::getPaisTable($plano->cnt_plano_fluxo_caixa_id, 1);
        }
        $html2 .= $html;


        return $html2;
    }


    public static function getFilhosTable($data)
    {
        $html='';
        $total_credito = 0;
        $total_debito = 0;
        $total_saldo =0;
        $formatter = Yii::$app->formatter;
        $query = Yii::$app->CntQuery->queryExtratoFluxoCaixa($data);
        if ($query->count()>0) {
                $html .= static::getPaisTable($data['cnt_plano_fluxo_caixa_id']);
// print_r($data);die();
            if (!empty($data['cnt_plano_fluxo_caixa_id'])) {
    # code...

                $html .='<tr>';
                $html .='<th colspan="2">'.$data['cnt_plano_fluxo_caixa_id'].'</th>';
                $html .='<th colspan="7">'.PlanoFluxoCaixa::findOne($data['cnt_plano_fluxo_caixa_id'])->descricao.'</th>';
                $html .='</tr>';
            }
        }
        foreach ($query->all()  as $key => $value){
            $total_credito = $total_credito + $value['credito'];
            $total_debito = $total_debito + $value['debito'];
            $total_saldo = $total_saldo + ($value['debito']-$value['credito']);
         $html .= Html::beginTag('tr');
         $html .= Html::tag('td', $formatter->asDate(strtotime($value['data']),'MM-Y'));
         $html .= Html::tag('td', $formatter->asDate($value['data'],'dd'));
         $html .= Html::tag('td', str_pad($value['cnt_diario_id'],2,'0',STR_PAD_LEFT));
         $html .= Html::tag('td', str_pad($value['num_doc'],6,'0',STR_PAD_LEFT));
         $html .= Html::tag('td', $value['terceiro']?str_pad($value['terceiro'],6,'0',STR_PAD_LEFT):null);
         $html .= Html::tag('td', $value['descricao']);
         $html .= Html::tag('td', !$value['debito']?null:$formatter->asCurrency($value['debito']),['class'=>'text-right']);
         $html .= Html::tag('td', !$value['credito']?null:$formatter->asCurrency($value['credito']),['class'=>'text-right']);
         $html .= Html::tag('td',  $formatter->asCurrency($total_saldo),['class'=>"text-right"]);
         $html .= Html::endTag('tr');
     }

        if ($query->count()>0) {
             $html .= Html::beginTag('tr');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('th','Totais Acumulado');
             $html .= Html::tag('th', $formatter->asCurrency($total_debito),['class'=>"text-right"]);
             $html .= Html::tag('th', $formatter->asCurrency($total_credito),['class'=>"text-right"]);
             $html .= Html::tag('th', $formatter->asCurrency($total_saldo),['class'=>"text-right"]);
             $html .= Html::endTag('tr');

             $html .= Html::beginTag('tr');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('th','Saldo');
             $html .= Html::tag('th', $total_saldo<0?$formatter->asCurrency(abs($total_saldo)):null,['class'=>"text-right"]);
             $html .= Html::tag('td', $total_saldo>0?$formatter->asCurrency(abs($total_saldo)):null,['class'=>"text-right"]);
             $html .= Html::tag('td',$total_saldo>0?'DB':'CR' ,['class'=>"text-right"]);
             $html .= Html::endTag('tr');

              $html .= Html::beginTag('tr');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('td');
             $html .= Html::tag('th','Total Controlo');
             $html .= Html::tag('th', $total_saldo<0?$formatter->asCurrency($total_debito+abs($total_saldo)):$formatter->asCurrency($total_debito),['class'=>"text-right"]);
             $html .= Html::tag('th', $total_saldo>0?$formatter->asCurrency($total_credito+abs($total_saldo)):$formatter->asCurrency($total_credito),['class'=>"text-right"]);
             $html .= Html::tag('td' );
             $html .= Html::endTag('tr');
        }
        return $html;
    }

    public static function getFilhosArray($id) 
    {   $array = [];
        $model = PlanoFluxoCaixa::find()->where(['cnt_plano_fluxo_caixa_id'=>$id])->all();
        $array[$id]=[
                'id'=>$id,
            ];
        foreach ($model as $data) {
            $array[$data->id]=[
                'id'=>$data->id,
                'filo'=>static::getFilhosArray($data->id)
            ];
        }
        return $array;
    }


    /**
     * Fold all levels of a multidimensional array down
     * to a single level.
     * @param array $array
     * @param bool $withKeys
     * @return array
     */
     public static function array_fold(array $array, bool $withKeys = false) 
    {
        $accumulator = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $accumulator = array_merge(
                    $accumulator,
                    static::array_fold($value, $withKeys)
                );
                continue;
            }
            if ($withKeys) {
                $accumulator[$key] = $value;
                continue;
            }
            $accumulator[] = $value;
        }
        return array_unique($accumulator);
    }



    


}
?>