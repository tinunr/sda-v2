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

class ProcessoRegistrado extends Widget
{
    
    public $user_id;
    public $status=[];
    public $dsp_person_id;
    public $bas_ano_id;
    public $dataInicio; 
    public $dataFim;
    public $comPl;
    public $dsp_desembaraco_id;
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
         $html .= Html::tag('th', 'Cliente',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Mercadoria',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Nº Registo',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Data Registo',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Dias Registado',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Valor a Pagar',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Posição Atual',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('th', 'Estncia Aduaneira',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::endTag('tr');

         $html .= Html::endTag('thead');
         $html .= Html::beginTag('tbody');
        foreach($this->data as $processo){
         $html .= Html::beginTag('tr',['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $processo->numero.'/'.$processo->bas_ano_id,['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $processo->nord->id,['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $processo->person->nome,['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $processo->descricao,['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $processo->nord->despacho->s_registo.' - '.$processo->nord->despacho->id,['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $formatter->asDate($processo->nord->despacho->data_registo),['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $formatter->asRelativeTime($processo->nord->despacho->data_registo),['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $formatter->asCurrency($processo->nord->despacho->valor),['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $processo->nord->despacho->tramitacao,['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::tag('td', $processo->nord->desembaraco->code,['style'=>'border-bottom: 1px solid #ddd;']);
         $html .= Html::endTag('tr');
        }
         
         $html .= Html::endTag('tbody');
         $html .= Html::endTag('table');
         $html .= Html::endTag('div');

        return $html;
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
            ->andFilterWhere(['C.dsp_desembaraco_id'=>$this->dsp_desembaraco_id])
            ->andFilterWhere(['<=','A.data',$this->dataFim]);
            if( $this->comPl==0){
                $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl]);
            }
            if( $this->comPl==1){
                $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl]);
            }
            if( $this->comPl==2){
                $query->andFilterWhere(['>', 'A.n_levantamento', $this->comPl]);
            }
           return $query->all();
    }



    public function getData()
    {
        $query = Processo::find()
        ->joinWith('nord')
        ->andFilterWhere(['dsp_processo.user_id'=>$this->user_id])
        ->andFilterWhere(['dsp_processo.dsp_person_id'=>$this->dsp_person_id])
        ->andFilterWhere(['dsp_processo.status'=>$this->status])
        ->andFilterWhere(['dsp_processo.bas_ano_id'=>$this->bas_ano_id])
        ->andFilterWhere(['>=','dsp_processo.data',$this->dataInicio])
        ->andFilterWhere(['<=','dsp_processo.data',$this->dataFim])
        ->andFilterWhere(['dsp_nord.dsp_desembaraco_id'=>$this->dsp_desembaraco_id]);

        if( $this->comPl==0){
             $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl]);
         }
         if($this->comPl==1){
             $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl])
                 ->andFilterWhere(['dsp_processo.status'=>[1,2,3,4,5,7]]);
         }
         if($this->comPl==2){
             $query->andFilterWhere(['>', 'dsp_processo.n_levantamento', $this->comPl])
                 ->andFilterWhere(['dsp_processo.status'=>[6,8,9]]);
         }
         $query->orderBy('dsp_processo.status');
         return $query->all();


    }


}
?>