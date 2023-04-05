<?php
$formatter = Yii::$app->formatter;
use app\modules\dsp\models\ProcessoStatus;
$total = 0;
?>
<?php foreach ($person->getModels() as $key => $person) :?>
<div class="titulo-principal"> <h5 class="titulo"><?=$person['nome']?></h5><div id="linha-longa"><div id="linha-curta"></div></div> </div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Nº PR</th>
            <th>NORD</th>
            <th>Estado</th>
            <th>Mercadoria</th>
            <th>OBS</th>
            <th>PL</th>
            <th>Data Regul. PL</th>
            <th>Data Liq.</th>
            <th>Dias</th>
        </tr>
    </thead>
    <tbody>
       <?php 
            $data['ProcessoSearch']['dsp_person_id'] = $person['id'];
            $searchModel = new app\modules\dsp\models\ProcessoSearch($data['ProcessoSearch']);
            // print_r($data['ProcessoSearch']);die();
            $dataProvider = $searchModel->processo(Yii::$app->request->queryParams);
            ?>
            <?php foreach ($dataProvider->getModels() as $key => $value):?>
        <tr>
            <td><?=$value->numero.'/'.$value->bas_ano_id?></td>
            <td><?=empty($value->nord->id)?'':$value->nord->id?></td>
            <td><?=$value->processoStatus->descricao?></td>
            <td><?=$value->descricao?></td>
            <td><?=($value->status ==ProcessoStatus::PENDENTE)?\app\modules\dsp\services\DspService::processoObsPendenstes($value->id):''?></td>
            <td><?=$value->n_levantamento?></td>
            <td>
                <?php if (!empty($value->pedidoLevantamento->data_proragacao)):?>
                   <?=$formatter->asDate($value->pedidoLevantamento->data_proragacao)?>
                <?php elseif (!empty($value->pedidoLevantamento->data_regularizacao)):?>
                    <?=$formatter->asDate($value->pedidoLevantamento->data_regularizacao)?>
                <?php endif;?>
            </td>
            <td><?=!empty($value->nord->despacho->data_liquidacao)?$value->nord->despacho->data_liquidacao:''?></td>
            <td> <?php $result =''; $result2 = ''; ?>
            <?php
              if(!empty($value->pedidoLevantamento->data_proragacao)){
                $result = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($value->pedidoLevantamento->data_proragacao,'php:d/m/Y'));
              }else{
                if(!empty($value->pedidoLevantamento->data_regularizacao)){
                $result = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($value->pedidoLevantamento->data_regularizacao,'php:d/m/Y'));
              }
              }
              if(!empty($value->nord->despacho->data_liquidacao)){
                $result2 = Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($value->nord->despacho->data_liquidacao,'php:d/m/Y'));
              }
              if (!empty($result)||!empty($result2)) {
                echo (!empty($result)?'PL: '.$result.'</br>':'').(!empty($result2)?' LIQ: '.$result2:'');
              }else{
              echo 'ESC: '.Yii::$app->DateHelper->DiasUteis((new \DateTime())->format('d/m/Y'),\Yii::$app->formatter->asDate($value->data,'php:d/m/Y'));
              }
            ?>
                
            </td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
<?php endforeach;?>


