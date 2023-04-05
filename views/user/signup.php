<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = 'Novo utilizador';

?>
<section>
<div class="site-signup">

	
            <h4>  NOVO UTILIZADOR</h4>
            <div id="line"></div>
    
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

    

                <?= $form->field($model, 'name')->textInput(array('placeholder' => 'Nome Completo')) ?>

                <?= $form->field($model, 'username')->textInput(array('placeholder' => 'E-mail'))->label('Nome de Utilizador') ?> 

                <?= $form->field($model, 'password')->passwordInput(array('placeholder' => 'Palavra-chave'))->label('Palavra Chave')?>
                
   
             
                <?= Html::submitButton('Criar conta', ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
                    
                <?= Html::a('Cancelar', ['/user/index'], ['class'=>'btn btn-danger']) ?>
                    
               
            <?php ActiveForm::end(); ?>
  
</div>
</section>