<?php

use kartik\sidenav\SideNav;
use app\modules\fin\services\FaturasService;

$count = FaturasService::countUnsendFaturas();
$ordemPag =  \app\modules\fin\services\PagamentoOrdemService::countStatus();

echo SideNav::widget([
  'type' => SideNav::TYPE_DEFAULT,
  'encodeLabels' => false,
  'items' => [
    ['label' => 'Home', 'url' => ['/site/index'], 'icon' => 'home'],

    [
      'label' => 'Tabela',
      'icon' => 'bookmark',
      'items' => [
        [
          'label' => 'Meio Financeiro',
          'url' => ['/fin/banco/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/banco'),
          'visible' => Yii::$app->user->can('fin/banco/index')
        ],
        [
          'label' => 'Conta Bancária',
          'url' => ['/fin/banco-conta/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/banco-conta'), 'visible' => Yii::$app->user->can('fin/banco-conta/index')
        ],
        [
          'label' => 'Moeda',
          'url' => ['/fin/currency/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/currency'),
          'visible' => Yii::$app->user->can('fin/currency/index')
        ],
      ]
    ],
    [
      'label' => 'Dashboard',
      'icon' => 'equalizer',
      'url' => ['/fin/dashboard/index'],
      'active' => Yii::$app->AuthService->isItemActive('fin/dashboard'),
      'visible' => Yii::$app->user->can('fin/dashboard/index')
    ],





    //Faturação
    [
      'label' => '<span class="pull-right badge badge-error"> ' . $count['totalb'] . ' </span><span class="pull-right badge badge-error"> ' . $count['total'] . ' </span>Faturação',
      'icon' => 'cloud',
      'items' => [
        [
          'label' => '<span class="pull-right badge badge-error"> ' . $count['faturaProformaSend'] . ' </span><span class="pull-right badge badge-error"> ' . $count['faturaProformaUnSend'] . ' </span> Fat. Proforma',
          'url' => ['/fin/fatura-proforma/index'], 'active' => Yii::$app->AuthService->isItemActive('fin/fatura-proforma'),
          'visible' => Yii::$app->user->can('fin/fatura-proforma/index')
        ],
        [
          'label' => '<span class="pull-right badge badge-error"> ' . $count['fpb'] . ' </span><span class="pull-right badge badge-error"> ' . $count['fp'] . ' </span> Fat. Provisória',
          'url' => ['/fin/fatura-provisoria/index'], 'active' => Yii::$app->AuthService->isItemActive('fin/fatura-provisoria'),
          'visible' => Yii::$app->user->can('fin/fatura-provisoria/index')
        ],
        [
          'label' => '<span class="pull-right badge badge-error"> ' . $count['fdb'] . ' </span><span class="pull-right badge badge-error"> ' . $count['fd'] . ' </span>Fat. Definitiva',
          'url' => ['/fin/fatura-definitiva/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/fatura-definitiva'), 'visible' => Yii::$app->user->can('fin/fatura-definitiva/index')
        ],
        [
          'label' => 'Fatura',
          'url' => ['/fin/fatura-eletronica/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/fatura-eletronica'), 'visible' => Yii::$app->user->can('fin/fatura-eletronica/index')
        ],
        [
          'label' => 'Nota de Débito',
          'url' => ['/fin/fatura-debito-cliente/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/fatura-debito-cliente'), 'visible' => Yii::$app->user->can('fin/fatura-debito-cliente/index')
        ],
        [
          'label' => 'Aviso de Credito',
          'url' => ['/fin/nota-credito/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/nota-credito'), 'visible' => Yii::$app->user->can('fin/nota-credito/index')
        ],
        [
          'label' => 'Nota de Credito',
          'url' => ['/fin/aviso-credito/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/aviso-credito'), 'visible' => Yii::$app->user->can('fin/aviso-credito/index')
        ],
        [
          'label' => 'Nota de Debito - Diversos',
          'url' => ['/fin/nota-debito/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/nota-debito'), 'visible' => Yii::$app->user->can('fin/nota-debito/index')
        ],

      ]
    ],

    [
      'label' => 'Despesas',
      'icon' => 'retweet',
      'url' => ['/fin/despesa/index'],
      'active' => Yii::$app->AuthService->isItemActive('fin/despesa'),
      'visible' => Yii::$app->user->can('fin/despesa/index')
    ],
    [
      'label' => 'Despesas & FP',
      'icon' => 'edit',
      'url' => ['/fin/despesa/grelha'],
      'active' => Yii::$app->AuthService->isItemActive('fin/despesa'),
      'visible' => Yii::$app->user->can('fin/despesa/grelha')
    ],





    [
      'label' => '<span class="pull-right badge badge-error"> ' . $ordemPag['total'] . ' </span> Tesouraria',
      'icon' => 'refresh',
      'items' => [
        [
          'label' => 'Recebimentos',
          'url' => ['/fin/recebimento/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/recebimento'), 'visible' => Yii::$app->user->can('fin/recebimento/index')
        ],
        [
          'label' => '<span class="pull-right badge badge-error"> ' . $ordemPag['por_validar'] . ' </span><span class="pull-right badge badge-error"> ' . $ordemPag['validado'] . ' </span> Ord. Pagamento',
          'url' => ['/fin/pagamento-ordem/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/pagamento-ordem'), 'visible' => Yii::$app->user->can('fin/pagamento-ordem/index')
        ],
        [
          'label' => 'Pagamentos',
          'url' => ['/fin/pagamento/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/pagamento'),
          'visible' => Yii::$app->user->can('fin/pagamento/index')
        ],
        [
          'label' => 'Encontro de Contas',
          'url' => ['/fin/of-accounts/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/of-accounts'), 'visible' => Yii::$app->user->can('fin/of-accounts/index')
        ],
        [
          'label' => 'Transferencia',
          'url' => ['/fin/transferencia/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/transferencia'), 'visible' => Yii::$app->user->can('fin/transferencia/index')
        ],
        [
          'label' => 'Diário de Tesouraria',
          'url' => ['/fin/caixa/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/caixa'),
          'visible' => Yii::$app->user->can('fin/transferencia/index')
        ],
      ]
    ],

    //Relatórios    
    [
      'label' => 'Relatórios',
      'icon' => 'stats',
      'items' => [
        [
          'label' => 'Posição de Tesouraria',
          'url' => ['/fin/banco-conta/posicao-tesouraria'],
          'active' => Yii::$app->AuthService->isItemActive('cnt/banco-conta'), 'visible' => Yii::$app->user->can('fin/banco-conta/posicao-tesouraria')
        ],
        [
          'label' => 'Extrato Bancário',
          'url' => ['/fin/caixa-transacao/index'],
          'active' => Yii::$app->AuthService->isItemActive('cnt/caixa-transacao'), 'visible' => Yii::$app->user->can('fin/caixa-transacao/index')
        ],
        [
          'label' => 'Despesa',
          'url' => ['/fin/despesa/relatorio'],
          'active' => Yii::$app->AuthService->isItemActive('cnt/despesa'),
          'visible' => Yii::$app->user->can('fin/despesa/relatorio')
        ],
        [
          'label' => 'Fatura Provisória',
          'url' => ['/fin/report/fatura-provisoria'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report'),
          'visible' => Yii::$app->user->can('fin/report/fatura-provisoria')
        ],
        [
          'label' => 'Fatura Definitiva',
          'url' => ['/fin/fatura-definitiva/report'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report'),
          'visible' => Yii::$app->user->can('fin/report/fatura-provisoria')
        ],
        [
          'label' => 'Valor a Favor de Cliente',
          'url' => ['/fin/report/valor-afavor'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report'),
          'visible' => Yii::$app->user->can('fin/report/valor-afavor')
        ],
        [
          'label' => 'Valor a Dever de Cliente',
          'url' => ['/fin/report/valor-adever'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report'),
          'visible' => Yii::$app->user->can('fin/report/valor-adever')
        ],
        [
          'label' => 'Valor a Favor / Dever',
          'url' => ['/fin/report/valor-afavor-adever'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report'),
          'visible' => Yii::$app->user->can('fin/report/valor-afavor')
        ],

        [
          'label' => 'Valor a Dever New' ,
          'url' => ['/fin/report/valor-adever-new'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report'),
          'visible' => Yii::$app->user->can('fin/report/valor-adever-new')
        ],
        [
          'label' => 'Recebimento',
          'url' => ['/fin/report/recebimento'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report'),
          'visible' => Yii::$app->user->can('fin/report/recebimento')
        ],
        [
          'label' => 'Adiantamento',
          'url' => ['/fin/report/adiantamento'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report'),
          'visible' => Yii::$app->user->can('fin/report/recebimento')
        ],
        [
          'label' => 'Honorário',
          'url' => ['/fin/fatura-provisoria/honorario'],
          'active' => Yii::$app->AuthService->isItemActive('fin/fatura-provisoria'), 'visible' => Yii::$app->user->can('fin/fatura-provisoria/honorario')
        ],
        [
          'label' => 'Lista a Dever 0',
          'url' => ['/fin/report-dever/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report-dever'), 
          'visible' => Yii::$app->user->can('fin/report-dever/index')
        ],
		    [
          'label' => 'Lista a Favor 0',
          'url' => ['/fin/report-favor/index'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report-favor'), 
          'visible' => Yii::$app->user->can('fin/report-favor/index')
        ],

        [
          'label' => 'Lista a Dever 1',
          'url' => ['/fin/report-dever/index-one'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report-dever'), 
          'visible' => Yii::$app->user->can('fin/report-dever/index')
        ],
		    [
          'label' => 'Lista a Favor 1',
          'url' => ['/fin/report-favor/index-one'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report-favor'), 
          'visible' => Yii::$app->user->can('fin/report-favor/index')
        ],
        /*[
          'label' => 'Lista a Favor Old',
          'url' => ['/fin/report-favor/index-old'],
          'active' => Yii::$app->AuthService->isItemActive('fin/report-favor'), 
          'visible' => Yii::$app->user->can('fin/report-favor/index')
        ],*/
      ]
    ],









  ],


]);
