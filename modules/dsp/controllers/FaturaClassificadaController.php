<?php

namespace app\modules\dsp\controllers;

use Yii;
use app\modules\dsp\models\FaturaClassificada;
use app\modules\dsp\models\ItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\ActiveQuery;
use app\models\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * ItemController implements the CRUD actions for FaturaClassificada model.
 */
class FaturaClassificadaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'create' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Creates a new FaturaClassificada model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($dsp_processo_id)
    {
        $model = \app\modules\dsp\models\Processo::findOne($dsp_processo_id);
        $modelsFaturaClassificada = $model->faturaClassificada;
        if (Yii::$app->request->post()) {
            $oldIDs = ArrayHelper::map($modelsFaturaClassificada, 'id', 'id');
            $modelsFaturaClassificada = Model::createMultiple(FaturaClassificada::classname());
            Model::loadMultiple($modelsFaturaClassificada, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsFaturaClassificada, 'id', 'id')));
            $transaction = \Yii::$app->db->beginTransaction();

            if (!empty($deletedIDs)) {
                FaturaClassificada::deleteAll(['id' => $deletedIDs]);
            }
            foreach ($modelsFaturaClassificada as $item) {
                $item->dsp_processo_id = $dsp_processo_id;
                if (!($flag = $item->save())) {
                    $transaction->rollBack();
                    break;
                }
            }
            if ($flag) {
                $transaction->commit();
                return $this->redirect(['/dsp/processo/view', 'id' => $dsp_processo_id]);
            }
        }
        return $this->render('create', [
            'modelsFaturaClassificada' => (empty($modelsFaturaClassificada)) ? [new FaturaClassificada] : $modelsFaturaClassificada,
            'model' => $model,

        ]);
    }






    /**
     * Deletes an existing FaturaClassificada model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            Yii::$app->getSession()->setFlash('success', 'Item removido com sucesso.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'ocoreu um erro ao efetuar a operção');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the FaturaClassificada model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FaturaClassificada the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FaturaClassificada::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
