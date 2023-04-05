<?php 
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Ficheiro;
use yii\web\UploadedFile;
use yii\helpers\Url;
use app\modules\dsp\models\ProcessoDespachoDocumento;
use app\modules\dsp\models\ProcessoHistorico;
use app\modules\dsp\models\DespachoDocumento;

class FicheiroController extends Controller
{
    public function beforeAction($action) 
{ 
    $this->enableCsrfValidation = false; 
    return parent::beforeAction($action); 
}

    public function actionAddProcessoDespachoDocumento($dsp_processo_id)
    {
        // print_r($modelProcessoDespachoDocumento);die();
        $modelProcessoDespachoDocumento = new ProcessoDespachoDocumento();        
        $model = new Ficheiro();
        
        if ($modelProcessoDespachoDocumento->load(Yii::$app->request->post())) {
            $modelProcessoDespachoDocumento->descricao = DespachoDocumento::findOne($modelProcessoDespachoDocumento->dsp_despacho_documento_id)->descricao;
            $model->file = UploadedFile::getInstance($modelProcessoDespachoDocumento, 'file');
            $model->name = $modelProcessoDespachoDocumento->descricao;
            $model->dsp_processo_id = $dsp_processo_id;
            $transaction = \Yii::$app->db->beginTransaction();
            if($bas_ficheiro_id=$model->upload()){
            $modelProcessoDespachoDocumento->bas_ficheiro_id = $bas_ficheiro_id;
            $modelProcessoDespachoDocumento->dsp_processo_id = $dsp_processo_id;
            if (! ($flag = $modelProcessoDespachoDocumento->save())) {
                $transaction->rollBack();
            }
            $transaction->commit();

            Yii::$app->getSession()->setFlash('success', 'Fichero anexado com sucesso');
                    return $this->redirect(Yii::$app->request->referrer);
            } else{
                Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
            return $this->redirect(Yii::$app->request->referrer);
        }   
            
        }
        Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionSetProcessoDespachoDocumento($id)
    {
        // print_r($modelProcessoDespachoDocumento);die();
        $modelProcessoDespachoDocumento =  ProcessoDespachoDocumento::findOne($id);
        $model = new Ficheiro();
        $model->name = $modelProcessoDespachoDocumento->descricao;
        $model->dsp_processo_id = $modelProcessoDespachoDocumento->dsp_processo_id;
        if ($modelProcessoDespachoDocumento->load(Yii::$app->request->post())&&($model->file = UploadedFile::getInstance($modelProcessoDespachoDocumento, 'file'))) {
            $transaction = \Yii::$app->db->beginTransaction();
            if($bas_ficheiro_id=$model->upload()){
            $modelProcessoDespachoDocumento->bas_ficheiro_id = $bas_ficheiro_id;
            if (! ($flag = $modelProcessoDespachoDocumento->save())) {
                $transaction->rollBack();
            }
            $transaction->commit();

            Yii::$app->getSession()->setFlash('success', 'Fichero anexado com sucesso');
                    return $this->redirect(Yii::$app->request->referrer);
            } else{
                Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
            return $this->redirect(Yii::$app->request->referrer);
        }   
            
        }
        Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
        return $this->redirect(Yii::$app->request->referrer);
    }




public function actionUploadDocumentoProcesso($id)
    {
        $model = new Ficheiro();
        $modelProcessoDespachoDocumento = new ProcessoDespachoDocumento();
        if ($modelProcessoDespachoDocumento->load(Yii::$app->request->post())&&($model->file = UploadedFile::getInstance($modelProcessoDespachoDocumento, 'file'))) {
            $transaction = \Yii::$app->db->beginTransaction();
            if($bas_ficheiro_id=$model->upload()){
            $modelProcessoDespachoDocumento->bas_ficheiro_id = $bas_ficheiro_id;
            $modelProcessoDespachoDocumento->dsp_processo_id = $id;
            if (! ($flag = $modelProcessoDespachoDocumento->save())) {
                $transaction->rollBack();
            }
            $documentoProcesso = \app\modules\dsp\services\DspService::isCompleteProcessoDocument($id);
            
           $processoHistorico = new ProcessoHistorico();
           $processoHistorico->dsp_processo_id = $id;
           $processoHistorico->descricao = $documentoProcesso?'Ficheiro associado ao processo  de ID '.$id.' e documento processo concluído':'Ficheiro associado ao processo  de ID '.$id;
           if (! ($flag = $processoHistorico->save())) {
                $transaction->rollBack();
            }

            $transaction->commit();

            Yii::$app->getSession()->setFlash('success', 'Fichero anexado com sucesso');
                    return $this->redirect(Yii::$app->request->referrer);
            } else{
                Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
            return $this->redirect(Yii::$app->request->referrer);
        }   
            
        }
        Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
        return $this->redirect(Yii::$app->request->referrer);
    }
	
	
	
	
	public function actionUploadAnexo($id, $biblioteca_id)
    {
        $modelCatRegisto = CatRegisto::find()->where(['id'=>$id, 'biblioteca_id'=>$biblioteca_id])->one();
        $model = new Ficheiro();
        $modelCatRegistoAnexo = new CatRegistoAnexo();
        if ($modelCatRegistoAnexo->load(Yii::$app->request->post())&&($model->file = UploadedFile::getInstance($modelCatRegistoAnexo, 'file'))) {
            if($modelCatRegistoAnexo->ficheiro_id=$model->upload()){
            $modelCatRegistoAnexo->cat_n_exemplar =$modelCatRegisto->n_exemplar;
            $modelCatRegistoAnexo->save(false);
            Yii::$app->getSession()->setFlash('success', 'Fichero anexado com sucesso');
                    return $this->redirect(Yii::$app->request->referrer);
            } else{
                Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
        return $this->redirect(Yii::$app->request->referrer);
            }       
            /*if ($model->file) {
                $model->file_name = $model->file->name;
                $model->file_extenction = $model->file->extension;
                if($model->save()){
                    $model->file_pacth = Yii::$app->AuthService->baseUrlDoc().$model->id.'.'.$model->file->extension;
                    $model->save();

                    $model->file->saveAs(Yii::$app->AuthService->baseUrlDoc().$model->id.'.'.$model->file->extension);
                    Yii::$app->getSession()->setFlash('success', 'Fichero anexado com sucesso');
                    return $this->redirect(Yii::$app->request->referrer);
                }
                
            }*/
            
        }
        Yii::$app->getSession()->setFlash('error', 'Erro ao anexar o ficheiro');
        return $this->redirect(Yii::$app->request->referrer);
        #return $this->render('upload', ['model' => $model]);
    }
	
	
	
	


    public function actionDelete($file_id)
    {
        $ficheiro =Yii::$app->AuthService->getFile($file_id);
        unlink(Url::to($ficheiro->file_pacth));
        $ProtocoloFile =ProtocoloFile::find()->where(['file_id'=>$file_id])->one();
        $ProtocoloFile->delete();
        $ficheiro->delete();
        Yii::$app->getSession()->setFlash('success', 'icheiro eliminado com sucesso');
        return $this->redirect(Yii::$app->request->referrer);

    }
}