<?php
namespace app\modules\cnt\widget;

use yii;
use yii\base\Widget;
use yii\db\Query;
use yii\helpers\Html;
use app\modules\cnt\models\PlanoFluxoCaixa;
use app\modules\cnt\models\PlanoTerceiro;
use app\models\Mes;

class BalanceteFluxoCaixa extends Widget
{
    
    public $bas_mes_id;
    public $bas_mes_descricao;
    public $bas_ano;
    public $bas_ano_id;
    public $cnt_plano_fluxo_caixa_id;
    public $cnt_plano_terceiro_id;
    public $terceiro;
    public $path;

    public $total_debito_acumulado = 0;
    public $total_credito_cumulado = 0;
    public $total_debito_atual =0;
    public $total_credito_atual = 0;
    public $total_saldo_d = 0;
    public $total_saldo_c = 0;

    public function init(){
        parent::init();
        $this->bas_mes_descricao = Mes::findOne($this->bas_mes_id)->descricao;
        if (!empty($this->cnt_plano_fluxo_caixa_id)) {
            $this->path = PlanoFluxoCaixa::findOne($this->cnt_plano_fluxo_caixa_id)->path;
        }
        if (!empty($this->cnt_plano_terceiro_id)) {
            $this->terceiro = PlanoTerceiro::findOne($this->cnt_plano_terceiro_id)->nome;
        } else{
            unset($this->terceiro);
        }
    }

    public function run()
    {   
         $formatter = Yii::$app->formatter;
         $html  = Html::beginTag('div',['class' => "row"]);

         $html .= Html::beginTag('table', ['class' => "table table-striped"]);
         $html .= Html::beginTag('thead');
         if (!empty($this->cnt_plano_terceiro_id)) {
            $html .= Html::beginTag('tr');
            $html .= Html::tag('th', 'TERCEIRO');
            $html .= Html::tag('th', $this->cnt_plano_terceiro_id.' - '.$this->terceiro,['colspan'=>7]);
            $html .= Html::endTag('tr');
         }
         $html .= Html::beginTag('tr');
         $html .= Html::tag('th', 'Conta');
         $html .= Html::tag('th', ' ');
         $html .= Html::tag('th', 'Acumulado do Mês',['colspan'=>2,'class' =>'text-center']);
         $html .= Html::tag('th', 'Acumulado até o Mês',['colspan'=>2,'class' =>'text-center']);
         $html .= Html::tag('th', 'Saldos',['colspan'=>2,'class' =>'text-center']);
         $html .= Html::endTag('tr');

         $html .= Html::beginTag('tr');
         $html .= Html::tag('th', 'Cod.');
         $html .= Html::tag('th', 'Descrição');
         $html .= Html::tag('th', 'Debitos',['class' =>'text-center']);
         $html .= Html::tag('th', 'Credito',['class' =>'text-center']);
         $html .= Html::tag('th', 'Debitos',['class' =>'text-center']);
         $html .= Html::tag('th', 'Credito',['class' =>'text-center']);
         $html .= Html::tag('th', 'Devedores',['class' =>'text-center']);
         $html .= Html::tag('th', 'Credores',['class' =>'text-center']);
         $html .= Html::endTag('tr');
         $html .= Html::endTag('thead');
         $html .= Html::beginTag('tbody');
         
             $html .= $this->getDataTable();
        

         $html .= Html::beginTag('tr');
         $html .= Html::tag('th','Total',['colspan'=>'2']);
         $html .= Html::tag('th', $formatter->asCurrency($this->total_debito_atual),['class'=>'text-right']);
         $html .= Html::tag('th', $formatter->asCurrency($this->total_credito_atual),['class'=>'text-right']);
         $html .= Html::tag('th', $formatter->asCurrency($this->total_debito_acumulado),['class'=>'text-right']);
         $html .= Html::tag('th', $formatter->asCurrency($this->total_credito_cumulado),['class'=>'text-right']);
         $html .= Html::tag('th', $formatter->asCurrency($this->total_saldo_d),['class'=>'text-right']);
         $html .= Html::tag('th', $formatter->asCurrency($this->total_saldo_c),['class'=>'text-right']);
         $html .= Html::endTag('tr');
         $html .= Html::endTag('tbody');
         $html .= Html::endTag('table');
         $html .= Html::endTag('div');


        return $html;
    }


