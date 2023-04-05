<?php

namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use app\modules\fin\models\BancoConta;
use app\modules\fin\models\Caixa;
use app\modules\fin\models\CaixaSearch;
use app\modules\fin\models\CaixaTransacao;
use app\modules\fin\models\CaixaTransacaoSearch;
/**
 * EdeficiosController implements the CRUD actions for Caixa model.
 */
class CaixaController extends Controller
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
                        'actions' => ['index','view','create','update','close-caixa','open-caixa','update-saldo'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->AuthService->permissiomHandler() === true;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'open-caixa' => ['POST'],
                    'update-saldo' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Caixa models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CaixaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Caixa model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new CaixaTransacaoSearch(['fin_caixa_id'=>$id]);
        $dataProvider = $searchModel->searchCaixaView(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $model,
            'dataProvider'=>$dataProvider,
        ]);
    }

    /**
     * Creates a new Caixa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Caixa();
        $openCaixa = Caixa::find()->where(['status'=>Caixa::OPEN_CAIXA])->all();
        #print_r(caixaOpen);
        if (empty($openCaixa)) {
            $model->data = date('Y-m-d');
            $model->descricao = 'Caixa '.date('d-m-Y');
            $model->user_open_id = Yii::$app->user->identity->id;
            $model->status = Caixa::OPEN_CAIXA;

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
               //return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }else{
            Yii::$app->getSession()->setFlash('warning', 'Já existe uma caixa Aberta feche a primero para puder abrir novo');
            return $this->redirect(['index']);

        }
        
    }

    /**
     * Updates an existing Caixa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionOpenCaixa($id)
    {
        $model = $this->findModel($id);
        $caixaDepois = Caixa::find()
                    ->where(['fin_banco_conta_id'=>$model->fin_banco_conta_id])
                    ->andWhere(['>','id',$model->id])
                    ->all();
        if (!empty($caixaDepois)) {       
            foreach ($caixaDepois as $key => $caixa) {
               if ($caixa->status==Caixa::CLOSE_CAIXA) {
                Yii::$app->getSession()->setFlash('warning', 'É preciso abrir as outras caixa que estão fechados que foram criada depois.');
                return $this->redirect(Yii::$app->request->referrer);
               }
            }
         }


         foreach ($model->caixaTransacao as $key => $caixaTransacao) {
            $caixaTransacao->status  = $model->status = Caixa::OPEN_CAIXA;
            $caixaTransacao->save(false);
         }
         $model->status = Caixa::OPEN_CAIXA;
         $model->saldo_fecho = null;
         $model->data_fecho = null;
         $model->save(false);
        return $this->redirect(Yii::$app->request->referrer);
        
    }

    /**
     * Deletes an existing Caixa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCloseCaixa($id)
    {
        $model = $this->findModel($id);
        $bancoConta = BancoConta::findOne($model->fin_banco_conta_id);
         $caixaAntes = Caixa::find()
                    ->where(['fin_banco_conta_id'=>$model->fin_banco_conta_id])
                    ->andWhere(['<','id',$model->id])
                    ->orderBy('id desc')
                    ->one();
        if (!empty($caixaAntes)) {       
            if ($caixaAntes->status==Caixa::OPEN_CAIXA) {
                Yii::$app->getSession()->setFlash('warning', 'É preciso fechar a caixa '.$caixaAntes->descricao.' antes.');
                return $this->redirect(Yii::$app->request->referrer);
               }
         }
        $selection = (array)Yii::$app->request->post('selection');
        $caixaTransacaoNaoValidado = CaixaTransacao::find()
                                  ->where(['fin_caixa_id'=>$id])  
                                  ->andWhere(['status'=>1]) 
                                  ->asArray()
                                  ->all();
         $ids = ArrayHelper::getColumn($caixaTransacaoNaoValidado, 'id'); 
         if(ArrayHelper::isSubset($ids,$selection)){        
            $modelsCaixaTransacao = CaixaTransacao::findAll(['id'=>$selection]);       
            if (!empty($modelsCaixaTransacao)) {
                foreach ($modelsCaixaTransacao as $key => $modelCaixaTransacao) {
                    $modelCaixaTransacao->status = CaixaTransacao::STATUS_CKECKED;
                    $modelCaixaTransacao->save();
                }
                $model->status = Caixa::CLOSE_CAIXA;
                $model->user_close_id = Yii::$app->user->identity->id;
                $model->data_fecho = new \yii\db\Expression('NOW()');
                if(!empty($caixaAntes)){
                    $model->saldo_inicial = $caixaAntes->saldo_fecho;
                }
                $model->saldo_fecho =Yii::$app->FinCaixa->caixaSaldoFecho($model->id);
                if($model->save()){
                    $bancoConta->saldo = $model->saldo_fecho;
                    $bancoConta->save();
                    Yii::$app->getSession()->setFlash('success', 'Caixa Fechado com Sucesso.');
                }
            }
        }else{
            Yii::$app->getSession()->setFlash('warning', 'Não foi selecionado todas a transação deste caixa.');

        }
      return $this->redirect(Yii::$app->request->referrer);

    }



    /**
     * Deletes an existing Caixa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateSaldo($id)
    {
        $model = $this->findModel($id);
        $caixaAntes = Caixa::find()
                    ->where(['fin_banco_conta_id'=>$model->fin_banco_conta_id])
                    ->andWhere(['<','id',$model->id])
                    ->orderBy('id desc')
                    ->one();
        if(!empty($caixaAntes)){
            $model->saldo_inicial = $caixaAntes->saldo_fecho;
        }
        if($model->save()){
            Yii::$app->getSession()->setFlash('success', 'Saldo atualizado com Sucesso.');
        }else{
            Yii::$app->getSession()->setFlash('warning', 'Erro ao efetuar a operação.');
        }
      return $this->redirect(Yii::$app->request->referrer);

    }


    /**
     * Finds the Caixa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Caixa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Caixa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
