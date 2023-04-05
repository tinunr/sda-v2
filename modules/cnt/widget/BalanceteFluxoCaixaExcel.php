<?php
namespace app\modules\cnt\widget;

use yii;
use yii\base\Widget;
use yii\db\Query;
use yii\helpers\Html;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\RazaoItemSearch;
use app\modules\cnt\models\PlanoTerceiro;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BalanceteFluxoCaixaExcel extends Widget
{
    public $ano;
    public $data = [];
    public $bas_mes_id;
    public $bas_mes_descricao;
    public $bas_ano;
    public $bas_ano_id;
    public $cnt_plano_fluxo_caixa_id;
    public $cnt_plano_terceiro_id;
    public $terceiro;
    public $path;

    public  $debito=0;
    public  $credito=0;
    public  $debito_acumulado=0;
    public  $credito_acumulado=0;
    public  $saldo_debito=0;
    public  $saldo_credito=0;


    public function init(){
        parent::init();
    }

    public function run()
    {   
        $formatter = Yii::$app->formatter;
        $styleData = [
                'numberFormat' => [
                    'formatCode' =>'#,##0.00' 
                ]
            ];
            $styleHeader = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $dataBalancete = $this->getData();
            // $dataBalancete = Yii::$app->CntQuery->getBalanceteFluxoCaixaAll($this->ano, $this->data['bas_mes_id'],$this->data['cnt_plano_fluxo_caixa_id'],$this->data['cnt_plano_terceiro_id']);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(50);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);

            $sheet->setTitle('Balancete '.$this->bas_ano.'-'.$this->bas_mes_id);
            $sheet->setCellValue('B1', 'Agência de Despacho Aduaneiro Morais & Cruz, Lda');            
            $sheet->setCellValue('B2', 'Balancete de Verificação do Razão Geral');
            $sheet->setCellValue('B3', 'Ano:'.$this->bas_ano.' Mês: '.$this->bas_mes_id);

            $dateTimeNow = time();
            $sheet->setCellValue('G1', 'Data:');
            $sheet->setCellValue('H1', Date::PHPToExcel($dateTimeNow));
            $sheet->getStyle('I1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

            $sheet->getStyle('B1:B3')->getFill()->getStartColor('#3c5462')->setARGB('#F1F2F3');
            $sheet->getStyle('B1:B3')->getFont()->setBold(TRUE);

            
            $sheet->setCellValue('A5', 'Conta');
            $sheet->setCellValue('B5', 'Descrição');
            $sheet->setCellValue('C5', 'Debito');
            $sheet->setCellValue('D5', 'Credito');
            $sheet->setCellValue('E5', 'Debito');
            $sheet->setCellValue('F5', 'Credito');
            $sheet->setCellValue('G5', 'Devedores');
            $sheet->setCellValue('H5', 'Credores');

            $sheet->setAutoFilter('A5:H5');
            $sheet->getStyle('A5:H5')->applyFromArray($styleHeader)->getFont()->setBold(true);
            // $sheet->getStyle('A5:H5')->applyFromArray($styleHeader)->setBold(true);


            $sheet = $spreadsheet->getActiveSheet()
                    ->fromArray(
                        $dataBalancete,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A6'         // Top left coordinate of the worksheet range where
                                    //    we want to set these values (default is A1)
                    );
            $i=count($dataBalancete) +6;
           

            foreach ($dataBalancete as $key => $value) {
                if(strlen($value['id'])==1){
                    $this->debito = $this->debito + $value['debito'];
                    $this->credito = $this->credito + $value['debito'];
                    $this->debito_acumulado = $this->debito_acumulado + $value['debito_acumulado'];
                    $this->credito_acumulado = $this->credito_acumulado + $value['credito_acumulado'];
                    $this->saldo_debito = $this->saldo_debito + $value['saldo_debito'];
                    $this->saldo_credito = $this->saldo_credito + $value['saldo_credito'];
                }
            }

                $sheet->setCellValue('A'.$i, '');
                $sheet->setCellValue('B'.$i, 'TOTAL');
                $sheet->setCellValue('C'.$i, $this->debito);
                $sheet->setCellValue('D'.$i, $this->credito);
                $sheet->setCellValue('E'.$i, $this->debito_acumulado);
                $sheet->setCellValue('F'.$i, $this->credito_acumulado);
                $sheet->setCellValue('G'.$i, $this->saldo_debito);
                $sheet->setCellValue('H'.$i, $this->saldo_credito);

                $sheet->getStyle('C6:H'.$i)->applyFromArray($styleData);
                $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($styleHeader)->getFont()->setBold(true);

            $writer = new Xlsx($spreadsheet);
            $writer->save('Spreadsheet/BalanceteFluxoCaixaExcel.xlsx');



        return true;
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