<?php

use kartik\sidenav\SideNav;


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
                    'label' => 'Funcionário',
                    'url' => ['/hmr/funcionario/index'],
                    // 'active' => Yii::$app->AuthService->isItemActive('cnt/documento'), 'visible' => Yii::$app->user->can('cnt/documento/index')
                ],
                [
                    'label' => 'Cargo',
                    'url' => ['/hmr/cargo/index'],
                    // 'active' => Yii::$app->AuthService->isItemActive('cnt/diario'), 'visible' => Yii::$app->user->can('cnt/diario/index')
                ],
                [
                    'label' => 'Departamento',
                    'url' => ['/hmr/departamento/index'],
                    // 'active' => Yii::$app->AuthService->isItemActive('cnt/diario-numero'), 'visible' => Yii::$app->user->can('cnt/diario-numero/index')
                ],
                [
                    'label' => 'Desconto',
                    'url' => ['/hmr/desconto/index'],
                    // 'active' => Yii::$app->AuthService->isItemActive('cnt/plano-conta'), 'visible' => Yii::$app->user->can('cnt/plano-conta/index')
                ],
                [
                    'label' => 'Gratificação',
                    'url' => ['/hmr/gratificacao/index'],
                    // 'active' => Yii::$app->AuthService->isItemActive('cnt/plano-terceiro'), 'visible' => Yii::$app->user->can('cnt/plano-terceiro/index')
                ],
                ['label' => 'Plano de IVA', 'url' => ['/cnt/plano-iva/index'], 'active' => Yii::$app->AuthService->isItemActive('cnt/plano-iva'), 'visible' => Yii::$app->user->can('cnt/plano-iva/index')],
                ['label' => 'Plano de Fluxo de Caixa', 'url' => ['/cnt/plano-fluxo-caixa/index'], 'active' => Yii::$app->AuthService->isItemActive('cnt/plano-fluxo-caixa'), 'visible' => Yii::$app->user->can('cnt/plano-fluxo-caixa/index')],
                ['label' => 'Tipologia', 'url' => ['/cnt/plano_conta_abertura/index'], 'active' => Yii::$app->AuthService->isItemActive('cnt/plano_conta_abertura'), 'visible' => Yii::$app->user->can('cnt/tipologia/index')],


            ]
        ],

        [
            'label' => 'Dashboard',
            'icon' => 'equalizer',
            'url' => ['/cnt/dashboard/index'], 'active' => Yii::$app->AuthService->isItemActive('cnt/dashboard'),
            'visible' => Yii::$app->user->can('cnt/dashboard/index')
        ],
        [
            'label' => 'Salário',
            'icon' => 'equalizer',
            'url' => ['/hmr/salario/index'],
            'active' => Yii::$app->AuthService->isItemActive('cnt/dashboard'), 'visible' => Yii::$app->user->can('cnt/dashboard/index')
        ],
        [
            'label' => 'Ferias',
            'icon' => 'equalizer',
            'url' => ['/hmr/vacation/index'],
            // 'active' => Yii::$app->AuthService->isItemActive('cnt/dashboard'), 'visible' => Yii::$app->user->can('cnt/dashboard/index')
        ],


        //Relatórios cnt 
        [
            'label' => 'Modelos Fiscais', 'icon' => 'print', 'items' => [
                [
                    'label' => 'Mod 106',
                    'url' => ['/cnt/modelo106/index'],
                    'active' => Yii::$app->AuthService->isItemActive('cnt/modelo106'), 'visible' => Yii::$app->user->can('cnt/modelo106/index')
                ],
            ]
        ],
    ]



]);
