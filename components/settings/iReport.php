<?php

namespace app\components\settings;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use PHPJasper\PHPJasper;

class iReport extends Component
{

    public function generate(array $param)
    {

        // $input = Url::to('@app/modules/cnt/reports') . '/' . $param['name'] . '.jrxml';
        // $jasper = new PHPJasper;
        // $jasper->compile($input)->output();
        // print_r($jasper);die();


        $inputJasper = Url::to('@app/modules/cnt/reports') . '/' . $param['name'] . '.jasper';
        // print_r($inputJasper);die();
        $output = Url::to('@app/web/outputs/PHPJasper'); //C:\xampp\htdocs\pr_despacho\web\outputs\PHPJasper
        //$output = Url::to('@app/web/outputs/PHPJasper'); //C:\xampp\htdocs\pr_despacho\web\outputs\PHPJasper
        $jdbc_dir = Url::to('@vendor/geekcom/phpjasper/bin/jasperstarter/jdbc');
        $options = [
            'format' => [$param['format']],
            'locale' => 'pt_BR',
            'params' => $param['parametre'],
            'db_connection' => [
                'driver' => 'mysql',
                'host' => '192.168.1.175',
                'database' => 'pr_despachos',
                'username' => 'mc',
                'password' => "Prd3bd2022",
                'port' => '3306',
                'jdbc_driver' => 'com.mysql.cj.jdbc.Driver',
                'jdbc_dir' => $jdbc_dir,
            ]
        ];

        $jasper = new PHPJasper;

        $jasper->process(
            $inputJasper,
            $output,
            $options
        )->execute();
        // print_r($jasper);die();
        $this->redirect(Url::to('@web/output/PHPJasper') . $param['name'] . $param['format'])->send();
        //print_r($jasper);
        //die();

        return $output . '/' . $param['name'] . '.' . $param['format'];
    }

    public function display($content = null)
    {
        if ($content != null) {
            $this->content = $content;
        }
    }
}
