<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;


use yii\console\Controller;




/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SettingController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($bas_ano_id)
    {
        //comand ./yii hello/index
        $ddd = \app\modules\dsp\models\Processo::find()->all();
        foreach($ddd as $key => $dd){
        echo $key.' Init processo numero '.$dd->numero.'/'.$dd->bas_ano_id.PHP_EOL;
        // \app\modules\dsp\services\ProcessoService::setStatusOperacional($dd->id,$dd->status);
        \app\modules\dsp\services\ProcessoService::setStatusFinanceiro($dd->id);
        echo $key.' Done processo numero '.$dd->numero.'/'.$dd->bas_ano_id. PHP_EOL;

        }
        echo 'All Done.';
    }


    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionSteImgBingo()
    {
        $bing = new \app\components\helpers\BingPhoto();  
        echo 'done!';
    }
}