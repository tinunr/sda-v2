<anexo_for xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://nosiapps.gov.cv/grexsd/2014/mod106 anexo_for.xsd">
  <linhas>

<?php foreach($dataProvider->getModels() as $model):
  $origem = Yii::$app->CntQuery->origemData($model['cnt_razao_id'])?>
<linha origem="<?=$origem->origem?>" nif="<?=$origem->nif?>" designacao="<?=$origem->designacao?>" tp_doc="<?=$origem->tp_doc?>" num_doc="<?=$origem->num_doc?>" data="<?=$origem->data?>" vl_fatura="<?=$model['vl_fatura']?>" vl_base_incid="<?=$model['vl_base_incid']?>" tx_iva="<?=$model['tx_iva']?>" iva_sup="<?=$model['iva_liq']?>" direito_ded="<?=$model['nao_liq_imp']?>" iva_ded="<?=$model['linha_dest_mod']?>" tipologia="SRV" linha_dest_mod="<?=$model['linha_dest_mod']?>" />
<?php endforeach;?>

</linhas>

<dt_entrega>2019-01-07</dt_entrega>
<total_fatura>274107</total_fatura>
<total_base_incid>237892</total_base_incid>
<total_suportado>35682</total_suportado>
<total_dedutivel>35682</total_dedutivel>
</anexo_for>
