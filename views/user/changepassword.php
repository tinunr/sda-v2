<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Change Password';
?>
<section>
<div class="site-changepassword">
<b></b>
    
    <?php $form = ActiveForm::begin(); 
    ?>
        <?php #echo  $form->field($model,'oldpass',['inputOptions'=>['placeholder'=>'Palavra Chave Existente']])->passwordInput(); ?>
        
        <?= $form->field($model,'newpass',['inputOptions'=>['placeholder'=>'Nova Palavra Chave']])->passwordInput() ?>
        
        <?= $form->field($model,'repeatnewpass',['inputOptions'=>['placeholder'=>'Confirme Palavra Chave']])->passwordInput() ?>
        
        <div class="form-group">
                <?= Html::submitButton('Alterar Palavra Chave',[
                    'class'=>'btn btn-primary'
                ]) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
</section>