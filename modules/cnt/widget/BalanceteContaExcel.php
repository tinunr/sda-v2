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

class BalanceteContaExcel extends Widget
{
    public $ano;
    public $data = [];

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
        $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $dataBalancete = Yii::$app->CntQuery->getBalanceteContaAll($this->data['bas_ano_id'], $this->data['bas_mes_id'],$this->data['cnt_plano_conta_id']);

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

            $sheet->setTitle('Balancete '.$this->ano.' - '.$this->data['bas_mes_id']);
            $sheet->setCellValue('B1', 'Agência de Despacho Aduaneiro Morais & Cruz, Lda');            
            $sheet->setCellValue('B2', 'Balancete de Verificação do Razão Geral');
            $sheet->setCellValue('B3', 'Ano:'.$this->ano.' Mês: '.$this->data['bas_mes_id']);

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
            $sheet->getStyle('A5:H5')->applyFromArray($styleArray);


            $sheet = $spreadsheet->getActiveSheet()
                    ->fromArray(
                        $dataBalancete,  // The data to set
                        NULL,        // Array values with this value will not be set
                        'A6'         // Top left coordinate of the worksheet range where
                                    //    we want to set these values (default is A1)
                    );
            $i=count($dataBalancete) +6;

            foreach ($dataBalancete as $key => $value) {
                if(strlen($value['id'])==2){
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
                $sheet->setCellValue('C'.$i, number_format($this->debito, 2));
                $sheet->setCellValue('D'.$i, number_format($this->credito, 2));
                $sheet->setCellValue('E'.$i, number_format($this->debito_acumulado, 2));
                $sheet->setCellValue('F'.$i, number_format($this->credito_acumulado, 2));
                $sheet->setCellValue('G'.$i, number_format($this->saldo_debito, 2));
                $sheet->setCellValue('H'.$i, number_format($this->saldo_credito, 2));

                $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($styleArray);

            $writer = new Xlsx($spreadsheet);
            $writer->save('Spreadsheet/Balancete.xlsx');



        return true;
    }



  

   



  
    


}
?>