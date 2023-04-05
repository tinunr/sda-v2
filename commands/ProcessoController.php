<?php
namespace app\commands;

use yii\console\Controller;
use fedemotta\cronjob\models\CronJob;
use app\modules\dsp\models\Processo;
use app\modules\dsp\models\ProcessoObs;

use Yii;



/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ProcessoController extends Controller
{
    /**
     * Run SomeModel::some_method for a period of time
     * @param string $from
     * @param string $to
     * @return int exit code
     */
    public function actionInit($from, $to){
        $dates  = CronJob::getDateRange($from, $to);
        $command = CronJob::run($this->id, $this->action->id, 0, CronJob::countDateRange($dates));
        if ($command === false){
            return Controller::EXIT_CODE_ERROR;
        }else{
            foreach ($dates as $date) {
            $models = Processo::find()
                   ->where(['in', 'status', [1,2,3,4]])
                   ->all();
            foreach ($models as $key => $model) {
            	if (empty($model->user_id)) {
		           $model->status = 1;
		        }else {
		            $model->status = 2;
		             if (!empty($model->nord->despacho->id)) {
		                $model->status = 4;
		            }
		            if (!empty($model->nord->despacho->numero_liquidade)) {
		                $model->status = 5;
		            }
		            if (!empty($model->nord->despacho->n_receita)) {
		                $model->status = 6;
		            }
		            $obs = ProcessoObs::find()->where(['dsp_processo_id'=>$model->id,'status'=>0])->count();
		            if ($obs>0) {
		               $model->status = 3;
		            }

		        }
		        if(!$model->save()){
		        	echo "error";
		        }
            }
        }
            $command->finish();
            return Controller::EXIT_CODE_NORMAL;
        }
    }

    /**
     * Run SomeModel::some_method for today only as the default action
     * @return int exit code
     */
    public function actionUpdateStatus(){
        return $this->actionInit(date("Y-m-d"), date("Y-m-d"));
    }
}
