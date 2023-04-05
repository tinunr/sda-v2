<?php 

    namespace app\models;

    
    use Yii;
    #use yii\base\Model;
    #use app\models\Login;
    use app\models\User;
    
    class PasswordForm extends \yii\db\ActiveRecord
    {
        public $oldpass;
        public $newpass;
        public $repeatnewpass;
        
        public function rules()
        {
            return [
                [['newpass','repeatnewpass'],'required'],
                [['newpass','repeatnewpass'],'string', 'min' => 6],
                ['oldpass','findPasswords'],
                ['repeatnewpass','compare','compareAttribute'=>'newpass'],
            ];
        }
        
        public function findPasswords($attribute, $params)
        {
            $user = User::find()
                        ->where(['username'=>Yii::$app->user->identity->username])
                        ->one();

                       

            $password = $user->newpass;
            #$oldpass = Yii::$app->security->generatePasswordHash($this->oldpass);
            #!Yii::$app->getSecurity()->validatePassword($password, $user->password_hash)

            #print_r($this->newpass.'----'.$oldpass); 

            if(!Yii::$app->getSecurity()->validatePassword($password, $user->password_hash))
            {
                $this->addError($attribute,'Palavra Chave Invalido');
            }

                
        }
        
        public function attributeLabels()
        {
            return [
                'oldpass'=>'Palavra Chave Existente',
                'newpass'=>'Nova Palavra Chave',
                'repeatnewpass'=>'Confirme Palavra Chave',
            ];
        }
    }