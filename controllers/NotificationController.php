<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use yii\data\Pagination;
use yii\helpers\Url;
use app\components\helpers\TimeElapsedHelper;
use app\components\widgets\NotificationWidget;

class NotificationController extends Controller
{
    /**
     * Displays index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->getUser()->getId();
        $query = (new Query())
            ->from('bas_notification')
            ->andWhere(['or', 'user_id = 0', 'user_id = :user_id'], [':user_id' => $userId]);

        $pagination = new Pagination([
            'pageSize' => 20,
            'totalCount' => $query->count(),
        ]);

        $list = $query
            ->orderBy(['id' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $notifs = $this->prepareNotifications($list);

        return $this->render('index', [
            'notifications' => $notifs,
            'pagination' => $pagination,
        ]);
    }

    /**
     *
     * @return 
     */
    public function actionList()
    {
        $userId = Yii::$app->getUser()->getId();
        $list = (new Query())
            ->from('bas_notification')
            ->andWhere(['or', 'user_id = 0', 'user_id = :user_id'], [':user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->limit(10)
            ->all();
        $notifs = $this->prepareNotifications($list);
        $this->ajaxResponse(['list' => $notifs]);
    }


    /**     
     * @return 
     */
    public function actionCount()
    {
        $count = NotificationWidget::getCountUnseen();
        $this->ajaxResponse(['count' => $count]);
    }


    /**     
     * @return 
     */
    public function actionRead($id)
    {
        Yii::$app->getDb()->createCommand()->update('bas_notification', ['read' => true], ['id' => $id])->execute();
        if (Yii::$app->getRequest()->getIsAjax()) {
            return $this->ajaxResponse(1);
        }
        return Yii::$app->getResponse()->redirect(['index']);
    }

    /**     
     * @return 
     */
    public function actionReadAll()
    {
        Yii::$app->getDb()->createCommand()->update('bas_notification', ['read' => true])->execute();
        if (Yii::$app->getRequest()->getIsAjax()) {
            return $this->ajaxResponse(1);
        }
        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'All notifications have been marked as read.'));
        return Yii::$app->getResponse()->redirect(['index']);
    }


    /**     
     * @return 
     */
    public function actionDeleteAll()
    {
        Yii::$app->getDb()->createCommand()->delete('bas_notification')->execute();

        if (Yii::$app->getRequest()->getIsAjax()) {
            return $this->ajaxResponse(1);
        }

        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'All notifications have been deleted.'));

        return Yii::$app->getResponse()->redirect(['index']);
    }

    /**     
     * @return 
     */
    private function prepareNotifications($list)
    {
        $notifs = [];
        $seen = [];
        foreach ($list as $notif) {
            if (!$notif['seen']) {
                $seen[] = $notif['id'];
            }
            $route = @unserialize($notif['route']);
            $notif['url'] = !empty($route) ? Url::to($route) : '';
            $notif['timeago'] = TimeElapsedHelper::timeElapsed($notif['created_at']);
            $notifs[] = $notif;
        }
        if (!empty($seen)) {
            Yii::$app->getDb()->createCommand()->update('bas_notification', ['seen' => true], ['id' => $seen])->execute();
        }
        return $notifs;
    }


    /**     
     * @return 
     */
    public function ajaxResponse($data = [])
    {
        if (is_string($data)) {
            $data = ['html' => $data];
        }

        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes(true);
        foreach ($flashes as $type => $message) {
            $data['bas_notification'][] = [
                'type' => $type,
                'message' => $message,
            ];
        }
        return $this->asJson($data);
    }
}
