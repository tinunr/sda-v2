<?php
namespace app\modules\dsp\behaviors;

use yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\fin\models\FaturaProvisoria;

class NordBehavior extends Behavior
{

   public function events()
   {
       return [
           ActiveRecord::EVENT_AFTER_INSERT => 'update_fatura_nord',
           ActiveRecord::EVENT_AFTER_UPDATE => 'update_fatura_nord',
       ];
   }

   public function update_fatura_nord(Event $after_insert)
   {
    $faturaProvisoria = FaturaProvisoria::find()->where(['dsp_processo_id'=>$this->owner->dsp_processo_id])->all();
    if(!empty($faturaProvisoria)){
        foreach ($faturaProvisoria as $key => $value) {
            $value->nord = $this->owner->id;
            $value->save();
        }

    }

        // gerar pasta automatico
        $pacth = Yii::getAlias('@nords');
        $ano = \app\models\Ano::findOne($this->owner->bas_ano_id);        
        $desembarco = \app\modules\dsp\models\Desembaraco::findOne($this->owner->dsp_desembaraco_id);        
        $pacthOne = $pacth . '/' . $ano->ano;
        if (!is_dir($pacthOne)) {
            mkdir($pacthOne);
        }
        $pacthTwo = $pacthOne . '/' . $desembarco->code;
        if (!is_dir($pacthTwo)) {
            mkdir($pacthTwo);
        }


  }


}