    public  function getDataTable()
    {   
        // print_r($this);die();
        $formatter = Yii::$app->formatter;
        // $data = $this->bas_ano_id;
        // $ano = \app\models\Ano::findOne($bas_ano_id)->ano;
        $html = '';
        
        $data = $this->getData();
        // $data = Yii::$app->CntQuery->getBalancete($ano,$bas_mes_id);
        // print_r($data);die();

        foreach($data as $value){
            if ($value['debito_acumulado']>0||$value['credito_acumulado']>0) {
                if(strlen($value['id'])==1){ 
                    $this->total_debito_atual = $this->total_debito_atual + $value['debito'];
                    $this->total_credito_atual = $this->total_credito_atual + $value['credito'];
                    $this->total_debito_acumulado = $this->total_debito_acumulado + $value['debito_acumulado'];
                    $this->total_credito_cumulado = $this->total_credito_cumulado + $value['credito_acumulado'];
                    $this->total_saldo_d = $this->total_saldo_d + $value['saldo_debito'];
                    $this->total_saldo_c = $this->total_saldo_c + $value['saldo_credito'];
                }
        
             $html .= Html::beginTag('tr');
             $html .= Html::tag('td', $value['id']);
             $html .= Html::tag('td', $value['descricao']);
             $html .= Html::tag('td', $formatter->asCurrency($value['debito']),['class'=>'text-right']);
             $html .= Html::tag('td', $formatter->asCurrency($value['credito']),['class'=>'text-right']);
             $html .= Html::tag('td', $formatter->asCurrency($value['debito_acumulado']),['class'=>'text-right']);
             $html .= Html::tag('td', $formatter->asCurrency($value['credito_acumulado']),['class'=>'text-right']);
             $html .= Html::tag('td', $formatter->asCurrency($value['saldo_debito']),['class'=>'text-right']);
             $html .= Html::tag('td', $formatter->asCurrency($value['saldo_credito']),['class'=>'text-right']);
             $html .= Html::endTag('tr');
            }
        }
        return $html;
    }






    public  function getData()
    {
        $raz_detalhado = (new \yii\db\Query())
                    ->select(['A.cnt_plano_fluxo_caixa_id'
                            ,'A.cnt_plano_terceiro_id'
                            ,'C.path'
                            ,'bas_mes_id'=>'YEAR(B.documento_origem_data)'
                            ,'bas_mes_id'=>'MONTH(B.documento_origem_data)'
                            ,'debito'=>"(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )"
                            ,'credito'=>"(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"])
                    ->from(['C'=>'cnt_plano_fluxo_caixa'])
                    ->leftJoin(['A'=>'cnt_razao_item'],'C.id = A.cnt_plano_fluxo_caixa_id')
                    ->leftJoin(['B'=>'cnt_razao'],'A.cnt_razao_id=B.id')
                    ->leftJoin(['D'=>'bas_ano'],'B.bas_ano_id=D.id')
                    ->where(['B.status'=>1])
                    ->andWhere(['D.ano'=>$this->bas_ano])
                    ->andWhere(['<=','B.bas_mes_id',$this->bas_mes_id])
                    ->filterWhere(['A.cnt_plano_terceiro_id'=>$this->cnt_plano_terceiro_id]);
      
          $razao_agrupado = (new \yii\db\Query())
                ->select([
                    'x.cnt_plano_fluxo_caixa_id'
                    ,'x.cnt_plano_terceiro_id'
                    ,'x.path'
                    ,'debito'=>"sum(CASE WHEN x.bas_mes_id = '$this->bas_mes_id' THEN x.debito    ELSE 0.00  END)"
                    ,'credito'=>"sum( CASE WHEN x.bas_mes_id = '$this->bas_mes_id' THEN x.credito    ELSE 0.00  END)"
                    ,'debito_acumulado'=>'sum(x.debito)'
                    ,'credito_acumulado'=>'sum(x.credito)'
                    ,'saldo_debito'=>'(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
                    ,'saldo_credito'=>'(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
                ])
                ->from(['x'=>$raz_detalhado])
                ->groupBy(['x.cnt_plano_fluxo_caixa_id', 'x.cnt_plano_terceiro_id']);
      
              $balancete = (new \yii\db\Query())
                    ->select([
                        'P.id'
                        ,'P.descricao'
                        ,'debito'=>'sum(xx.debito)'
                        ,'credito'=>'sum(xx.credito)'
                        ,'debito_acumulado'=>'sum(xx.debito_acumulado)'
                        ,'credito_acumulado'=>'sum(xx.credito_acumulado)'
                        ,'saldo_debito'=>'sum(xx.saldo_debito)'
                        ,'saldo_credito'=>'sum(xx.saldo_credito)'
                    ])
                    ->from(['xx'=>$razao_agrupado])
                    ->leftJoin(['P'=>'cnt_plano_fluxo_caixa'],['LIKE','xx.path',new \yii\db\Expression("CONCAT(P.path,'%')"), FALSE])
                    ->groupBy(['P.id' , 'P.descricao'])
                    ->filterWhere(['LIKE','P.path', $this->path.'%', FALSE])
                    ->orderBy('P.path')
                    ->all();

              return $balancete; 

    }


}
?>