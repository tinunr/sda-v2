<?php 
use app\modules\dsp\models\ProcessoStatus;
$provider = \app\modules\dsp\services\DspService::processPdfAll($user_id,$status,$dsp_person_id,$bas_ano_id,$dataInicio,$dataFim,$comPl);
?>
<table>
<tr>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Nº Pro.</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">NORD</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Estado</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Cliente</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Mercadoria</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Obs</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Nº PL</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Data Regul. PL</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Data Liq.</th>
  <th style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd; font-size: 11px;">Dias</th>

</tr>
<?php foreach($provider->getModels() as $model):?>
  <tr>
    <td style="padding: 4px; text-align: left;border-bottom: 1px solid #ddd; font-size: 12px;"><?=$model->numero.'/'.$model->bas_ano_id?></td>
    <td style="padding: 4px; text-align: left;border-bottom: 1px solid #ddd; font-size: 12px;"><?=empty($model->nord->id)?'':$model->nord->id?></td>
    <td style="padding: 4px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 12px;"><?=$model->processoStatus->descricao?></td>
    <td style="padding: 4px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 12px;"><?=$model->person->nome?></td>

    <td style="padding: 4px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 12px;"><?=$model->descricao?></td>
    <td style="padding: 4px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 12px;"><?=($model->status ==ProcessoStatus::PENDENTE)?\app\modules\dsp\services\DspService::processoObsPendenstes($model->id):''?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;"><?=$model->n_levantamento?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;"><?php if (!empty($model->pedidoLevantamento->data_proragacao)) {
               echo $model->pedidoLevantamento->data_proragacao;
            }elseif (!empty($model->pedidoLevantamento->data_regularizacao)) {
                echo $model->pedidoLevantamento->data_regularizacao; 
            }  ?></td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px; width: 50px"><?=!empty($model->nord->despacho->data_liquidacao)?$model->nord->despacho->data_liquidacao:''?>
    </td>
    <td style="padding: 2px; text-align: left;border-bottom: 1px solid #ddd;  font-size: 11px;">
    <?php $result =''; $result2 = ''; ?>
    <?php
      if(!empty($model->pedidoLevantamento->data_proragacao)){
        $result = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($model->pedidoLevantamento->data_proragacao,'php:d/m/Y'));
      }else{
        if(!empty($model->pedidoLevantamento->data_regularizacao)){
        $result = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($model->pedidoLevantamento->data_regularizacao,'php:d/m/Y'));
      }
      }
      if(!empty($model->nord->despacho->data_liquidacao)){
        $result2 = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($model->nord->despacho->data_liquidacao,'php:d/m/Y'));
      }
      if (!empty($result)||!empty($result2)) {
        echo (!empty($result)?'PL: '.$result.'</br>':'').(!empty($result2)?' LIQ: '.$result2:'');
      }else{
      echo 'ESC: '.Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($model->data,'php:d/m/Y'));
      }
    ?>
    </td>


  </tr>
<?php endforeach;?>

</table>