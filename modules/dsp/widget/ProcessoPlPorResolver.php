<?php

namespace app\modules\dsp\widget;

use yii;
use yii\base\Widget;
use yii\helpers\Html;

class ProcessoPlPorResolver extends Widget
{
    public $data = [];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $formatter = Yii::$app->formatter;


        $html  = Html::beginTag('div', ['class' => 'row']);

        $html .= Html::beginTag('table', ['class' => 'table table-striped']);
        $html .= Html::beginTag('thead');

        $html .= Html::beginTag('tr');
        $html .= Html::tag('th', 'Nº Proc.', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Nord', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Cliente', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Mercadoria', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Nº PL', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Data Regul.', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Dias Regulação', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Data Liquidação', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Dias Liquidado', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Valor a Pagar', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::tag('th', 'Estncia Aduaneira', ['style' => 'border-bottom: 1px solid #ddd;']);
        $html .= Html::endTag('tr');

        $html .= Html::endTag('thead');
        $html .= Html::beginTag('tbody');
        foreach ($this->data as $processo) {
            if (!empty($processo->pedidoLevantamento->id)) {
                $html .= Html::beginTag('tr', ['style' => 'border-bottom: 1px solid #ddd;']);
                $html .= Html::tag('td', $processo->numero . '/' . $processo->bas_ano_id, ['style' => 'border-bottom: 1px solid #ddd;']);
                $html .= Html::tag('td', empty($processo->nord->id) ? '' : $processo->nord->id, ['style' => 'border-bottom: 1px solid #ddd;']);
                $html .= Html::tag('td', $processo->person->nome, ['style' => 'border-bottom: 1px solid #ddd;']);
                $html .= Html::tag('td', $processo->descricao, ['style' => 'border-bottom: 1px solid #ddd;']);

                $html .= Html::tag('td',  empty($processo->pedidoLevantamento->id) ? '' : $processo->pedidoLevantamento->ser . '-' . $processo->pedidoLevantamento->id, ['style' => 'border-bottom: 1px solid #ddd;']);

                $html .= Html::tag('td',  empty($processo->pedidoLevantamento->data_proragacao) ? empty($processo->pedidoLevantamento->data_regularizacao) ? '' : $formatter->asDate($processo->pedidoLevantamento->data_regularizacao) : $formatter->asDate($processo->pedidoLevantamento->data_proragacao), ['style' => 'border-bottom: 1px solid #ddd;']);

                $html .= Html::tag('td',  empty($processo->pedidoLevantamento->data_proragacao) ? $formatter->asRelativeTime($processo->pedidoLevantamento->data_regularizacao) : $formatter->asRelativeTime($processo->pedidoLevantamento->data_proragacao), ['style' => 'border-bottom: 1px solid #ddd;']);

                $html .= Html::tag('td',  empty($processo->nord->despacho->id) ? '' : $formatter->asDate($processo->nord->despacho->data_liquidacao), ['style' => 'border-bottom: 1px solid #ddd;']);

                $html .= Html::tag('td',  empty($processo->nord->despacho->id) ? '' : $formatter->asRelativeTime($processo->nord->despacho->data_liquidacao), ['style' => 'border-bottom: 1px solid #ddd;']);

                $html .= Html::tag('td', empty($processo->nord->despacho->valor) ? '' : $formatter->asCurrency($processo->nord->despacho->valor), ['style' => 'border-bottom: 1px solid #ddd;']);

                $html .= Html::tag('td', empty($processo->nord->id) ? '' : $processo->nord->desembaraco->code, ['style' => 'border-bottom: 1px solid #ddd;']);
                $html .= Html::endTag('tr');
            }
        }

        $html .= Html::endTag('tbody');
        $html .= Html::endTag('table');
        $html .= Html::endTag('div');

        return $html;
    }
}
