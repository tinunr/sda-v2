<?php

namespace app\modules\cnt\widget;

use yii;
use yii\base\Widget;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoTerceiro;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExtratoContaExcel extends Widget
{
    public $bas_ano_id;
    public $begin_mes;
    public $end_mes;
    public $cnt_plano_conta_id;
    public $cnt_plano_terceiro_id;
    public $data = [];
    public $planoContas = [];

    public $total_debito = 0;
    public $total_credito = 0;
    public $total_saldo = 0;
    public $terceiro = [];
    public $i = 0;


    public function init()
    {
        parent::init();
        if (!empty($this->data['cnt_plano_terceiro_id'])) {
            $this->terceiro  = PlanoTerceiro::find()->where(['id' => $this->data['cnt_plano_terceiro_id']])->AsArray()->one();
        }
        if (empty($this->cnt_plano_conta_id)) {
            $this->planoContas  = PlanoConta::find()->orderBy('path')->AsArray()->all();
        } else {
            $plano_conta = PlanoConta::find()->where(['id' => $this->cnt_plano_conta_id])->AsArray()->one();
            $this->planoContas = PlanoConta::find()->where(['LIKE', 'path', $plano_conta['path'] . '%', false])->orderBy('path')->AsArray()->all();
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
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(80);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);

        $sheet->setTitle('Extrato de Conta');
        $sheet->setCellValue('A1', 'Agência de Despacho Aduaneiro Morais & Cruz, Lda');
        $sheet->setCellValue('F2', 'Balancete de Verificação do Razão Geral');
        $sheet->setCellValue('F3', 'Mês:' . $this->begin_mes . ' - ' . $this->bas_ano_id . ' / ' . $this->end_mes . ' - ' . $this->bas_ano_id);
        $sheet->setCellValue('F4', 'Moeda: Nacional');
        $dateTimeNow = time();
        $sheet->setCellValue('H1', 'Data:');
        $sheet->setCellValue('I1', Date::PHPToExcel($dateTimeNow));
        $sheet->getStyle('I1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);


        $this->i = 6;

        //  $sheet->getStyle('B1:B3')->getFill()->getStartColor('#3c5462')->setARGB('#F1F2F3');
        //  $sheet->getStyle('B1:B3')->getFont()->setBold(TRUE);
        // print_r($this->data['cnt_plano_terceiro_id']);die();
        if (!empty($this->data['cnt_plano_terceiro_id'])) {
            $sheet->setCellValue('A' . $this->i, str_pad($this->terceiro['id'], 2, '0', STR_PAD_LEFT));
            $sheet->setCellValue('B' . $this->i, $this->terceiro['nome']);
            $sheet->mergeCells('B' . $this->i . ':I' . $this->i);
            $this->i++;
        }


        $sheet->setCellValue('A' . $this->i, 'Mês');
        $sheet->setCellValue('B' . $this->i, 'Dia');
        $sheet->setCellValue('C' . $this->i, 'Diário');
        $sheet->setCellValue('D' . $this->i, 'Docum.');
        $sheet->setCellValue('E' . $this->i, 'Tereiro');
        $sheet->setCellValue('F' . $this->i, 'Descritivo');
        $sheet->setCellValue('G' . $this->i, 'Debito');
        $sheet->setCellValue('H' . $this->i, 'Credito');
        $sheet->setCellValue('I' . $this->i, 'Saldo');

        $sheet->getStyle('A' . $this->i . ':I' . $this->i)->getFont()->setBold(TRUE);
        $sheet->getStyle('A' . $this->i . ':I' . $this->i)->applyFromArray($styleArray);

        $this->i++;
        //  $sheet->setAutoFilter('A5:I5');

        //  print_r($this->planoContas);die();



        foreach ($this->planoContas as $key => $value) {

            $total_credito = 0;
            $total_debito = 0;
            $total_saldo = 0;




            // // $sheet->getStyle('A'.$this->i.':B'.$this->i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('EAEDED');



            if (strlen($value['id']) >= 2) {


                $this->data['cnt_plano_conta_id'] = $value['id'];
                $query = Yii::$app->CntQuery->queryExtrato($this->data);
                $queryCount = $query->count();
                $total_saldo = $total_saldo + Yii::$app->CntQuery->queryExtratoSaldoMesAnterior($this->data);


                if ($queryCount > 0 || $total_saldo > 0) {
                    $sheet->setCellValue('A' . $this->i, $value['id']);
                    $sheet->setCellValue('B' . $this->i, $value['descricao']);
                    $sheet->mergeCells('B' . $this->i . ':E' . $this->i);
                    $sheet->getStyle('A' . $this->i . ':B' . $this->i)->getFont()->setBold(TRUE);
                    $this->i++;

                    $sheet->setCellValue('F' . $this->i, 'Saldo Anterior');
                    $sheet->setCellValue('I' . $this->i, $formatter->asCurrency($total_saldo));
                    $this->i++;
                }
                if ($queryCount > 0) {
                    foreach ($query->all() as $value) {

                        $total_debito = $total_debito + $value['debito'];
                        $total_credito = $total_credito + $value['credito'];
                        $total_saldo = $total_saldo + ($value['debito'] - $value['credito']);

                        $sheet->setCellValue('A' . $this->i, $formatter->asDate($value['data'], 'MM-Y'));
                        $sheet->setCellValue('B' . $this->i, $formatter->asDate($value['data'], 'dd'));
                        $sheet->setCellValue('C' . $this->i, str_pad($value['cnt_diario_id'], 2, '0', STR_PAD_LEFT));
                        $sheet->setCellValue('D' . $this->i, str_pad($value['num_doc'], 6, '0', STR_PAD_LEFT));
                        $sheet->setCellValue('E' . $this->i, $value['terceiro'] ? str_pad($value['terceiro'], 6, '0', STR_PAD_LEFT) : null);
                        $sheet->setCellValue('F' . $this->i, $value['descricao']);
                        $sheet->setCellValue('G' . $this->i, !$value['debito'] ? null : $value['debito']);
                        $sheet->setCellValue('H' . $this->i, !$value['credito'] ? null : $value['credito']);
                        $sheet->setCellValue('I' . $this->i, $total_saldo);


                        $this->i++;
                    }
                    if ($queryCount > 0 || $total_saldo > 0) {
                        $sheet->setCellValue('F' . $this->i, 'Totais Acumulados');
                        $sheet->setCellValue('G' . $this->i, $total_debito);
                        $sheet->setCellValue('H' . $this->i, $total_credito);
                        $sheet->getStyle('A' . $this->i . ':I' . $this->i)->getFont()->setBold(TRUE);
                        $sheet->getStyle('A' . $this->i . ':I' . $this->i)->applyFromArray($styleArray);
                        $this->i++;
                        $sheet->setCellValue('F' . $this->i, 'Saldo ');
                        $sheet->setCellValue('G' . $this->i, $total_saldo < 0 ? abs($total_saldo) : null);
                        $sheet->setCellValue('H' . $this->i, $total_saldo > 0 ? abs($total_saldo) : null);
                        $sheet->setCellValue('I' . $this->i, $total_saldo > 0 ? 'DB' : 'CR');
                        $sheet->getStyle('A' . $this->i . ':I' . $this->i)->getFont()->setBold(TRUE);
                        $sheet->getStyle('A' . $this->i . ':I' . $this->i)->applyFromArray($styleArray);
                        $this->i++;
                        $sheet->setCellValue('F' . $this->i, 'Total Controlo');
                        $sheet->setCellValue('G' . $this->i, $total_saldo < 0 ? $total_debito + abs($total_saldo) : $total_debito);
                        $sheet->setCellValue('H' . $this->i, $total_saldo > 0 ? $total_credito + abs($total_saldo) : $total_credito);
                        $sheet->getStyle('A' . $this->i . ':I' . $this->i)->getFont()->setBold(TRUE);
                        $sheet->getStyle('A' . $this->i . ':I' . $this->i)->applyFromArray($styleArray);
                        $this->i++;
                    }
                }
            }
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save('Spreadsheet/EcxtratoConta.xlsx');



        return true;
    }



    public function getPaisTable($cnt_plano_conta_id, $nivel = 0)
    {
        $plano = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();
        $html = [];
        $nivel++;
        if ($nivel > 0) {
            $html = [
                'id' => $plano['id'],
                'descricao' => $plano['descricao'],
            ];
        }
        if (!empty($plano['cnt_plano_conta_id']) && $plano['cnt_plano_conta_id'] > 0) {
            $html[$nivel] = static::getPaisTable($plano['cnt_plano_conta_id'], $nivel);
        }


        return $html;
    }
}
