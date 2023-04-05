<?php

namespace app\components\notifications\channels;

use Yii;
use app\components\notifications\Channel;
use app\components\services\NotificationService;
use app\models\Notification;

class ScreenChannel extends Channel
{
    public function send(NotificationService $notification)
    {
        $className = $notification->className();
        $model = new Notification();
        $model->service =  $notification->getService();
        $model->class = $className;
        $model->key =  $notification->key;
        $model->message = (string) $notification->getTitle();
        $model->route = serialize($notification->getRoute());
        $model->user_id = $notification->userId;
        $model->save();
    }
}
