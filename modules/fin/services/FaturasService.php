<?php

namespace app\modules\fin\services;


use yii;
use yii\base\Component;


use app\modules\fin\models\FaturaProvisoria;
use app\modules\fin\models\FaturaDefinitiva;
use app\modules\fin\models\FaturaProforma;

/**
 * 
 */
class FaturasService extends Component
{

    /**
     * Vereficar se a despesa tem fatura definitiva
     * @var integer
     */
    public static function countUnsendFaturas()
    {
        $fp = FaturaProvisoria::find()->where(['status' => 1, 'send' => 0])->count();
        $fpb = FaturaProvisoria::find()->where(['status' => 1, 'send' => 1])->count();
        $fd = FaturaDefinitiva::find()->where(['status' => 1, 'send' => 0])->count();
        $fdb = FaturaDefinitiva::find()->where(['status' => 1, 'send' => 1])->count();
        $faturaProformaSend = FaturaProforma::find()->where(['status' => 1, 'send' => 1])->count();
        $faturaProformaUnSend = FaturaProforma::find()->where(['status' => 1, 'send' => 0])->count();

        return [
            'faturaProformaSend' => $faturaProformaSend,
            'faturaProformaUnSend' => $faturaProformaUnSend,
            'fp' => $fp,
            'fpb' => $fpb,
            'fd' => $fd,
            'fdb' => $fdb,
            'total' => ($fp + $fd + $faturaProformaUnSend),
            'totalb' => ($fpb +  $fdb + $faturaProformaSend),
        ];
    }
}
