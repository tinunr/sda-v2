<?php
namespace app\modules\dsp\widget;

use yii;
use yii\base\Widget;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\dsp\models\Processo;
use app\models\Mes;
use kartik\mpdf\Pdf;

class ProcessoLiquidado extends Widget
{
    
    public $user_id;
    public $status=[];
    public $dsp_person_id;
    public $bas_ano_id;
    public $dataInicio; 
    public $dataFim;
    public $comPl;
    public $dsp_desembaraco_id ;
    public $users= [];
    public $data= []; 

    public function init(){
        parent::init();
        // $this->users = $this->getResponsavel();
        $this->data = $this->getData();
    }

    public function run()
    {   
         $formatter = Yii::$app->formatter;
        

         $html  = Html::beginTag('div',['class' => 'row']);

         $html .= Html::beginTag('table', ['class' => 'table table-striped']);
         $html .= Html::beginTag('thead');
         
         $html .= Html::beginTag('tr');
         $html .= Html::tag('th', 'Nº Proc.',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Nord',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Estncia Aduaneira',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Cliente',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Mercadoria',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Nº liquidação',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Data Liquidação',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Dias Liquidado',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Valor SIDONIA',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Saldo INTERNO',['style'=>'border-bottom: 1px solid #ddd;']); 
         $html .= Html::tag('th', 'Valor INTERNO',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Valor Pago INTERNO',['style'=>'border-bottom: 1px solid #ddd;']); 
         $html .= Html::endTag('tr');

         $html .= Html::endTag('thead');
         $html .= Html::beginTag('tbody');
        foreach($this->data as $processo){

            if($processo['valor_total_despesa_saldo'] != $processo['valor']){
$temSaldo = true;
            }else{
                $temSaldo= false;
            }
         $html .= Html::beginTag('tr',['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $processo['numero'],['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $processo['nord'],['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $processo['code'],['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]); 
         $html .= Html::tag('td', $processo['person'],['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $processo['descricao'],['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $processo['liquidacao'],['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $formatter->asDate($processo['data_liquidacao']),['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]); 
         $html .= Html::tag('td', $formatter->asRelativeTime($processo['data_liquidacao']),['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]); 
         $html .= Html::tag('td', $formatter->asCurrency($processo['valor']),['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $formatter->asCurrency($processo['valor_total_despesa_saldo']),['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $formatter->asCurrency($processo['valor_total_despesa']),['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
         $html .= Html::tag('td', $formatter->asCurrency($processo['valor_total_despesa_pago']),['style'=>'border-bottom: 1px solid #ddd;'.($temSaldo?'color:red;':'')]);
          
         $html .= Html::endTag('tr');
        }
         
         $html .= Html::endTag('tbody');
         $html .= Html::endTag('table');
         $html .= Html::endTag('div');

        return $html;
    }

  


    public function getData()
    {
        // $query1 = Processo::find()
        // ->joinWith('nord')
        // ->andFilterWhere(['dsp_processo.user_id'=>$this->user_id])
        // ->andFilterWhere(['dsp_processo.dsp_person_id'=>$this->dsp_person_id])
        // ->andFilterWhere(['dsp_processo.status'=>$this->status])
        // ->andFilterWhere(['dsp_processo.bas_ano_id'=>$this->bas_ano_id])
        // ->andFilterWhere(['>=','dsp_processo.data',$this->dataInicio])
        // ->andFilterWhere(['<=','dsp_processo.data',$this->dataFim])
        // ->andFilterWhere(['dsp_nord.dsp_desembaraco_id'=>$this->dsp_desembaraco_id]);
 
         /**
         * Valor recebido via reembolço devolução de alfandiga
         **/    
        $query = (new \yii\db\Query())
            ->select([
                'A.id',
                'numero'=>"CONCAT(`A`.`numero`, '/', `A`.`bas_ano_id`)", 
                'nord'=>'B.id',
                'E.code',
                'person'=>'D.nome', 
                'A.descricao',  
                'liquidacao'=>"CONCAT(`C`.`s_liquidade`, '-', `C`.`numero_liquidade`)", 
                'C.data_liquidacao', 
                'C.valor', 
                'valor_total_despesa' => 'IFNULL(SUM(F.valor),0)',
                'valor_total_despesa_pago' => 'IFNULL(SUM(F.valor_pago),0)',
                'valor_total_despesa_saldo' => 'IFNULL(SUM(F.saldo),0)', 
            ])
            ->from('dsp_processo A') 
            ->leftJoin('dsp_nord B', 'B.dsp_processo_id = A.id')
            ->leftJoin('dsp_despacho C', 'C.dsp_desembaraco_id = B.dsp_desembaraco_id AND C.bas_ano_id = B.bas_ano_id AND C.nord=B.numero') 
            ->leftJoin('dsp_person D', 'D.id = A.nome_fatura') 
            ->leftJoin('dsp_desembaraco E', 'E.id = B.dsp_desembaraco_id') 
            ->leftJoin('fin_despesa F', 'F.dsp_processo_id = A.id AND F.dsp_person_id = 161 AND F.status = 1')
            ->groupBy('A.id');

        $query->andFilterWhere(['A.user_id'=>$this->user_id])
        ->andFilterWhere(['A.dsp_person_id'=>$this->dsp_person_id])
        ->andFilterWhere(['A.status'=>$this->status])
        ->andFilterWhere(['A.bas_ano_id'=>$this->bas_ano_id])
        ->andFilterWhere(['>=','A.data',$this->dataInicio])
        ->andFilterWhere(['<=','A.data',$this->dataFim])
        ->andFilterWhere(['B.dsp_desembaraco_id'=>$this->dsp_desembaraco_id]);

        if( $this->comPl==0){
             $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl]);
         }
         if($this->comPl==1){
             $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl])
                 ->andFilterWhere(['A.status'=>[1,2,3,4,5,7]]);
         }
         if($this->comPl==2){
             $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl])
                 ->andFilterWhere(['A.status'=>[6,8,9]]);
         }
             $query->orderBy(['A.id'=>SORT_DESC]);
        // ->orderBy('A.status');
         return $query->all();


    }
 public  function getResponsavel()
    {
        
        $query = new Query;
        $query->select(['B.id','B.name'])
            ->from('dsp_processo A')
            ->join('LEFT JOIN', 'user B', 'B.id = A.user_id')
            ->join('LEFT JOIN', 'dsp_nord C', 'C.dsp_processo_id = A.id')
            ->orderBy('B.name')
            ->groupBy(['B.id','B.name'])
            ->where(['A.bas_ano_id'=>$this->bas_ano_id])
            ->filterWhere(['A.user_id'=>$this->user_id])
            ->andFilterWhere(['A.status'=>$this->status])
            ->andFilterWhere(['A.dsp_person_id'=>$this->dsp_person_id])
            ->andFilterWhere(['>=','A.data',$this->dataInicio])
            ->andFilterWhere(['<=','A.data',$this->dataFim])
            ->andFilterWhere(['C.dsp_desembaraco_id'=>$this->dsp_desembaraco_id]);
        if( $this->comPl==0){
                $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl]);
            }
            if( $this->comPl==1){
                $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl])
                      ->andFilterWhere(['A.status'=>[1,2,3,4,5,7]]);
            }
            if( $this->comPl==2){
                $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl])
                      ->andFilterWhere(['A.status'=>[6,8,9]]);
            }
           return $query->all();
    }


}
?>