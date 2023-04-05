<?php

namespace app\components\helpers;

/**
 * A simple class which fetches Bing's image of the day with meta data.
 */
class MenuHelper
{
    public static function setAcessoRapido($app_module_id)
    {

        switch ($app_module_id) {
            case $app_module_id == 'dsp':
                return  [
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Processo', 'url' => ['/dsp/processo/create']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> NORD',
                        'url' => ['/dsp/nord/create']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Cliente / Fornecedor',
                        'url' => ['/dsp/person/create']
                    ],
                    '<li role="separator" class="divider"></li>',
                    [
                        'label' => '<i class="fa  fa-sync"></i> Workflow', 'url' => ['/dsp/processo-workflow/index']
                    ],
                    [
                        'label' => '<i class="fa  fa-list"></i> Despacho',
                        'url' => ['/dsp/despacho/index']
                    ],
                    [
                        'label' => '<i class="fa  fa-list"></i> Pedido Levantamento',
                        'url' => ['/dsp/pedido-levantamento/index']
                    ],
                ];
                break;
            case $app_module_id == 'fin':
                return  [
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Fatura provisória', 'url' => ['/fin/fatura-provisoria/index']
                    ],

                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Fatura Definitiva', 'url' => ['/fin/fatura-definitiva/index']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Recebimento',
                        'url' => ['/fin/recebimento/index']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Pagamento',
                        'url' => ['/fin/pagamento/index']
                    ],
                    '<li role="separator" class="divider"></li>',
                    [
                        'label' => '<i class="fa  fa-list"></i> Despesa',
                        'url' => ['/fin/despesa/index']
                    ],
                    [
                        'label' => '<i class="fa  fa-list"></i> Diario de Caixa',
                        'url' => ['/fin/caixa/index']
                    ],
                ];
                break;
            case $app_module_id == 'cnt':
                return  [
                    [
                        'label' => '<i class="fa  fa-sync"></i> Livro Diário',
                        'url' => ['/cnt/razao/index']
                    ],
                    [
                        'label' => '<i class="fa  fa-print"></i> Modelo 106',
                        'url' => ['/cnt/modelo106/index']
                    ],
                    [
                        'label' => '<i class="fas  fa-print"></i> Extrato',
                        'url' => ['/cnt/extrato/index'],
                    ],
                    [
                        'label' => '<i class="fas  fa-print"></i> Balancete',
                        'url' => ['/cnt/balancete/index'],
                    ]
                ];
                break;
            default:
                return  [
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Processo',
                        'url' => ['/dsp/processo/create']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> NORD',
                        'url' => ['/dsp/norde/create']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Fatura Provisória',
                        'url' => ['/dsp/fatura-provisoria/create']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Fatura Definitiva',
                        'url' => ['/dsp/fatura-definitiva/create']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Recebimento',
                        'url' => ['/dsp/revebimento/create']
                    ],
                    [
                        'label' => '<i class="fa  fa-plus-circle"></i> Pagamento',
                        'url' => ['/dsp/pagamento/create']
                    ],
                ];
                break;
        }
    }
}
