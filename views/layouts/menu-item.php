<?php 
use kartik\sidenav\SideNav;
use app\modules\dsp\models\PedidoLevantamento;
$cntError = Yii::$app->CntQuery->getLancamentoErrorCount();
$dspWork = \app\modules\dsp\services\DspService::CountWorkflowStatus(1, Yii::$app->user->identity->id);

echo SideNav::widget([
  'type' => SideNav::TYPE_DEFAULT,
  'encodeLabels' => false,
  'items' => [
               ['label' => 'Home','url' =>['/site/index'],'icon' => 'home'],

               //Operacional
                [
                  'label' => '<span class="pull-right badge badge-error"> ' . $dspWork . ' </span> Operacional', 
                  'icon' => 'th-large', 
                  'items' => [
                          ['label' => 'Tabela', 'icon' => 'bookmark', 'items' => [
                            ['label' => 'Cliente / Fornecedor','url' => ['/dsp/person/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/person'),'visible' =>Yii::$app->user->can('dsp/person/index') ],  
                            ['label' => 'Itens','url' => ['/dsp/item/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/item'),'visible' =>Yii::$app->user->can('dsp/item/index')  ],   
                            ['label' => 'Regime','url' => ['/dsp/regime/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/regime') ,'visible' =>Yii::$app->user->can('dsp/regime/index') ],
                            ['label' => 'Unidade','url' => ['/dsp/unidade/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/unidade'),'visible' =>Yii::$app->user->can('dsp/unidade/index') ], 
                            ['label' => 'Documento Despacho','url' => ['/dsp/despacho-documento/index'] ,'active'=>Yii::$app->AuthService->isItemActive('dsp/despacho-documento'),'visible' =>Yii::$app->user->can('dsp/despacho-documento/index')], 
                            ['label' => 'Tarefas','url' => ['/dsp/tarefa/index'] ,'active'=>Yii::$app->AuthService->isItemActive('dsp/tarefa'),'visible' =>Yii::$app->user->can('dsp/tarefa/index')], 
                            ['label' => 'Origem','url' => ['/dsp/origem/index'] ,'active'=>Yii::$app->AuthService->isItemActive('dsp/origem'),'visible' =>Yii::$app->user->can('dsp/origem/index') ], 
                            ['label' => 'Setor','url' =>['/dsp/setor/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/setor'),'visible' =>Yii::$app->user->can('dsp/setor/index')],               

                            
                          ]
                      ],
                      ['label' => 'Processo','url' => ['/dsp/processo/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/processo'),'visible' =>Yii::$app->user->can('dsp/processo/index') ],
                      ['label' => '<span class="pull-right badge badge-error"> '.$dspWork.' </span> Workflow','url' => ['/dsp/processo-workflow/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/processo-workflow'),'visible' =>Yii::$app->user->can('dsp/processo-workflow/index') ],
                      ['label' => 'Disponível no Setor','url' => ['/dsp/processo-workflow/processo-setor'],'active'=>Yii::$app->AuthService->isItemActive('dsp/processo-workflow'),'visible' =>Yii::$app->user->can('dsp/processo-workflow/index') ],
                      ['label' => 'Arquivo','url' => ['/dsp/processo-workflow/arquivo'],'active'=>Yii::$app->AuthService->isItemActive('dsp/processo-workflow'),'visible' =>Yii::$app->user->can('dsp/processo-workflow/arquivo') ],
                      ['label' => 'NORD','url' => ['/dsp/nord/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/nord'),'visible' =>Yii::$app->user->can('dsp/nord/index')  ],  
                      ['label' => 'P. Levantamento','url' => ['/dsp/pedido-levantamento/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/pedido-levantamento'),'visible' =>Yii::$app->user->can('dsp/pedido-levantamento/index')  ],  
                      ['label' => 'Despachos','url' => ['/dsp/despacho/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/despacho'),'visible' =>Yii::$app->user->can('dsp/despacho/index')  ],

                      

                    ]
                ],
                








                //Financeiro
                ['label' => 'Financeiro', 'icon' => 'usd', 'items' => [
                  ['label' => 'Tabela', 'icon' => 'bookmark', 'items' => [
                        ['label' => 'Meio Financeiro', 'url' => ['/fin/banco/index' ],'active'=>Yii::$app->AuthService->isItemActive('fin/banco'),'visible' =>Yii::$app->user->can('fin/banco/index')],
                        ['label' => 'Conta Bancária', 'url' => ['/fin/banco-conta/index' ],'active'=>Yii::$app->AuthService->isItemActive('fin/banco-conta'),'visible' =>Yii::$app->user->can('fin/banco-conta/index')],
                        ['label' => 'Moeda','url' => ['/fin/currency/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/currency'),'visible' =>Yii::$app->user->can('fin/currency/index') ], 
                      ]
                  ],
                  ['label' => 'Dashboard','url' => ['/fin/dashboard/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/dashboard'),'visible' =>Yii::$app->user->can('fin/dashboard/index') ],



                
                
                    //Faturação
                ['label' => 'Faturação', 'icon' => 'tasks', 'items' => [
                    ['label' => 'Fatura Provisória','url' => ['/fin/fatura-provisoria/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/fatura-provisoria'),'visible' =>Yii::$app->user->can('fin/fatura-provisoria/index') ],
                    ['label' => 'Fatura Definitiva','url' => ['/fin/fatura-definitiva/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/fatura-definitiva'),'visible' =>Yii::$app->user->can('fin/fatura-definitiva/index') ],
                    ['label' => 'Aviso de Credito','url' => ['/fin/nota-credito/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/nota-credito'),'visible' =>Yii::$app->user->can('fin/nota-credito/index') ],
                    ['label' => 'Nota de Credito','url' => ['/fin/aviso-credito/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/aviso-credito'),'visible' =>Yii::$app->user->can('fin/aviso-credito/index') ],
                    ['label' => 'Nota de Debito','url' => ['/fin/nota-debito/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/nota-debito'),'visible' =>Yii::$app->user->can('fin/nota-debito/index') ],

                ]],

                 ['label' => 'Despesas','url' => ['/fin/despesa/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/despesa'),'visible' =>Yii::$app->user->can('fin/despesa/index') ], 
                 ['label' => 'Despesas & FP','url' => ['/fin/despesa/grelha'],'active'=>Yii::$app->AuthService->isItemActive('fin/despesa'),'visible' =>Yii::$app->user->can('fin/despesa/grelha') ], 





                ['label' => 'Tesouraria', 'icon' => 'tasks', 'items' => [
                        ['label' => 'Recebimentos', 'url' => ['/fin/recebimento/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/recebimento'),'visible' =>Yii::$app->user->can('fin/recebimento/index')],
                        ['label' => 'Pagamentos', 'url' => ['/fin/pagamento/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/pagamento'),'visible' =>Yii::$app->user->can('fin/pagamento/index')],
                         ['label' => 'Encontro de Contas', 'url' => ['/fin/of-accounts/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/of-accounts'),'visible' =>Yii::$app->user->can('fin/of-accounts/index')],
                        ['label' => 'Transferencia', 'url' => ['/fin/transferencia/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/transferencia'),'visible' =>Yii::$app->user->can('fin/transferencia/index')],
                        ['label' => 'Diário de Tesouraria', 'url' => ['/fin/caixa/index'],'active'=>Yii::$app->AuthService->isItemActive('fin/caixa'),'visible' =>Yii::$app->user->can('fin/transferencia/index')],
                    ]],

                


                ]],




                //contabilidade    
                ['label' => '<span class="pull-right badge badge-error"> '.$cntError.' </span> Contabilidade', 
                  'icon' => 'copyright-mark', 
                  'items' => [
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
                        ['label' => 'Dashboard','url' => ['/cnt/dashboard/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/dashboard'),'visible' =>Yii::$app->user->can('cnt/dashboard/index') ],


                      ['label' => 'Livro Diário', 'url' => ['/cnt/razao/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/razao'),'visible' =>Yii::$app->user->can('cnt/razao/index')],
                       ['label' => '<span class="pull-right badge badge-error"> '.$cntError.' </span> Lançamento Erado', 'url' => ['/cnt/razao/lancamento-error'],'visible' =>Yii::$app->user->can('cnt/razao/lancamento-error')],

                      ['label' => 'Extrato', 'icon' => 'tasks', 'items' => [
                            ['label' => 'Conta', 'url' => ['/cnt/extrato/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/extrato'),'visible' =>Yii::$app->user->can('cnt/extrato/index')],
                            ['label' => 'Fluxo de Caixa', 'url' => ['/cnt/extrato/index-fluxo-caixa'],'active'=>Yii::$app->AuthService->isItemActive('cnt/extrato'),'visible' =>Yii::$app->user->can('cnt/extrato/index-fluxo-caixa')],
                          ]
                      ],
                      // balancete
                      ['label' => 'Balancete', 'icon' => 'tasks', 'items' => [
                          ['label' => 'Conta', 'url' => ['/cnt/balancete/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/balancete'),'visible' =>Yii::$app->user->can('cnt/balancete/index')],
                          ['label' => 'Fluxo de Caixa', 'url' => ['/cnt/balancete/index-fluxo-caixa'],'active'=>Yii::$app->AuthService->isItemActive('cnt/balancete'),'visible' =>Yii::$app->user->can('cnt/balancete/index-fluxo-caixa')],
                        ]
                     ],
                            //Relatórios cnt 
                      ['label' => 'Modelos Fiscais', 'icon' => 'tasks', 'items' => [
                          ['label' => 'Mod 106', 'url' => ['/cnt/modelo106/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/modelo106'),'visible' =>Yii::$app->user->can('cnt/modelo106/index')],
                        ]
                     ],
                    ]
                ],

                







                
                // Relactorios
                ['label' => 'Relatórios', 'icon' => 'stats', 
                  'items' => [

                    ['label' => 'Processo Geral', 'url' => ['/dsp/report/index'],'active'=>Yii::$app->AuthService->isItemActive('dsp/report'),'visible' =>Yii::$app->user->can('dsp/report/index')],
                    ['label' => 'Processo Liquidado', 'url' => ['/dsp/report/pr-liquidado'],'active'=>Yii::$app->AuthService->isItemActive('dsp/report'),'visible' =>Yii::$app->user->can('dsp/report/index')],
                    ['label' => 'Processo Registado','url' => ['/dsp/report/pr-registrado'],'active'=>Yii::$app->AuthService->isItemActive('dsp/report'),'visible' =>Yii::$app->user->can('dsp/report/index') ],
                    ['label' => 'PL Por Resolver','url' => ['/dsp/report/pr-pl-por-resolver'],'active'=>Yii::$app->AuthService->isItemActive('dsp/report'),'visible' =>Yii::$app->user->can('dsp/report/index') ],
                    ['label' => 'Workflow','url' => ['/dsp/processo-workflow/report'],'active'=>Yii::$app->AuthService->isItemActive('dsp/report'),'visible' =>Yii::$app->user->can('dsp/processo-workflow/report') ],
                        
                    
                    ['label' => 'Posição de Tesouraria', 'url' => ['/fin/banco-conta/posicao-tesouraria'],'active'=>Yii::$app->AuthService->isItemActive('cnt/banco-conta'),'visible' =>Yii::$app->user->can('fin/banco-conta/posicao-tesouraria')],
                    ['label' => 'Extrato Bancário', 'url' => ['/fin/caixa-transacao/index'],'active'=>Yii::$app->AuthService->isItemActive('cnt/caixa-transacao'),'visible' =>Yii::$app->user->can('fin/caixa-transacao/index')],
                    ['label' => 'Despesa','url' => ['/fin/despesa/relatorio'],'active'=>Yii::$app->AuthService->isItemActive('cnt/despesa'),'visible' =>Yii::$app->user->can('fin/despesa/relatorio') ],
                    ['label' => 'Fatura Provisória','url' => ['/fin/report/fatura-provisoria'],'active'=>Yii::$app->AuthService->isItemActive('fin/report'),'visible' =>Yii::$app->user->can('fin/report/fatura-provisoria') ],

                    ['label' => 'Valor a Favor de Cliente','url' => ['/fin/report/valor-afavor'],'active'=>Yii::$app->AuthService->isItemActive('fin/report'),'visible' =>Yii::$app->user->can('fin/report/valor-afavor') ],
                    ['label' => 'Valor a Dever de Cliente','url' => ['/fin/report/valor-adever'],'active'=>Yii::$app->AuthService->isItemActive('fin/report'),'visible' =>Yii::$app->user->can('fin/report/valor-adever') ],
                    ['label' => 'Recebimento','url' => ['/fin/report/recebimento'],'active'=>Yii::$app->AuthService->isItemActive('fin/report'),'visible' =>Yii::$app->user->can('fin/report/recebimento') ],
                    ['label' => 'Honorário','url' => ['/fin/fatura-provisoria/honorario'],'active'=>Yii::$app->AuthService->isItemActive('fin/fatura-provisoria'),'visible' =>Yii::$app->user->can('fin/fatura-provisoria/honorario') ],

                 ]
              ], 





                



                
  
                



                // Administração
                ['label' => 'Administração', 'icon' => 'cog', 'items' => [
                        ['label' => 'Utilizadores','url' => ['/user/index'],'active'=>Yii::$app->AuthService->isItemActive('app/user'),'visible' =>Yii::$app->user->can('app/user/index')],
                        
                        ['label' => 'Documento','url' =>['/documento/index'],'active'=>Yii::$app->AuthService->isItemActive('app/documento'),'visible' =>Yii::$app->user->can('app/documento/index')],

                        ['label' => 'Numero Documento','url' => ['/documento-numero/index'],'active'=>Yii::$app->AuthService->isItemActive('app/documento-numero'),'visible' =>Yii::$app->user->can('app/documento-numero/index')],
                        ['label' => 'Grupos', 'url' =>  ['/auth-item/grupo'],'active'=>Yii::$app->AuthService->isItemActive('app/auth-item'),'visible' =>Yii::$app->user->can('app/auth-item/grupo')], 
                        ['label' => 'Permissões','url' =>  ['/auth-item/index'],'active'=>Yii::$app->AuthService->isItemActive('app/auth-item'),'visible' =>Yii::$app->user->can('app/auth-item/index')],                        
                        
                        ['label' => 'Tabela de Parametro', 'url' =>  ['/parameter/index'],'active'=>Yii::$app->AuthService->isItemActive('app/parameter'),'visible' =>Yii::$app->user->can('app/parameter/index')], 
                ]],  
     

                       
                         
                    ],
             
  
]);
?>