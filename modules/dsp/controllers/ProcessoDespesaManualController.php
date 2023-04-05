<?php

namespace app\modules\dsp\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Model;
use yii\helpers\ArrayHelper;
use app\modules\dsp\models\ProcessoDespesaManual;
use app\modules\dsp\models\ProcessoTce;
use app\modules\dsp\models\PedidoLevantamento;

/**
 * ItemController implements the CRUD actions for ProcessoDespesaManual model.
 */
class ProcessoDespesaManualController extends Controller
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
     * Creates a new ProcessoDespesaManual model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($dsp_processo_id)
    {
        $model = \app\modules\dsp\models\Processo::findOne($dsp_processo_id);
        $model->processo_tce = ArrayHelper::map(
            \app\modules\dsp\models\ProcessoTce::find()->where(['dsp_processo_id' => $dsp_processo_id])->all(),
            'tce',
            'tce'
        );
        $flag = TRUE;
        $modelsProcessoDespesaManual = $model->processoDespesaManual;
        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($modelsProcessoDespesaManual, 'id', 'id');
            $modelsProcessoDespesaManual = Model::createMultiple(ProcessoDespesaManual::classname());
            Model::loadMultiple($modelsProcessoDespesaManual, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsProcessoDespesaManual, 'id', 'id')));
            $transaction = \Yii::$app->db->beginTransaction();

            if (!$model->save(FALSE)) {
                $transaction->rollBack();
            }


            ProcessoTce::deleteAll(['dsp_processo_id' => $dsp_processo_id]);
            if (!empty($model->processo_tce)) {
                foreach ($model->processo_tce as $key => $tce) {
                    $new = new ProcessoTce();
                    $new->dsp_processo_id = $dsp_processo_id;
                    $new->tce = $tce;
                    if (!($flag = $new->save())) {
                        $transaction->rollBack();
                        break;
                    }
                }
            }



            if (($pl = PedidoLevantamento::find()->where(['id' => $model->n_levantamento, 'bas_ano_id' => $model->n_levantamento_ano_id, 'dsp_desembaraco_id' => $model->n_levantamento_desembarco_id])->one()) != NULL) {
                $pl->data_proragacao = $model->pl_data_prorogacao;
                $pl->save();
            }

            if (!empty($deletedIDs)) {
                ProcessoDespesaManual::deleteAll(['id' => $deletedIDs]);
            }
            if (!empty($modelsProcessoDespesaManual)) {
                foreach ($modelsProcessoDespesaManual as $item) {
                    $item->dsp_processo_id = $dsp_processo_id;
                    if (!($flag = $item->save())) {
                        $transaction->rollBack();
                        break;
                    }
                }
            }
            if ($flag) {
                $transaction->commit();
                return $this->redirect(['/dsp/processo/view', 'id' => $dsp_processo_id]);
            }
        }
        return $this->render('create', [
            'modelsProcessoDespesaManual' => (empty($modelsProcessoDespesaManual)) ? [new ProcessoDespesaManual] : $modelsProcessoDespesaManual,
            'model' => $model,

        ]);
    }






    /**
     * Deletes an existing ProcessoDespesaManual model.
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
     * Finds the ProcessoDespesaManual model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProcessoDespesaManual the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProcessoDespesaManual::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
