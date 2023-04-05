<?php

namespace app\modules\cnt\widget;

use yii;
use yii\base\Widget;
use yii\helpers\Html;
use app\modules\cnt\models\PlanoConta;
use app\modules\cnt\models\PlanoTerceiro;
use app\modules\cnt\repositories\RazaoRepository;

class ExtratoContaPorTerceiro extends Widget
{
    public $cnt_plano_conta_id;
    public $titleRepor;
    public $data = [];
    public $planoContas = [];
    public $total_debito = 0;
    public $total_credito = 0;
    public $total_saldo = 0;
    public $terceiro = [];


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
        set_time_limit(0);
        $html  = Html::beginTag('div', ['class' => "row"]);
        $html .= Html::tag('p', 'MES: ' . $this->titleRepor, ['class' => "text-center"]);
        $html .= Html::tag('p', 'Moeda: Nacional', ['class' => "text-center"]);
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


        foreach ($this->planoContas as $key => $value) {
            $this->data['cnt_plano_conta_id'] = $value['id'];
            $html .= static::getFilhosTable($this->data);
        }

        $html .= Html::endTag('tbody');
        $html .= Html::endTag('table');
        $html .= Html::endTag('div');


        return $html;
    }







    public static function getFilhosTable($data)
    {
        $html = '';

        $formatter = Yii::$app->formatter;
        $terceiroData = RazaoRepository::listExtratoTerceiro($data);
        // print_r($data);die();
        foreach ($terceiroData as $key => $terceiro) {
            $total_credito = 0;
            $total_debito = 0;
            $total_saldo = 0;
            $data['cnt_plano_terceiro_id'] = $terceiro['cnt_plano_terceiro_id'];


            $query = RazaoRepository::queryExtrato($data);
            $total_saldo = $total_saldo + RazaoRepository::queryExtratoSaldoMesAnterior($data);
            if ($query->count() > 0 || $total_saldo > 0) {
                $html .= static::getPaisTable($data['cnt_plano_conta_id']);
                $html .= '<tr>';
                $html .= '<th colspan="2">' . $data['cnt_plano_conta_id'] . '</th>';
                $html .= '<th colspan="7">' . PlanoConta::findOne($data['cnt_plano_conta_id'])->descricao . '</th>';
                $html .= '</tr>';

                if ($terceiro['cnt_plano_terceiro_id'] > 0) {
                    $html .= Html::beginTag('tr');
                    $html .= Html::tag('th', str_pad($terceiro['cnt_plano_terceiro_id'], 6, '0', STR_PAD_LEFT));
                    $html .= Html::tag('th', $terceiro['cnt_plano_terceiro_name'], ['colspan' => '8']);
                    $html .= Html::endTag('tr');
                }

                $html .= '<tr>';
                $html .= '<th colspan="5"></th>';
                $html .= Html::tag('td', 'Saldo Anterior', ['colspan' => '3']);
                $html .= Html::tag('td', $formatter->asCurrency($total_saldo), ['class' => "text-right"]);
                $html .= '</tr>';
            }
            if ($query->count() > 0) {
                foreach ($query->all()  as $key => $value) {
                    $total_credito = $total_credito + $value['credito'];
                    $total_debito = $total_debito + $value['debito'];
                    $total_saldo = $total_saldo + ($value['debito'] - $value['credito']);
                    $ano = \app\models\Ano::findOne($value['bas_ano_id'])->ano;
                    $html .= Html::beginTag('tr');
                    $html .= Html::tag('td', $value['bas_mes_id'] . '-' . $ano);
                    $html .= Html::tag('td', $formatter->asDate($value['data'], 'dd'));
                    $html .= Html::tag('td', str_pad($value['cnt_diario_id'], 2, '0', STR_PAD_LEFT));
                    $html .= Html::tag('td', str_pad($value['num_doc'], 6, '0', STR_PAD_LEFT));
                    $html .= Html::tag('td', $value['terceiro'] ? str_pad($value['terceiro'], 6, '0', STR_PAD_LEFT) : null);
                    $html .= Html::tag('td', $value['descricao']);
                    $html .= Html::tag('td', !$value['debito'] ? null : $formatter->asCurrency($value['debito']), ['class' => 'text-right']);
                    $html .= Html::tag('td', !$value['credito'] ? null : $formatter->asCurrency($value['credito']), ['class' => 'text-right']);
                    $html .= Html::tag('td',  $formatter->asCurrency($total_saldo), ['class' => "text-right"]);
                    $html .= Html::endTag('tr');
                }
            }


            if ($query->count() > 0 || $total_saldo > 0) {
                $html .= Html::beginTag('tr');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('th', 'Totais Acumulado');
                $html .= Html::tag('th', $formatter->asCurrency($total_debito), ['class' => 'text-right']);
                $html .= Html::tag('th', $formatter->asCurrency($total_credito), ['class' => 'text-right']);
                $html .= Html::tag('th', $formatter->asCurrency($total_saldo), ['class' => 'text-right']);
                $html .= Html::endTag('tr');

                $html .= Html::beginTag('tr');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('th', 'Saldo');
                $html .= Html::tag('th', $total_saldo < 0 ? $formatter->asCurrency(abs($total_saldo)) : null, ['class' => 'text-right']);
                $html .= Html::tag('td', $total_saldo > 0 ? $formatter->asCurrency(abs($total_saldo)) : null, ['class' => 'text-right']);
                $html .= Html::tag('td', $total_saldo > 0 ? 'DB' : 'CR', ['class' => 'text-right']);
                $html .= Html::endTag('tr');

                $html .= Html::beginTag('tr');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('td');
                $html .= Html::tag('th', 'Total Controlo');
                $html .= Html::tag('th', $total_saldo < 0 ? $formatter->asCurrency($total_debito + abs($total_saldo)) : $formatter->asCurrency($total_debito));
                $html .= Html::tag('th', $total_saldo > 0 ? $formatter->asCurrency($total_credito + abs($total_saldo)) : $formatter->asCurrency($total_credito));
                $html .= Html::tag('td');
                $html .= Html::endTag('tr');
            }
        }

        return $html;
    }

    public static function getPaisTable($cnt_plano_conta_id, $nivel = 0)
    {
        $plano = PlanoConta::find()->where(['id' => $cnt_plano_conta_id])->asArray()->one();
        $html = '';
        $html2 = '';
        if ($nivel == 1) {
            $html = '<tr>';
            $html .= '<th colspan="2">' . $plano['id'] . '</th>';
            $html .= '<th colspan="7">' . $plano['descricao'] . '</th>';
            $html .= '</tr>';
        }
        if (!empty($plano['cnt_plano_conta_id']) && $plano['cnt_plano_conta_id'] > 0) {
            $html2 = static::getPaisTable($plano['cnt_plano_conta_id'], 1);
        }
        $html2 .= $html;


        return $html2;
    }
}
