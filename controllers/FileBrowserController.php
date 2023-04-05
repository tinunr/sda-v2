<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Ficheiro;
use yii\web\UploadedFile;
use yii\helpers\Url;

class FileBrowserController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'list-data','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        // 'matchCallback' => function ($rule, $action) {
                        //     return Yii::$app->AuthService->permissiomHandler() === true;
                        // }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Lists all Documento models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'fileBrowser';
        return $this->render('index');
    }

    /**
     * Lists all Documento models.
     * @return mixed
     */
    public function actionListData($dir = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (empty($dir)) {
            $dir = "data";
        }
        $files = $this->scanData($dir);

        return [
            'name' => 'data',
            'type' => 'folder',
            'path' => $dir,
            'items' => $files
        ];
    }


    public function actionDelete(string $path)
    {
        if (file_exists($path)) {
            $deleted = unlink($path);
            if ($deleted) {
                Yii::$app->getSession()->setFlash('success', 'O ficheiro foi eliminado com sucesso');
            } else {
                Yii::$app->getSession()->setFlash('error', 'Ocoreu um erro!');
            }
        } else {
            Yii::$app->getSession()->setFlash('error', 'O ficheiro especificado não existe');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Lists all Documento models.
     * @return mixed
     */
    protected function scanData($dir)
    {
        $files = array();

        // Is there actually such a folder/file?

        if (file_exists($dir)) {

            foreach (scandir($dir) as $f) {

                if (!$f || $f[0] == '.') {
                    continue; // Ignore hidden files
                }

                if (is_dir($dir . '/' . $f)) {

                    // The path is a folder

                    $files[] = array(
                        "name" => $f,
                        "type" => "folder",
                        "path" => $dir . '/' . $f,
                        "items" => $this->scanData($dir . '/' . $f), // Recursively get the contents of the folder
                        "last_modified" => date ("d/m/Y H:i:s",filemtime($dir . '/' . $f)), // Gets last  modified date file

                    );
                } else {

                    // It is a file

                    $files[] = array(
                        "name" => $f,
                        "type" => "file",
                        "path" => $dir . '/' . $f,
                        "size" => filesize($dir . '/' . $f), // Gets the size of this file
                        "last_modified" => date ("d/m/Y H:i:s",filemtime($dir . '/' . $f)), // Gets last  modified date file
                    );
                }
            }
        }

        return $files;
    }
}