<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = $model->name.' / '.$model->username;
?>
<section>
<div class="user-view">

<p style="margin: 10px 0px; text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-user"></i> <?=$model->name.' / '.$model->username?> </strong></p>
     
    
    <div class="row">

            <p>
                <?= Html::a('<i class="fa fa-chevron-left"></i> Voltar', ['index'], ['class'=>'btn btn-warning']) ?>
                <?= Html::a('<i class="fa fa-pencil"></i> Atualizar', ['update', 'id' => $model->id], ['class'=>'btn btn-primary']) ?>
                <?= Html::a('<i class="fa  fa-unlock"></i> Alterar palavra chave', ['reset-password', 'id' => $model->id], ['class'=>'btn btn-primary']) ?> 
               
               

                <?php if(Yii::$app->user->can('app/user/delete')):?>
                <?= Html::a('<i class="fa  fa-trash"></i> Eliminar', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-dangers',
                    'data' => [
                        'confirm' => 'Pretende realmente eleminar este item?',
                        'method' => 'post',
                    ],
                ]) ?> 
                <?php endif;?>
            </p>


         <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    //'id',
                   'username:email',
                    'name',
                    'status',
                    
                ],
            ]) ?> 

        
        
    </div>
   

    


 </div>


<?php if(Yii::$app->user->can('app/auth-assignment/create')&&$model->status==10):?>

 <div class="auth-assignment-index">



<p style="margin: 10px 0px; text-align: left; padding: 10px; border-left: 2px solid #ab162b; background: #f8f8f8;"><strong><i class="fa fa-user"></i> ACESSO </strong></p>

        <div class="row">
    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-10">
        <?= $form->field($modelAuthAssignment, 'item_name')->widget(Select2::classname(), [
          'data' =>ArrayHelper::map(AuthItem::find()->where(['type' => 1])->orderBy('name')->all(), 'name','name','type'),
          'options' => ['placeholder' => 'Selecione item name ...'],
          'pluginOptions' => [
              'allowClear' => true
          ],
      ])->label(false);

      ?>
      </div>

    <div class="col-md-2">
        <?= Html::submitButton($modelAuthAssignment->isNewRecord ? 'Adicinar': 'Atualizar', ['class' => $modelAuthAssignment->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
    

</div>

<?php endif;?>

<?= GridView::widget([
        'dataProvider' => $dataProviderAuthAssignment,
        #'filterModel' => $searchModel,

        'columns' => [
            
             [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:20px;'],
                'header'=>'Ações',
                'template' => '{delete}',
                 'buttons' => [
                        //view button
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="fa fa-trash"></i>  Eliminar', $url, [
                                        'title' => 'Delete',
                                        'class'=>'btn  btn-xs',
                            ]);
                        },
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'delete') {
                            $url = Url::to(['/user/delete-auth-assignment', 'item_name' => $model->item_name,'user_id' => $model->user_id], [
                                    'data' => [
                                        'confirm' => 'Pretende realmente remover este registo?',
                                        'method' => 'post',
                                    ],
                                ]);
                            return $url;
                        }
                  }

                ],            
            #'user_id',
            #'user.nome_completo',
            'item_name',
            'created_at',

            
        ],
    ]); ?>


</div>
</section>












