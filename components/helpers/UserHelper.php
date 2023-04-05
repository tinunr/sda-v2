<?php
namespace app\components\helpers;
use yii;
use yii\base\Component;
use app\models\User;
/**
* 
*/
class UserHelper extends Component
{      
    public function getUserName(int $user_id)
    {   if(($model = User::findOne($user_id)) != NULL){
            return $model->name;
        }
        return ' ' ;
    }


     
}
?>



