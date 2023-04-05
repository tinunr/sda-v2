<?php 
use kartik\sidenav\SideNav;
use app\modules\dsp\models\PedidoLevantamento;
$cntError = Yii::$app->CntQuery->getLancamentoErrorCount();

echo SideNav::widget([
  'type' => SideNav::TYPE_DEFAULT,
  'encodeLabels' => false,
  'items' => [
      ['label' => 'Home','icon' => 'home','url' =>['/site/index']],

      [
        'label' => 'Utilizadores',
        'icon' => 'user','url' => ['/user/index'],
        'active'=>Yii::$app->AuthService->isItemActive('app/user'),
        'visible' =>Yii::$app->user->can('app/user/index')
      ],
      [
        'label' => 'Documento',
        'icon' => 'folder-close',
        'url' => ['/documento/index'],
        'active'=>Yii::$app->AuthService->isItemActive('app/user'),
        'visible' =>Yii::$app->user->can('app/user/index')
      ],
      [
        'label' => 'Numero Documento',
        'icon' => 'sort-by-order-alt',
        'url' => ['/documento-numero/index'],
        'active'=>Yii::$app->AuthService->isItemActive('app/user'),
        'visible' =>Yii::$app->user->can('app/user/index')
      ],
      [
        'label' => 'Grupos',
        'icon' => 'list-alt', 
        'url' =>  ['/auth-item/grupo'],
        'active'=>Yii::$app->AuthService->isItemActive('app/auth-item'),
        'visible' =>Yii::$app->user->can('app/auth-item/grupo')
      ], 
      [
        'label' => 'Permissões',
        'icon' => 'ok',
        'url' =>  ['/auth-item/index'],
        'active'=>Yii::$app->AuthService->isItemActive('app/auth-item'),
        'visible' =>Yii::$app->user->can('app/auth-item/index')
      ],                       
      
      [
        'label' => 'Tabela de Parametro',
        'icon' => 'certificate', 
        'url' =>  ['/parameter/index'],
        'active'=>Yii::$app->AuthService->isItemActive('app/parameter'),
        'visible' =>Yii::$app->user->can('app/parameter/index')
      ], 
              
  ]
            
]);
?>