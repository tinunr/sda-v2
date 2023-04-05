<?php 
use app\modules\dsp\models\ProcessoStatus;



foreach($users as $user):?>
<p style="padding: 5px; border-bottom: 1px solid #ddd; font-size: 12px; margin-top: 25px; text-transform: uppercase;font-weight: 700; color: #444444;"><?=!empty($user['name'])?$user['name']:'Não Atríbuido'?></p>
<table style=" border-collapse: collapse;width: 100%;">
<?php 
$provider = \app\modules\dsp\services\DspService::processPdfReport($user['id'],$status,$dsp_person_id,$bas_ano_id);
?>
<tr>
	<th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Nº Pro.</th>
	<th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Estado</th>
	<th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Obs</th>
	<th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Cliente</th>
	<th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Mercadoria</th>
	<th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Nº PL</th>
	<th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Data Regl.</th>
</tr>
<?php foreach($provider->getModels() as $model):?>
  <tr>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;"><?=$model->numero?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;"><?=$model->processoStatus->descricao?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;"><?=($model->status ==ProcessoStatus::PENDENTE)?\app\modules\dsp\services\DspService::processoObsPendenstes($model->id):''?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;"><?=$model->person->nome?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;"><?=$model->descricao?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;"><?=!empty($model->pedidoLevantamento->id)?$model->pedidoLevantamento->id:''?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;"><?php
    if (!empty($model->pedidoLevantamento->data_proragacao)) {                  
      echo Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($model->pedidoLevantamento->data_proragacao,'php:d/m/Y')); 
    }elseif (!empty($model->pedidoLevantamento->data_regularizacao)) {
      echo Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($model->pedidoLevantamento->data_regularizacao,'php:d/m/Y'));
    } 
    ?></td>

  </tr>
  <tr>
<?php endforeach;?>

</table>
<?php endforeach;?>