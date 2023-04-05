<?php
namespace app\components\services;
use yii;
use yii\base\Component;
use yii\helpers\Html;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsonParser;
use app\models\PersonCard;

class AuthService extends Component
{   

     

    /**
     * Lists all User models.
     * @return mixed
     */
    public function permissiomHandler()
    {
        #print_r(Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);die();
        return  Yii::$app->user->can(Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/'.Yii::$app->controller->action->id)?true:false;
        // return true;
        
    }

     /**
     * Lists all User models.
     * @return mixed
     */
    public function isItemActive($controller)
    {
        return  Yii::$app->controller->module->id.'/'.Yii::$app->controller->id==$controller;
        
    }

     
}