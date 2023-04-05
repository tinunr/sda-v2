<?php

use kartik\sidenav\SideNav;

$dspWork = \app\modules\dsp\services\DspService::CountWorkflowStatus(1, Yii::$app->user->identity->id);

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
          'label' => 'Cliente / Fornecedor',
          'url' => ['/dsp/person/index'],
          'active' => Yii::$app->AuthService->isItemActive('dsp/person'),
          'visible' => Yii::$app->user->can('dsp/person/index')
        ],
        [
          'label' => 'Documento',
          'url' => ['/documento/index'],
          'active' => Yii::$app->AuthService->isItemActive('app/documento'),
          'visible' => Yii::$app->user->can('app/documento/index')
        ],
        [
          'label' => 'Nº Documento',
          'url' => ['/documento-numero/index'],
          'active' => Yii::$app->AuthService->isItemActive('app/documento-numero'),
          'visible' => Yii::$app->user->can('app/documento-numero/index')
        ],
        [
          'label' => 'Itens',
          'url' => ['/dsp/item/index'],
          'active' => Yii::$app->AuthService->isItemActive('dsp/item'),
          'visible' => Yii::$app->user->can('dsp/item/index')
        ],
        [
          'label' => 'Regime',
          'url' => ['/dsp/regime/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/regime'),
          'visible' => Yii::$app->user->can('dsp/regime/index')
        ],
        [
          'label' => 'Unidade',
          'url' => ['/dsp/unidade/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/unidade'),
          'visible' => Yii::$app->user->can('dsp/unidade/index')
        ],
        [
          'label' => 'Documento Despacho',
          'url' => ['/dsp/despacho-documento/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/despacho-documento'),
          'visible' => Yii::$app->user->can('dsp/despacho-documento/index')
        ],
        [
          'label' => 'Origem', 'url' => ['/dsp/origem/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/origem'), 'visible' => Yii::$app->user->can('dsp/origem/index')
        ],
        [
          'label' => 'Setor', 'url' => ['/dsp/setor/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/setor'), 'visible' => Yii::$app->user->can('dsp/setor/index')
        ],
        [
          'label' => 'Tarefa', 'url' => ['/dsp/tarefa/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/tarefa'), 'visible' => Yii::$app->user->can('dsp/tarefa/index')
        ],
        [
          'label' => 'Paises', 'url' => ['/dsp/pais/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/pais'), 'visible' => Yii::$app->user->can('dsp/pais/index')
        ],

      ]
    ],
    [
      'label' => 'Dashboard',
      'icon' => 'bookmark',
      'items' => [
        [
          'label' => 'Processo Interno',
          'url' => ['/dsp/default/processo-interno'],
          'active' => Yii::$app->AuthService->isItemActive('dsp/default'),
          'visible' => Yii::$app->user->can('dsp/default/processo-interno')
        ],
        [
          'label' => 'Processo Externo',
          'url' => ['/dsp/default/processo-externo'],
          'active' => Yii::$app->AuthService->isItemActive('dsp/default'),
          'visible' => Yii::$app->user->can('dsp/default/processo-externo')
        ],
      ],
    ],
    [
      'label' => 'Processo',
      'icon' => 'book',
      'url' => ['/dsp/processo/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/processo'),
      'visible' => Yii::$app->user->can('dsp/processo/index')
    ],
    [
      'label' => '<span class="pull-right badge badge-error"> ' . $dspWork . ' </span> Workflow',
      'icon' => 'retweet',
      'url' => ['/dsp/processo-workflow/index'],
      'active' => Yii::$app->AuthService->isItemActive('dsp/processo-workflow'), 'visible' => Yii::$app->user->can('dsp/processo-workflow/index')
    ],
    [
      'label' => 'Disponível no Setor',
      'icon' => 'tasks',
      'url' => ['/dsp/processo-workflow/processo-setor'], 'active' => Yii::$app->AuthService->isItemActive('dsp/processo-workflow'),
      'visible' => Yii::$app->user->can('dsp/processo-workflow/index')
    ],
    [
      'label' => 'Classificação',
      'icon' => 'calendar',
      'url' => ['/dsp/processo-workflow/classificacao-user'],
      'active' => Yii::$app->AuthService->isItemActive('dsp/processo-workflow'), 'visible' => Yii::$app->user->can('dsp/processo-workflow/classificacao-user')
    ],
    [
      'label' => 'Arquivo',
      'icon' => 'compressed',
      'url' => ['/dsp/processo-workflow/arquivo'], 'active' => Yii::$app->AuthService->isItemActive('dsp/processo-workflow'),
      'visible' => Yii::$app->user->can('dsp/processo-workflow/arquivo')
    ],
    [
      'label' => 'NORD', 'icon' => 'folder-close',
      'url' => ['/dsp/nord/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/nord'),
      'visible' => Yii::$app->user->can('dsp/nord/index')
    ],
    [
      'label' => 'P. Levantamento',
      'icon' => 'new-window',
      'url' => ['/dsp/pedido-levantamento/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/pedido-levantamento'),
      'visible' => Yii::$app->user->can('dsp/pedido-levantamento/index')
    ],
    [
      'label' => 'Despachos',
      'icon' => 'floppy-saved',
      'url' => ['/dsp/despacho/index'], 'active' => Yii::$app->AuthService->isItemActive('dsp/despacho'), '
                visible' => Yii::$app->user->can('dsp/despacho/index')
    ],

    //report processo    
    [
      'label' => 'Relatórios',
      'icon' => 'stats',
      'items' => [
        [
          'label' => 'Processo',
          'url' => ['/dsp/report/processo'],
          // 'active' => Yii::$app->AuthService->isItemActive('dsp/report'),
          'visible' => Yii::$app->user->can('dsp/report/index')
        ],
        [
          'label' => 'Processo Geral',
          'url' => ['/dsp/report/index'],
          // 'active' => Yii::$app->AuthService->isItemActive('dsp/report'),
          'visible' => Yii::$app->user->can('dsp/report/index')
        ],
        [
          'label' => 'Processo Liquidado',
          'url' => ['/dsp/report/pr-liquidado'],
          // 'active' => Yii::$app->AuthService->isItemActive('dsp/report'),
          'visible' => Yii::$app->user->can('dsp/report/index')
        ],
        [
          'label' => 'Processo Registado',
          'url' => ['/dsp/report/pr-registrado'],
          // 'active' => Yii::$app->AuthService->isItemActive('dsp/report'),
          'visible' => Yii::$app->user->can('dsp/report/index')
        ],
        [
          'label' => 'PL Por Resolver',
          'url' => ['/dsp/report/pr-pl-por-resolver'],
          // 'active' => Yii::$app->AuthService->isItemActive('dsp/report'),
          'visible' => Yii::$app->user->can('dsp/report/index')
        ],
        [
          'label' => 'Workflow',
          'url' => ['/dsp/processo-workflow/report'],
          // 'active' => Yii::$app->AuthService->isItemActive('dsp/report'),
          'visible' => Yii::$app->user->can('dsp/processo-workflow/report')
        ],
        [
          'label' => 'Classificação',
          'url' => ['/dsp/processo-workflow/classificacao'],
          // 'active' => Yii::$app->AuthService->isItemActive('dsp/processo-workflow'), 'visible' => Yii::$app->user->can('dsp/processo-workflow/classificacao')
        ],
        [
          'label' => 'Classificação Setor',
          'url' => ['/dsp/processo-workflow/classificacao-setor'],
          // 'active' => Yii::$app->AuthService->isItemActive('dsp/processo-workflow'), 'visible' => Yii::$app->user->can('dsp/processo-workflow/classificacao')
        ],
      ]
    ]

  ],

]);
