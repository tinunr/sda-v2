<?php

namespace app\modules\cnt\widget;

use yii;
use yii\base\Widget;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoTerceiro;
use app\modules\cnt\repositories\RazaoRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExtratoContaPorTerceiroExcel extends Widget
{

    public $data = [];
    public $planoContas = [];
    public $total_debito = 0;
    public $total_credito = 0;
    public $total_saldo = 0;
    public $terceiro = [];
    public $lineId;
    public $spreadsheet;
    public $sheet;
    public $titleRepor;
    public $begin_mes;
    public $end_mes;
    public $cnt_plano_conta_id;
    public $cnt_plano_terceiro_id;


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
                ],
            ],
        ];

        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();

        $this->sheet->getColumnDimension('A')->setWidth(10);
        $this->sheet->getColumnDimension('B')->setWidth(10);
        $this->sheet->getColumnDimension('C')->setWidth(10);
        $this->sheet->getColumnDimension('D')->setWidth(10);
        $this->sheet->getColumnDimension('E')->setWidth(10);
        $this->sheet->getColumnDimension('F')->setWidth(80);
        $this->sheet->getColumnDimension('G')->setWidth(20);
        $this->sheet->getColumnDimension('H')->setWidth(20);
        $this->sheet->getColumnDimension('I')->setWidth(20);



        $this->sheet->setTitle('Extrato de Conta Por Terceiro');
        $this->sheet->setCellValue('A1', 'Agência de Despacho Aduaneiro Morais & Cruz, Lda');
        $this->sheet->mergeCells('A1:F1');
        $this->sheet->setCellValue('G2', 'Mês:' . $this->begin_mes . ' - '  . ' / ' . $this->end_mes . ' - ');
        $this->sheet->setCellValue('G3', 'Moeda: Nacional');
        $dateTimeNow = time();
        $this->sheet->setCellValue('H1', 'Data:');
        $this->sheet->setCellValue('I1', Date::PHPToExcel($dateTimeNow));
        $this->sheet->getStyle('I1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);


        $this->sheet->setCellValue('A4', 'Mês');
        $this->sheet->setCellValue('B4', 'Dia');
        $this->sheet->setCellValue('C4', 'Diário');
        $this->sheet->setCellValue('D4', 'Docum.');
        $this->sheet->setCellValue('E4', 'Tereiro');
        $this->sheet->setCellValue('F4', 'Descritivo');
        $this->sheet->setCellValue('G4', 'Debito');
        $this->sheet->setCellValue('H4', 'Credito');
        $this->sheet->setCellValue('I4', 'Saldo');
        $this->lineId = 5;
        foreach ($this->planoContas as $key => $value) {
            $this->data['cnt_plano_conta_id'] = $value['id'];
            $formatter = Yii::$app->formatter;


            $terceiroData = RazaoRepository::listExtratoTerceiro($this->data);
            foreach ($terceiroData as $key => $terceiro) {

                $total_credito = 0;
                $total_debito = 0;
                $total_saldo = 0;
                $this->data['cnt_plano_terceiro_id'] = $terceiro['cnt_plano_terceiro_id'];

                $query = RazaoRepository::queryExtrato($this->data);

                $total_saldo = $total_saldo + RazaoRepository::queryExtratoSaldoMesAnterior($this->data);
                if ($query->count() > 0 || $total_saldo > 0) {

                    $this->sheet->setCellValue('A' . $this->lineId, $this->data['cnt_plano_conta_id']);
                    $this->sheet->setCellValue('B' . $this->lineId, PlanoConta::findOne($this->data['cnt_plano_conta_id'])->descricao);
                    $this->sheet->mergeCells('B' . $this->lineId . ':I' . $this->lineId);
                    $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->getFont()->setBold(TRUE);
                    $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->applyFromArray($styleArray);
                    $this->lineId++;

                    if (!empty($terceiro['cnt_plano_terceiro_id'])) {
                        $this->sheet->setCellValue('A' . $this->lineId, str_pad($terceiro['cnt_plano_terceiro_id'], 6, '0', STR_PAD_LEFT));
                        $this->sheet->setCellValue('B' . $this->lineId, $terceiro['cnt_plano_terceiro_name']);
                        $this->sheet->mergeCells('B' . $this->lineId . ':I' . $this->lineId);
                        $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->getFont()->setBold(TRUE);
                        $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->applyFromArray($styleArray);
                        $this->lineId++;
                    }

                    $this->sheet->setCellValue('H' . $this->lineId, 'Saldo Anterior');
                    $this->sheet->setCellValue('I' . $this->lineId, ($total_saldo));
                    $this->lineId++;
                }
                if ($query->count() > 0) {
                    foreach ($query->all()  as $key => $value) {
                        $total_credito = $total_credito + $value['credito'];
                        $total_debito = $total_debito + $value['debito'];
                        $total_saldo = $total_saldo + ($value['debito'] - $value['credito']);
                        $ano = \app\models\Ano::findOne($value['bas_ano_id'])->ano;

                        $this->sheet->setCellValue('A' . $this->lineId, $value['bas_mes_id'] . '-' . $ano);
                        $this->sheet->setCellValue('B' . $this->lineId, $formatter->asDate($value['data'], 'dd'));
                        $this->sheet->setCellValue('C' . $this->lineId, str_pad($value['cnt_diario_id'], 2, '0', STR_PAD_LEFT));
                        $this->sheet->setCellValue('D' . $this->lineId, str_pad($value['num_doc'], 6, '0', STR_PAD_LEFT));
                        $this->sheet->setCellValue('E' . $this->lineId, $value['terceiro'] ? str_pad($value['terceiro'], 6, '0', STR_PAD_LEFT) : null);
                        $this->sheet->setCellValue('F' . $this->lineId, $value['descricao']);
                        $this->sheet->setCellValue('G' . $this->lineId, !$value['debito'] ? null : ($value['debito']));
                        $this->sheet->setCellValue('H' . $this->lineId, !$value['credito'] ? null : ($value['credito']));
                        $this->sheet->setCellValue('I' . $this->lineId, ($total_saldo));

                        $this->lineId++;
                    }
                }


                if ($query->count() > 0 || $total_saldo > 0) {
                    $this->sheet->setCellValue('F' . $this->lineId, 'Totais Acumulado');
                    $this->sheet->setCellValue('G' . $this->lineId, ($total_debito));
                    $this->sheet->setCellValue('H' . $this->lineId, ($total_credito));
                    $this->sheet->setCellValue('I' . $this->lineId, ($total_saldo));
                    $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->getFont()->setBold(TRUE);
                    $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->applyFromArray($styleArray);
                    $this->lineId++;
                    $this->sheet->setCellValue('F' . $this->lineId, 'Saldo');
                    $this->sheet->setCellValue('G' . $this->lineId, $total_saldo < 0 ? (abs($total_saldo)) : null);
                    $this->sheet->setCellValue('H' . $this->lineId, $total_saldo > 0 ? (abs($total_saldo)) : null);
                    $this->sheet->setCellValue('I' . $this->lineId, $total_saldo > 0 ? 'DB' : 'CR');
                    $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->getFont()->setBold(TRUE);
                    $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->applyFromArray($styleArray);
                    $this->lineId++;
                    $this->sheet->setCellValue('F' . $this->lineId, 'Total Controlo');
                    $this->sheet->setCellValue('G' . $this->lineId, $total_saldo < 0 ? ($total_debito + abs($total_saldo)) : ($total_debito));
                    $this->sheet->setCellValue(
                        'H' . $this->lineId,
                        $total_saldo > 0 ? ($total_credito + abs($total_saldo)) : $total_credito
                    );
                    $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->getFont()->setBold(TRUE);
                    $this->sheet->getStyle('A' . $this->lineId . ':I' . $this->lineId)->applyFromArray($styleArray);
                    $this->lineId++;
                }
            }
        }

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('Spreadsheet/EcxtratoContaPorTerceiro.xlsx');
    }
}
