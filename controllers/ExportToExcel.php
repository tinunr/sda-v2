<?php
namespace app\modules\cnt\components;

use yii;
use yii\base\Widget;
use yii\db\Query;
use yii\helpers\Html;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\RazaoItemSearch;

class ExportToExcel extends Widget
{
    public $mes;
    public $ano;
    public $cnt_plano_conta_id;
    public $data = [];
    public $planoContas = [];
    public $total_debito = 0;
    public $total_credito = 0;
    public $total_saldo = 0;

    public function init(){
        parent::init();
        if($this->cnt_plano_conta_id===null){
            $this->planoContas  = PlanoConta::find()->where(['or',['cnt_plano_conta_id'=>0],['cnt_plano_conta_id'=>null]])->AsArray()->all();
        }else{
            $this->planoContas = PlanoConta::find()->where(['id'=>$this->cnt_plano_conta_id])->AsArray()->all();
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
        
         
        $arrayFilhos = static::array_fold(static::getFilhosArray($this->cnt_plano_conta_id));
        $max = sizeof($arrayFilhos);
        for($i = 0; $i < $max; $i++){
             $this->data['cnt_plano_conta_id'] = $arrayFilhos[$i];
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
        $plano = PlanoConta::findOne(['id'=>$id]);
        $array[$plano->id]= [
            'id'=>$plano->id,
        ];
        // print_r($array);die();
        if ($plano->cnt_plano_conta_id >0) {
          $array[$plano->cnt_plano_conta_id]= [
                'id'=>$plano->cnt_plano_conta_id,
                'pai'=>static::getPaisArray($plano->cnt_plano_conta_id),
           ];
        }else{}
        // print_r($array);die();
        
        // ksort($array);
        return $array;
    }


    public static function getPaisTable($cnt_plano_conta_id)
    {
        $plano = PlanoConta::findOne($cnt_plano_conta_id);
        $html ='<tr>';
        $html .='<th colspan="2">'.$plano->id.'</th>';
        $html .='<th colspan="7">'.$plano->descricao.'</th>';
        $html .='</tr>';
        return $html;
    }


    public static function getFilhosTable($data)
    {
        $html='';
        $total_credito = 0;
        $total_debito = 0;
        $total_saldo =0;
        $formatter = Yii::$app->formatter;
        $query = new Query();
        $query->select(['A.id','A.cnt_plano_conta_id','descricao'=>'A.descricao','B.cnt_diario_id','debito'=>new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='D' THEN A.valor ELSE 0 END"),'credito'=>new \yii\db\Expression("CASE WHEN  cnt_natureza_id ='C' THEN A.valor ELSE 0 END"),'cnt_documento_id','data'=>'B.documento_origem_data','B.cnt_diario_id','num_doc'=>'B.documento_origem_numero','terceiro'=>'A.cnt_plano_terceiro_id'])
        ->from('cnt_razao_item A')
        ->leftJoin('cnt_razao B','B.id = A.cnt_razao_id')
        ->where(['B.status'=>1])
        ->orderBy('B.documento_origem_data');
        $query->andFilterWhere([
            'YEAR(B.documento_origem_data)' => $data['bas_ano_id'],
            'MONTH(B.documento_origem_data)' => $data['begin_mes'],
            'A.cnt_plano_conta_id' => $data['cnt_plano_conta_id'],
            'A.cnt_plano_terceiro_id' => $data['cnt_plano_terceiro_id'],
        ]);
        // if ($query->count()>0) {
           // $arrayPais = static::getPaisArray($data['cnt_plano_conta_id']);
           // foreach ( $arrayPais as $key => $cnt_plano_terceiro_id) {
                $html .= static::getPaisTable($data['cnt_plano_conta_id']);
            // }
        // }
        foreach ($query->all()  as $key => $value){
            $total_credito = $total_credito + $value['credito'];
            $total_debito = $total_debito + $value['debito'];
            $total_saldo = $total_saldo + ($value['debito']-$value['credito']);
         $html .= Html::beginTag('tr');
         $html .= Html::tag('td', $formatter->asDate($value['data'],'MM-Y'));
         $html .= Html::tag('td', $formatter->asDate($value['data'],'dd'));
         $html .= Html::tag('td', str_pad($value['cnt_diario_id'],2,'0',STR_PAD_LEFT));
         $html .= Html::tag('td', str_pad($value['num_doc'],6,'0',STR_PAD_LEFT));
         $html .= Html::tag('td', $value['terceiro']?str_pad($value['terceiro'],6,'0',STR_PAD_LEFT):null);
         $html .= Html::tag('td', $value['descricao']);
         $html .= Html::tag('td', !$value['debito']?null:$formatter->asCurrency($value['debito']),['class'=>'float-right']);
         $html .= Html::tag('td', !$value['credito']?null:$formatter->asCurrency($value['credito']),['class'=>'float-right']);
         $html .= Html::tag('td',  $formatter->asCurrency($total_saldo),['class'=>"float-right"]);
         $html .= Html::endTag('tr');
     }

        return $html;
    }

    public static function getFilhosArray($id) 
    {   $array = [];
        $model = PlanoConta::find()->where(['cnt_plano_conta_id'=>$id])->all();
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