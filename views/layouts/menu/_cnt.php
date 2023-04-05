<?php 
use kartik\sidenav\SideNav;
use app\modules\dsp\models\PedidoLevantamento;
$cntError = Yii::$app->CntQuery->getLancamentoErrorCount();

echo SideNav::widget([
  'type' => SideNav::TYPE_DEFAULT,
  'encodeLabels' => false,
  'items' => [
               ['label' => 'Home','url' =>['/site/index'],'icon' => 'home'],

 ['label' => 'Tabela', 
                          'icon' => 'bookmark', 
                          'items' => [
                              ['label' => 'Documento', 'url' => ['/cnt/documento/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/documento'),'visible' =>Yii::$app->user->can('cnt/documento/index')],
                              ['label' => 'Pasta Diário','url' => ['/cnt/diario/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/diario'),'visible' =>Yii::$app->user->can('cnt/diario/index') ],
                              ['label' => 'Numero Diário','url' => ['/cnt/diario-numero/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/diario-numero'),'visible' =>Yii::$app->user->can('cnt/diario-numero/index') ],
                              ['label' => 'Plano de Conta','url' => ['/cnt/plano-conta/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/plano-conta'),'visible' =>Yii::$app->user->can('cnt/plano-conta/index') ],
                              ['label' => 'Plano de Terceiro','url' => ['/cnt/plano-terceiro/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/plano-terceiro'),'visible' =>Yii::$app->user->can('cnt/plano-terceiro/index') ],
                              ['label' => 'Plano de IVA','url' => ['/cnt/plano-iva/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/plano-iva'),'visible' =>Yii::$app->user->can('cnt/plano-iva/index') ],
                              ['label' => 'Plano de Fluxo de Caixa','url' => ['/cnt/plano-fluxo-caixa/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/plano-fluxo-caixa'),'visible' =>Yii::$app->user->can('cnt/plano-fluxo-caixa/index') ],
                              ['label' => 'Tipologia','url' => ['/cnt/plano_conta_abertura/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/plano_conta_abertura'),'visible' =>Yii::$app->user->can('cnt/tipologia/index') ],
                              

                            ]
                        ],
                        ['label' => 'Fecho & Abertura', 'icon' => 'cog',
                        'items' => [
                                ['label' => 'Configuração','url' => ['/cnt/lancamento/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/lancamento'),'visible' =>Yii::$app->user->can('cnt/tipologia/index') ],
                                ['label' => 'Lançamento','url' => ['/cnt/lancamento/razao-lancamento'],'active'=>Yii::$app->AuthService->isItemActive('cnt/lancamento'),'visible' =>Yii::$app->user->can('cnt/lancamento/razao-lancamento') ],

                        ]
                    ],
                        ['label' => 'Dashboard','icon'=>'equalizer','url' => ['/cnt/dashboard/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/dashboard'),'visible' =>Yii::$app->user->can('cnt/dashboard/index') ],


                      ['label' => 'Livro Diário','icon'=>'refresh', 'url' => ['/cnt/razao/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/razao'),'visible' =>Yii::$app->user->can('cnt/razao/index')],
                       ['label' => '<span class="pull-right badge badge-error"> '.$cntError.' </span> Lançamento Erado','icon'=>'remove', 'url' => ['/cnt/razao/lancamento-error'],'visible' =>Yii::$app->user->can('cnt/razao/lancamento-error')],

                      ['label' => 'Extrato', 'icon' => 'print', 'items' => [
                            ['label' => 'Conta', 'url' => ['/cnt/extrato/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/extrato'),'visible' =>Yii::$app->user->can('cnt/extrato/index')],
                            ['label' => 'Fluxo de Caixa', 'url' => ['/cnt/extrato/index-fluxo-caixa'],'active'=>Yii::$app->AuthService->isItemActive('cnt/extrato'),'visible' =>Yii::$app->user->can('cnt/extrato/index-fluxo-caixa')],
                          ]
                      ],
                      // balancete
                      ['label' => 'Balancete', 'icon' => 'print', 'items' => [
                          ['label' => 'Conta', 'url' => ['/cnt/balancete/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/balancete'),'visible' =>Yii::$app->user->can('cnt/balancete/index')],
                          ['label' => 'Fluxo de Caixa', 'url' => ['/cnt/balancete/index-fluxo-caixa'],'active'=>Yii::$app->AuthService->isItemActive('cnt/balancete'),'visible' =>Yii::$app->user->can('cnt/balancete/index-fluxo-caixa')],
                        ]
                     ],
                            //Relatórios cnt 
                      ['label' => 'Modelos Fiscais', 'icon' => 'print', 'items' => [
                          ['label' => 'Mod 106', 'url' => ['/cnt/modelo106/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/modelo106'),'visible' =>Yii::$app->user->can('cnt/modelo106/index')],
                        ]
                     ],
                    ]
                
             
  
]);
?>