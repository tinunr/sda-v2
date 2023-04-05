<?php

namespace app\components\notifications;

use app\components\services\NotificationService;


abstract class Channel extends \yii\base\BaseObject
{

    public $id;

    public function __construct($id, $config = [])
    {
        $this->id = $id;
        parent::__construct($config);
    }

    public abstract function send(NotificationService $notification);
}
