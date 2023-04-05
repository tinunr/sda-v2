<?php
namespace app\modules\cnt\widget;

use yii;
use yii\base\Widget;
use yii\db\Query;
use yii\helpers\Html;
use app\modules\cnt\models\RazaoItem;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\RazaoItemSearch;
use app\modules\dsp\models\Person;

class BalancetePersonWidget extends Widget
{
    public $bas_mes_id;
    public $bas_ano_id;
    public $planoConta;
    public $bas_ano;
    public $cnt_plano_conta_id;
    public $cnt_plano_terceiro_id;
    public $path;

    public function init(){
        parent::init();
         if(empty($this->cnt_plano_conta_id)){
            $this->planoConta = PlanoConta::find()
                             ->where(['tem_plano_externo'=>1])
                             ->orderBy('path')                             
                             ->asArray()
                             ->all();
        }else{
            $plano_conta = planoConta::find()->where(['id'=>$this->cnt_plano_conta_id])->asArray()->one();
            $this->planoConta = PlanoConta::find()
                              ->where(['LIKE','path',$plano_conta['path'].'%', false])
                              ->andWhere(['tem_plano_externo'=>1])
                              ->orderBy('path')
                              ->asArray()
                              ->all();
        }



    }

    public function run()
    {   
         $formatter = Yii::$app->formatter;

        // print_r($this->cnt_plano_conta_id);die();

         $html  = Html::beginTag('div',['class' => "row"]);
         $html .= Html::beginTag('table', ['class' => "table table-striped"]);
         $html .= Html::beginTag('thead');
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

         foreach ($this->planoConta as $key => $planoConta) {  
                 $html .= Html::beginTag('tr');
                 $html .= Html::tag('th',$planoConta['id']);
                 $html .= Html::tag('th',$planoConta['descricao'],['colspan'=>'7']);
                 $html .= Html::endTag('tr');  
                $this->cnt_plano_conta_id = $planoConta['id'];
                $data = $this->getData(); 
                $total_debito=0;
                $total_credito=0;
                $total_debito_acumulado=0;
                $total_credito_acumulado=0;
                $total_saldo_debito=0;
                $total_saldo_credito=0;           
                foreach ($data as $key => $value) {

                    $total_debito = $total_debito + $value['debito'];
                    $total_credito = $total_credito + $value['credito'];
                    $total_debito_acumulado = $total_debito_acumulado + $value['debito_acumulado'];
                    $total_credito_acumulado = $total_credito_acumulado + $value['credito_acumulado'];
                    $total_saldo_debito = $total_saldo_debito + $value['saldo_d'];
                    $total_saldo_credito= $total_saldo_credito + $value['saldo_c'];

                     $html .= Html::beginTag('tr');
                     $html .= Html::tag('td', str_pad($value['cnt_plano_terceiro_id'],6,'0',STR_PAD_LEFT));
                     $html .= Html::tag('td', $value['person']);
                     $html .= Html::tag('td', $formatter->asCurrency($value['debito']),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($value['credito']),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($value['debito_acumulado']),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($value['credito_acumulado']),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($value['saldo_d']),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($value['saldo_c']),['class'=>'text-right']);
                     $html .= Html::endTag('tr');
                }

                     $html .= Html::beginTag('tr');
                     $html .= Html::tag('td', 'Total',['colspan'=>'2']);
                     $html .= Html::tag('td', $formatter->asCurrency($total_debito),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($total_credito),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($total_debito_acumulado),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($total_credito_acumulado),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($total_saldo_debito),['class'=>'text-right']);
                     $html .= Html::tag('td', $formatter->asCurrency($total_saldo_credito),['class'=>'text-right']);
                     $html .= Html::endTag('tr');

         }
        
         $html .= Html::endTag('tbody');
         $html .= Html::endTag('table');
         $html .= Html::endTag('div');

        
        return $html;
    }





    public  function getData()
    {
        $data_one = (new \yii\db\Query())
                    ->select(['A.cnt_plano_conta_id'
                            ,'A.cnt_plano_terceiro_id'
                            ,'person'=>'D.nome'
                            ,'C.path'
                            ,'bas_mes_id'=>'YEAR(B.documento_origem_data)'
                            ,'bas_mes_id'=>'MONTH(B.documento_origem_data)'
                            ,'debito'=>"(CASE WHEN A.cnt_natureza_id = 'D' THEN  A.valor ELSE 0.00  END )"
                            ,'credito'=>"(CASE WHEN A.cnt_natureza_id = 'C' THEN  A.valor ELSE 0.00  END )"])
                    ->from(['C'=>'cnt_plano_conta'])
                    ->leftJoin(['A'=>'cnt_razao_item'],'C.id = A.cnt_plano_conta_id')
                    ->leftJoin(['B'=>'cnt_razao'],'A.cnt_razao_id=B.id')
                    ->leftJoin(['D'=>'dsp_person'],'D.id = A.cnt_plano_terceiro_id')
                     ->leftJoin(['E'=>'bas_ano'],'B.bas_ano_id=E.id')
                    ->where(['B.status'=>1])
                    ->andWhere(['C.tem_plano_externo'=>1])
                    ->andWhere(['A.cnt_plano_conta_id'=>$this->cnt_plano_conta_id])
                    ->andWhere(['E.ano'=>$this->bas_ano])
                    ->andWhere(['<=','B.bas_mes_id',$this->bas_mes_id])
                    ->filterWhere(['A.cnt_plano_terceiro_id'=>$this->cnt_plano_terceiro_id]);
      
          $data_two = (new \yii\db\Query())
                ->select([
                    'x.cnt_plano_conta_id'
                    ,'x.cnt_plano_terceiro_id'
                    ,'x.person'
                    ,'x.path'
                    ,'debito'=>"sum(CASE WHEN x.bas_mes_id = '$this->bas_mes_id' THEN x.debito    ELSE 0.00  END)"
                    ,'credito'=>"sum( CASE WHEN x.bas_mes_id = '$this->bas_mes_id' THEN x.credito    ELSE 0.00  END)"
                    ,'debito_acumulado'=>'sum(x.debito)'
                    ,'credito_acumulado'=>'sum(x.credito)'
                    ,'saldo_d'=>'(CASE WHEN (sum(x.debito)-sum(x.credito))>=0 THEN (sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
                    ,'saldo_c'=>'(CASE WHEN (sum(x.debito)-sum(x.credito))<0 THEN -1*(sum(x.debito)-sum(x.credito))  ELSE 0.00  END)'
                ])
                ->from(['x'=>$data_one])
                ->groupBy(['x.cnt_plano_conta_id', 'x.cnt_plano_terceiro_id'])
                ->orderBy('x.person')
                // ->filterWhere(['LIKE','x.path',$this->path.'%', FALSE])
                ->all();

              return $data_two; 

    }


}
?>
