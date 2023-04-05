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

class BalancetePersonExcel extends Widget
{
    public $ano;
    public $data = [];
    public $planoConta;
    public $i = 0;


    public function init(){
        parent::init();
        if(empty($this->data['cnt_plano_conta_id'])){
            $this->planoConta = PlanoConta::find()
                             ->where(['tem_plano_externo'=>1])
                             ->orderBy('path')                             
                             ->asArray()
                             ->all();
        }else{
            $plano_conta = planoConta::find()->where(['id'=>$this->data['cnt_plano_conta_id']])->asArray()->one();
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
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    // 'color' => ['argb' => 'FFFF0000'],
                ],
            ],
        ];
        

          $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet(); 

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(80);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);

         $sheet->setTitle('Extrato de Conta');
         $sheet->setCellValue('B1', 'Agência de Despacho Aduaneiro Morais & Cruz, Lda');            
         $sheet->setCellValue('B2', 'Balancete de Verificação do Razão Geral');
         $sheet->setCellValue('B3', 'Mês:'.$this->data['bas_mes_id'].' - '.$this->ano);
         $sheet->setCellValue('B4', 'Moeda: Nacional');
         $dateTimeNow = time();
         $sheet->setCellValue('G1', 'Data:');
         $sheet->setCellValue('H1', Date::PHPToExcel($dateTimeNow));
         $sheet->getStyle('H1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);

         
         $this->i = 6;

        //  $sheet->getStyle('B1:B3')->getFill()->getStartColor('#3c5462')->setARGB('#F1F2F3');
        //  $sheet->getStyle('B1:B3')->getFont()->setBold(TRUE);
        // print_r($this->data['cnt_plano_terceiro_id']);die();
        if (!empty($this->data['cnt_plano_terceiro_id'])) {
             $sheet->setCellValue('A'.$this->i, str_pad($this->terceiro['id'],2,'0',STR_PAD_LEFT));
             $sheet->setCellValue('B'.$this->i, $this->terceiro['nome']);
              $sheet->mergeCells('B'.$this->i.':I'.$this->i);
             $this->i++;
         }


        
        
         

         foreach ($this->planoConta as $key => $value) {

                $total_debito=0;
                $total_credito=0;
                $total_debito_acumulado=0;
                $total_credito_acumulado=0;
                $total_saldo_d=0;
                $total_saldo_c=0;  
                
            $this->data['cnt_plano_conta_id'] = $value['id'];
            $data = Yii::$app->CntQuery->BalancetePerson($this->data);

            if(count($data)>0){
            $paisData = $this->getPaisTable($value['id']);
            // print_r($paisData);die();
            foreach ($paisData as $key => $value) {
                $sheet->setCellValue('A'.$this->i, $value['id']);
                $sheet->setCellValue('B'.$this->i, $value['descricao']);
                $sheet->mergeCells('B'.$this->i.':H'.$this->i);
                $sheet->getStyle('A'.$this->i.':H'.$this->i)->getFont()->setBold(TRUE);
                $this->i++;
            }


            $sheet->setCellValue('A'.$this->i, 'Conta');
            $sheet->mergeCells('A'.$this->i.':B'.$this->i);
            $sheet->setCellValue('C'.$this->i, 'Acumulado do Mês');
            $sheet->mergeCells('C'.$this->i.':D'.$this->i);
            $sheet->setCellValue('E'.$this->i, 'Acumulado até o Mês');
            $sheet->mergeCells('E'.$this->i.':F'.$this->i);
            $sheet->setCellValue('G'.$this->i, 'Saldos');
            $sheet->mergeCells('G'.$this->i.':H'.$this->i);
            $sheet->getStyle('A'.$this->i.':H'.$this->i)->getFont()->setBold(TRUE);
            $sheet->getStyle('A'.$this->i.':H'.$this->i)->applyFromArray($styleArray);
            $this->i++;

            
            $sheet->setCellValue('A'.$this->i, 'Código');
            $sheet->setCellValue('B'.$this->i, 'Descrição');
            $sheet->setCellValue('C'.$this->i, 'Debito');
            $sheet->setCellValue('D'.$this->i, 'Credito.');
            $sheet->setCellValue('E'.$this->i, 'Debito');
            $sheet->setCellValue('F'.$this->i, 'Credito');
            $sheet->setCellValue('G'.$this->i, 'Devedores');
            $sheet->setCellValue('H'.$this->i, 'Credores');

            $sheet->getStyle('A'.$this->i.':H'.$this->i)->getFont()->setBold(TRUE);
            $sheet->getStyle('A'.$this->i.':H'.$this->i)->applyFromArray($styleArray);

            $this->i++;
            
                
                
                foreach($data as $value){  

                    $total_debito = $total_debito + $value['debito'];
                    $total_credito = $total_credito + $value['credito'];
                    $total_debito_acumulado = $total_debito_acumulado + $value['debito_acumulado'];
                    $total_credito_acumulado = $total_credito_acumulado + $value['credito_acumulado'];
                    $total_saldo_d = $total_saldo_d + $value['saldo_d'];
                    $total_saldo_c= $total_saldo_c + $value['saldo_c'];

                    $sheet->setCellValue('A'.$this->i, str_pad($value['cnt_plano_terceiro_id'],6,'0',STR_PAD_LEFT));
                    $sheet->setCellValue('B'.$this->i, $value['person']);
                    $sheet->setCellValue('C'.$this->i, $value['debito']);
                    $sheet->setCellValue('D'.$this->i, $value['credito']);
                    $sheet->setCellValue('E'.$this->i, $value['debito_acumulado']);
                    $sheet->setCellValue('F'.$this->i, $value['credito_acumulado']);
                    $sheet->setCellValue('G'.$this->i, $value['saldo_d']);
                    $sheet->setCellValue('H'.$this->i, $value['saldo_c']);
                    
                    $this->i++;
                }

                    $sheet->setCellValue('A'.$this->i, 'TOTAL');
                    $sheet->setCellValue('B'.$this->i,'');
                    $sheet->setCellValue('C'.$this->i, $total_debito);
                    $sheet->setCellValue('D'.$this->i, $total_credito);
                    $sheet->setCellValue('E'.$this->i, $total_debito_acumulado);
                    $sheet->setCellValue('F'.$this->i, $total_credito_acumulado);
                    $sheet->setCellValue('G'.$this->i, $total_saldo_d);
                    $sheet->setCellValue('H'.$this->i, $total_saldo_c);
                    $sheet->getStyle('A'.$this->i.':H'.$this->i)->getFont()->setBold(TRUE);


                    $this->i++;
            
            }
         }

        $writer = new Xlsx($spreadsheet);
        $writer->save('Spreadsheet/EcxtratoPerson.xlsx');



        return true;
    }



    public  function getPaisTable($cnt_plano_conta_id)
    {
        $plano = (new \yii\db\Query())
                    ->select([
                        'A.id'
                        ,'A.descricao'
                        ,'A.path'
                    ])
                    ->from(['A'=>'cnt_plano_conta'])
                    ->where(['id'=>$cnt_plano_conta_id]);

        $data = (new \yii\db\Query())
                    ->select([
                        'P.id'
                        ,'P.descricao'
                    ])
                    ->from(['B'=>$plano])
                    ->leftJoin(['P'=>'cnt_plano_conta'],['LIKE','B.path',new \yii\db\Expression("CONCAT(P.path,'%')"), FALSE])
                    ->orderBy('P.path')
                    ->all();
                    // print_r($data->createCommand()->getRawSql());die();

        
        return $data;
    }

  

   



  
    


}
?>